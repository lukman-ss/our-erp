<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseMaterial;
use App\Models\MaterialBatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    /**
     * DataTables server-side
     */
    public function datatable(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $query = Purchase::query()
                ->withCount('materials')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
                ->select([
                    'purchases.id',
                    'patches' => DB::raw('purchases.id'), // anti select overwrite by dt
                    'purchases.code',
                    'purchases.supplier_id',
                    'purchases.purchase_date',
                    'purchases.total_amount',
                    'purchases.status',
                    'purchases.created_at',
                    DB::raw('COALESCE(suppliers.name, "-") as supplier_name'),
                ])
                ->orderByDesc('purchases.purchase_date')
                ->orderByDesc('purchases.created_at');

            // DataTables
            $json = DataTables::eloquent($query)
                ->addIndexColumn()
                ->filter(function ($q) use ($request) {
                    $qParam   = trim((string) $request->input('q', ''));
                    $status   = trim((string) $request->input('status', ''));
                    $dateFrom = trim((string) $request->input('date_from', ''));
                    $dateTo   = trim((string) $request->input('date_to', ''));
                    $supplier = trim((string) $request->input('supplier_id', ''));

                    if ($qParam !== '') {
                        $q->where(function ($w) use ($qParam) {
                            $w->where('purchases.code', 'like', "%{$qParam}%")
                              ->orWhere('suppliers.name', 'like', "%{$qParam}%")
                              ->orWhere('purchases.status', 'like', "%{$qParam}%");
                        });
                    }
                    if ($status !== '') {
                        $q->where('purchases.status', $status);
                    }
                    if ($supplier !== '') {
                        $q->where('purchases.supplier_id', $supplier);
                    }
                    if ($dateFrom !== '') {
                        $q->whereDate('purchases.purchase_date', '>=', $dateFrom);
                    }
                    if ($dateTo !== '') {
                        $q->whereDate('purchases.purchase_date', '<=', $dateTo);
                    }
                })
                ->editColumn('total_amount', fn ($row) =>
                    number_format((float) $row->total_amount, 2, '.', '')
                )
                ->addColumn('items_count', fn ($row) =>
                    (int) ($row->materials_count ?? 0)
                )
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:show_view(\''.$row->id.'\')" title="View"><i class="fa fa-file-o text-success fa-lg mx-2"></i></a>';
                })
                ->rawColumns(['action'])
                ->toJson();

            DB::commit();
            return $json;
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Load datatable failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Detail purchase
     */
    public function show(string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $purchase = Purchase::with(['materials.material', 'materialBatches'])->findOrFail($id);

            DB::commit();
            return response()->json($purchase);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Load detail failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Create purchase + items + (optional) batches
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code'           => ['nullable','string','max:191','unique:purchases,code'],
            'supplier_id'    => ['nullable','uuid'],
            'purchase_date'  => ['required','date'],
            'status'         => ['nullable', Rule::in(['pending','confirmed','received','cancelled'])],
            'create_batches' => ['sometimes','boolean'],

            'items'               => ['required','array','min:1'],
            'items.*.material_id' => ['required','uuid'],
            'items.*.qty'         => ['required','numeric','gt:0'],
            'items.*.unit_cost'   => ['required','numeric','gte:0'],
            'items.*.total_cost'  => ['nullable','numeric','gte:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $v   = $validator->validated();
            $dt  = Carbon::parse($v['purchase_date']);
            $code = $v['code'] ?? $this->generateCode($dt);

            // safety: unique check again (race)
            if (Purchase::where('code', $code)->exists()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors'  => ['code' => ['The code has already been taken.']],
                ], 422);
            }

            $purchase = Purchase::create([
                'id'            => (string) Str::uuid(),
                'code'          => $code,
                'supplier_id'   => $v['supplier_id'] ?? null,
                'purchase_date' => $dt->toDateString(),
                'total_amount'  => 0,
                'status'        => $v['status'] ?? 'pending',
            ]);

            $totalAmount   = '0.00';
            $createBatches = (bool)($v['create_batches'] ?? true);

            foreach ($v['items'] as $row) {
                $qty       = (float) $row['qty'];
                $unitCost  = (float) $row['unit_cost'];
                $totalCost = array_key_exists('total_cost', $row)
                    ? (float) $row['total_cost']
                    : $qty * $unitCost;

                $pm = PurchaseMaterial::create([
                    'id'          => (string) Str::uuid(),
                    'purchase_id' => $purchase->id,
                    'material_id' => $row['material_id'],
                    'qty'         => $qty,
                    'unit_cost'   => $unitCost,
                    'total_cost'  => $totalCost,
                ]);

                if ($createBatches) {
                    MaterialBatch::create([
                        'id'                   => (string) Str::uuid(),
                        'material_id'          => $row['material_id'],
                        'qty_initial'          => $qty,
                        'qty_remaining'        => $qty,
                        'unit_cost'            => $unitCost,
                        'received_at'          => $dt->copy(),
                        'purchase_id'          => $purchase->id,
                        'purchase_material_id' => $pm->id,
                    ]);
                }

                $totalAmount = bcadd($totalAmount, number_format($totalCost, 2, '.', ''), 2);
            }

            $purchase->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'message'  => 'Created',
                'purchase' => $purchase->load(['materials.material', 'materialBatches']),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Create failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Update header + (optional) reset items/batches
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $purchase = Purchase::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code'           => ['sometimes','nullable','string','max:191', Rule::unique('purchases','code')->ignore($purchase->id)],
            'supplier_id'    => ['sometimes','nullable','uuid'],
            'purchase_date'  => ['sometimes','required','date'],
            'status'         => ['sometimes','nullable', Rule::in(['pending','confirmed','received','cancelled'])],
            'create_batches' => ['sometimes','boolean'],

            'items'               => ['sometimes','array'],
            'items.*.material_id' => ['required_with:items','uuid'],
            'items.*.qty'         => ['required_with:items','numeric','gt:0'],
            'items.*.unit_cost'   => ['required_with:items','numeric','gte:0'],
            'items.*.total_cost'  => ['nullable','numeric','gte:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $v = $validator->validated();

            // header
            if (array_key_exists('code', $v)) {
                $purchase->code = $v['code'];
            }
            if (array_key_exists('supplier_id', $v)) {
                $purchase->supplier_id = $v['supplier_id'];
            }
            if (array_key_exists('purchase_date', $v)) {
                $purchase->purchase_date = Carbon::parse($v['purchase_date'])->toDateString();
            }
            if (array_key_exists('status', $v)) {
                $purchase->status = $v['status'];
            }

            $purchase->save();

            // items & batches reset (jika items dikirim)
            if (array_key_exists('items', $v)) {
                $receivedAt     = Carbon::parse($purchase->purchase_date);
                $createBatches  = (bool)($v['create_batches'] ?? true);

                // hapus existing
                MaterialBatch::where('purchase_id', $purchase->id)->delete();
                PurchaseMaterial::where('purchase_id', $purchase->id)->delete();

                $totalAmount = '0.00';

                foreach ($v['items'] as $row) {
                    $qty       = (float) $row['qty'];
                    $unitCost  = (float) $row['unit_cost'];
                    $totalCost = array_key_exists('total_cost', $row)
                        ? (float) $row['total_cost']
                        : $qty * $unitCost;

                    $pm = PurchaseMaterial::create([
                        'id'          => (string) Str::uuid(),
                        'purchase_id' => $purchase->id,
                        'material_id' => $row['material_id'],
                        'qty'         => $qty,
                        'unit_cost'   => $unitCost,
                        'total_cost'  => $totalCost,
                    ]);

                    if ($createBatches) {
                        MaterialBatch::create([
                            'id'                   => (string) Str::uuid(),
                            'material_id'          => $row['material_id'],
                            'qty_initial'          => $qty,
                            'qty_remaining'        => $qty,
                            'unit_cost'            => $unitCost,
                            'received_at'          => $receivedAt->copy(),
                            'purchase_id'          => $purchase->id,
                            'purchase_material_id' => $pm->id,
                        ]);
                    }

                    $totalAmount = bcadd($totalAmount, number_format($totalCost, 2, '.', ''), 2);
                }

                $purchase->update(['total_amount' => $totalAmount]);
            }

            DB::commit();

            return response()->json([
                'message'  => 'Updated',
                'purchase' => $purchase->load(['materials.material', 'materialBatches']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Update failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Delete purchase (cascade items; batches removed explicitly)
     */
    public function destroy(string $id): JsonResponse
    {
        $purchase = Purchase::findOrFail($id);

        try {
            DB::beginTransaction();

            MaterialBatch::where('purchase_id', $purchase->id)->delete();
            $purchase->delete();

            DB::commit();
            return response()->json(['message' => 'Deleted']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Delete failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ================== helpers ==================
    private function generateCode(Carbon $date): string
    {
        $prefix = 'PO-' . $date->format('Ymd');
        $count  = Purchase::whereDate('purchase_date', $date->toDateString())->count();
        $seq    = str_pad((string) ($count + 1), 5, '0', STR_PAD_LEFT);
        return "{$prefix}-{$seq}";
    }
}
