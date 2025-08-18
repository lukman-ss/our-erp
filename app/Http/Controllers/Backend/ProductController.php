<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function datatable(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $query = Product::query()->orderByDesc('created_at');

            $json = DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('action', fn($row) =>
                    '<a href="javascript:show_view(\''.$row->id.'\')" title="View"><i class="fa fa-file-o text-success fa-lg mx-2"></i></a>'
                )
                ->rawColumns(['action'])
                ->toJson();

            DB::commit();
            return $json;
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message'=>'Load datatable failed','error'=>config('app.debug')?$e->getMessage():null], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::with(['materials' => function ($q) {
            $q->select(
                'materials.id',
                'materials.code',
                'materials.name',
                'materials.unit',
                'materials.cost_price as material_cost_price' // avoid ambiguity
            );
        }])->findOrFail($id);

        return response()->json([
            'product'   => $product->only(['id','sku','name','unit','sell_price','stock','description','created_at','updated_at']),
            'materials' => $product->materials->map(fn($m) => [
                'id'                   => $m->id,
                'code'                 => $m->code,
                'name'                 => $m->name,
                'unit'                 => $m->unit,
                'material_cost_price'  => $m->material_cost_price,
                'qty'                  => $m->pivot->qty,
                'pivot_cost_price'     => $m->pivot->cost_price ?? null,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sku'         => ['required','string','max:100','unique:products,sku'],
            'name'        => ['required','string','max:255'],
            'unit'        => ['nullable','string','max:20'],
            'sell_price'  => ['nullable','numeric'],
            'stock'       => ['nullable','numeric'],
            'description' => ['nullable','string'],

            'items'                       => ['required','array','min:1'],
            'items.*.material_id'         => ['required','exists:materials,id'],
            'items.*.qty'                 => ['required','numeric','min:0.0001'],
            'items.*.cost_price'          => ['nullable','numeric'], // optional pivot cost
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>'Validation failed','errors'=>$validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $data = collect($validator->validated());
            $productFields = $data->only(['sku','name','unit','sell_price','stock','description'])->all();
            $items = $data->get('items', []);

            $product = Product::create($productFields);

            $pivot = [];
            foreach ($items as $it) {
                $pivot[$it['material_id']] = [
                    'id'         => (string) Str::uuid(),
                    'qty'        => $it['qty'],
                    'cost_price' => $it['cost_price'] ?? null,
                ];
            }
            $product->materials()->sync($pivot);

            DB::commit();

            $product->load(['materials' => function ($q) {
                $q->select(
                    'materials.id',
                    'materials.code',
                    'materials.name',
                    'materials.unit',
                    'materials.cost_price as material_cost_price'
                );
            }]);

            return response()->json([
                'message' => 'Created',
                'product' => $product->only(['id','sku','name','unit','sell_price','stock','description','created_at','updated_at']),
                'materials' => $product->materials->map(fn($m) => [
                    'id'                   => $m->id,
                    'code'                 => $m->code,
                    'name'                 => $m->name,
                    'unit'                 => $m->unit,
                    'material_cost_price'  => $m->material_cost_price,
                    'qty'                  => $m->pivot->qty,
                    'pivot_cost_price'     => $m->pivot->cost_price ?? null,
                ]),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message'=>'Create failed','error'=>config('app.debug')?$e->getMessage():null], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'sku'         => ['sometimes','required','string','max:100', Rule::unique('products','sku')->ignore($product->id)],
            'name'        => ['sometimes','required','string','max:255'],
            'unit'        => ['nullable','string','max:20'],
            'sell_price'  => ['nullable','numeric'],
            'stock'       => ['nullable','numeric'],
            'description' => ['nullable','string'],

            'items'                       => ['sometimes','array','min:1'],
            'items.*.material_id'         => ['required_with:items','exists:materials,id'],
            'items.*.qty'                 => ['required_with:items','numeric','min:0.0001'],
            'items.*.cost_price'          => ['nullable','numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>'Validation failed','errors'=>$validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $data = collect($validator->validated());

            if ($fields = $data->only(['sku','name','unit','sell_price','stock','description'])->filter()->all()) {
                $product->fill($fields)->save();
            }

            if ($data->has('items')) {
                $pivot = [];
                foreach ($data->get('items') as $it) {
                    $pivot[$it['material_id']] = [
                        'id'         => (string) Str::uuid(),
                        'qty'        => $it['qty'],
                        'cost_price' => $it['cost_price'] ?? null,
                    ];
                }
                $product->materials()->sync($pivot);
            }

            DB::commit();

            $product->load(['materials' => function ($q) {
                $q->select(
                    'materials.id',
                    'materials.code',
                    'materials.name',
                    'materials.unit',
                    'materials.cost_price as material_cost_price'
                );
            }]);

            return response()->json([
                'message' => 'Updated',
                'product' => $product->only(['id','sku','name','unit','sell_price','stock','description','created_at','updated_at']),
                'materials' => $product->materials->map(fn($m) => [
                    'id'                   => $m->id,
                    'code'                 => $m->code,
                    'name'                 => $m->name,
                    'unit'                 => $m->unit,
                    'material_cost_price'  => $m->material_cost_price,
                    'qty'                  => $m->pivot->qty,
                    'pivot_cost_price'     => $m->pivot->cost_price ?? null,
                ]),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message'=>'Update failed','error'=>config('app.debug')?$e->getMessage():null], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        try {
            DB::beginTransaction();
            $product->delete();
            DB::commit();
            return response()->json(['message' => 'Deleted']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message'=>'Delete failed','error'=>config('app.debug')?$e->getMessage():null], 500);
        }
    }
}
