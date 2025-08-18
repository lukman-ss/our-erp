<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleProduct;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function datatable(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $query = Sale::query()
                ->withCount('items')
                ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
                ->select([
                    'sales.id','sales.code','sales.customer_id','sales.sale_date',
                    'sales.total_amount','sales.status','sales.created_at',
                    DB::raw('COALESCE(customers.name, "-") as customer_name')
                ])
                ->orderByDesc('sales.sale_date')
                ->orderByDesc('sales.created_at');

            $json = DataTables::eloquent($query)
                ->addIndexColumn()
                ->filter(function ($q) use ($request) {
                    $search = trim((string) $request->input('q',''));
                    $status = trim((string) $request->input('status',''));
                    $from   = trim((string) $request->input('date_from',''));
                    $to     = trim((string) $request->input('date_to',''));
                    $cust   = trim((string) $request->input('customer_id',''));

                    if ($search !== '') {
                        $q->where(function($w) use ($search){
                            $w->where('sales.code','like',"%{$search}%")
                              ->orWhere('customers.name','like',"%{$search}%")
                              ->orWhere('sales.status','like',"%{$search}%");
                        });
                    }
                    if ($status !== '') $q->where('sales.status',$status);
                    if ($cust   !== '') $q->where('sales.customer_id',$cust);
                    if ($from   !== '') $q->whereDate('sales.sale_date','>=',$from);
                    if ($to     !== '') $q->whereDate('sales.sale_date','<=',$to);
                })
                ->editColumn('total_amount', fn($r)=>number_format((float)$r->total_amount,2,'.',''))
                ->addColumn('items_count', fn($r)=>(int)($r->items_count ?? 0))
                ->addColumn('action', fn($r)=>'<a href="javascript:show_view(\''.$r->id.'\')" title="View"><i class="fa fa-file-o text-success fa-lg mx-2"></i></a>')
                ->rawColumns(['action'])
                ->toJson();

            DB::commit();
            return $json;
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message'=>'Load datatable failed','error'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $sale = Sale::with(['items.product'])->findOrFail($id);
            DB::commit();
            return response()->json($sale);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message'=>'Load detail failed','error'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code'         => ['nullable','string','max:191','unique:sales,code'],
            'customer_id'  => ['nullable','string','size:36'],
            'sale_date'    => ['required','date'],
            'status'       => ['nullable', Rule::in(['draft','confirmed','paid','cancelled'])],
            'affect_stock' => ['sometimes','boolean'],

            'items'                        => ['required','array','min:1'],
            'items.*.product_id'           => ['required_with:items','string','size:36','exists:products,id'],
            'items.*.qty'                  => ['required_with:items','numeric','gt:0'],
            'items.*.unit_price'           => ['required_with:items','numeric','gte:0'],
            'items.*.discount_percentage'  => ['nullable','numeric','gte:0'], // %
            'items.*.discount_amount'      => ['nullable','numeric','gte:0'], // rupiah
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>'Validation failed','errors'=>$validator->errors()],422);
        }

        try {
            DB::beginTransaction();

            $v        = $validator->validated();
            $saleDate = Carbon::parse($v['sale_date']);
            $code     = $v['code'] ?? $this->generateCode($saleDate);

            if (Sale::where('code',$code)->exists()) {
                return response()->json([
                    'message'=>'Validation failed',
                    'errors'=>['code'=>['The code has already been taken.']]
                ],422);
            }

            $sale = Sale::create([
                'id'           => (string) Str::uuid(),
                'code'         => $code,
                'customer_id'  => $v['customer_id'] ?? null,
                'sale_date'    => $saleDate->toDateString(),
                'total_amount' => 0,
                'status'       => $v['status'] ?? 'draft',
            ]);

            $affectStock = (bool)($v['affect_stock'] ?? true);
            $grandTotal  = '0.00';

            foreach ($v['items'] as $it) {
                $qty        = (float) $it['qty'];
                $unitPrice  = (float) $it['unit_price'];
                $dp         = isset($it['discount_percentage']) ? (float)$it['discount_percentage'] : 0.0;
                $da         = isset($it['discount_amount']) ? (float)$it['discount_amount'] : 0.0;

                $gross         = $qty * $unitPrice;
                $percentCut    = $gross * ($dp / 100);
                $nominalCut    = $da;
                $lineTotalCalc = max($gross - $percentCut - $nominalCut, 0);

                SaleProduct::create([
                    'id'                  => (string) Str::uuid(),
                    'sale_id'             => $sale->id,
                    'product_id'          => $it['product_id'],
                    'qty'                 => $qty,
                    'unit_price'          => $unitPrice,
                    'discount_percentage' => $dp,
                    'discount_amount'     => $da,
                    'line_total'          => $lineTotalCalc,
                ]);

                if ($affectStock) {
                    Product::where('id', $it['product_id'])->decrement('stock', $qty);
                }

                $grandTotal = bcadd($grandTotal, number_format($lineTotalCalc, 2, '.', ''), 2);
            }

            $sale->update(['total_amount' => $grandTotal]);

            DB::commit();
            return response()->json([
                'message' => 'Created',
                'sale'    => $sale->load('items.product'),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message'=>'Create failed','error'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $sale = Sale::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code'         => ['sometimes','nullable','string','max:191', Rule::unique('sales','code')->ignore($sale->id)],
            'customer_id'  => ['sometimes','nullable','string','size:36'],
            'sale_date'    => ['sometimes','required','date'],
            'status'       => ['sometimes','nullable', Rule::in(['draft','confirmed','paid','cancelled'])],
            'affect_stock' => ['sometimes','boolean'],

            'items'                        => ['sometimes','array'],
            'items.*.product_id'           => ['required_with:items','string','size:36','exists:products,id'],
            'items.*.qty'                  => ['required_with:items','numeric','gt:0'],
            'items.*.unit_price'           => ['required_with:items','numeric','gte:0'],
            'items.*.discount_percentage'  => ['nullable','numeric','gte:0'],
            'items.*.discount_amount'      => ['nullable','numeric','gte:0'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>'Validation failed','errors'=>$validator->errors()],422);
        }

        try {
            DB::beginTransaction();

            $v = $validator->validated();

            if (array_key_exists('code',$v))        $sale->code = $v['code'];
            if (array_key_exists('customer_id',$v)) $sale->customer_id = $v['customer_id'];
            if (array_key_exists('sale_date',$v))   $sale->sale_date = Carbon::parse($v['sale_date'])->toDateString();
            if (array_key_exists('status',$v))      $sale->status = $v['status'];
            $sale->save();

            if (array_key_exists('items',$v)) {
                $affectStock = (bool)($v['affect_stock'] ?? true);

                // Restore old stock if affecting stock
                if ($affectStock) {
                    $oldItems = SaleProduct::where('sale_id', $sale->id)->get();
                    foreach ($oldItems as $o) {
                        Product::where('id', $o->product_id)->increment('stock', (float)$o->qty);
                    }
                }

                // Remove old lines
                SaleProduct::where('sale_id',$sale->id)->delete();

                // Insert new lines
                $grandTotal = '0.00';
                foreach ($v['items'] as $it) {
                    $qty        = (float) $it['qty'];
                    $unitPrice  = (float) $it['unit_price'];
                    $dp         = isset($it['discount_percentage']) ? (float)$it['discount_percentage'] : 0.0;
                    $da         = isset($it['discount_amount']) ? (float)$it['discount_amount'] : 0.0;

                    $gross         = $qty * $unitPrice;
                    $percentCut    = $gross * ($dp / 100);
                    $nominalCut    = $da;
                    $lineTotalCalc = max($gross - $percentCut - $nominalCut, 0);

                    SaleProduct::create([
                        'id'                  => (string) Str::uuid(),
                        'sale_id'             => $sale->id,
                        'product_id'          => $it['product_id'],
                        'qty'                 => $qty,
                        'unit_price'          => $unitPrice,
                        'discount_percentage' => $dp,
                        'discount_amount'     => $da,
                        'line_total'          => $lineTotalCalc,
                    ]);

                    if ($affectStock) {
                        Product::where('id', $it['product_id'])->decrement('stock', $qty);
                    }

                    $grandTotal = bcadd($grandTotal, number_format($lineTotalCalc, 2, '.', ''), 2);
                }

                $sale->update(['total_amount' => $grandTotal]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Updated',
                'sale'    => $sale->load('items.product'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message'=>'Update failed','error'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $sale = Sale::findOrFail($id);

        try {
            DB::beginTransaction();

            // Restore stock before delete
            $items = SaleProduct::where('sale_id',$sale->id)->get();
            foreach ($items as $it) {
                Product::where('id', $it->product_id)->increment('stock', (float)$it->qty);
            }

            $sale->delete();

            DB::commit();
            return response()->json(['message'=>'Deleted']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message'=>'Delete failed','error'=>config('app.debug')?$e->getMessage():null],500);
        }
    }

    private function generateCode(Carbon $date): string
    {
        $prefix = 'SO-' . $date->format('Ymd');
        $count  = Sale::whereDate('sale_date', $date->toDateString())->count();
        $seq    = str_pad((string)($count + 1), 5, '0', STR_PAD_LEFT);
        return "{$prefix}-{$seq}";
    }
}
