<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Production;
use App\Models\ProductionMaterial;
use App\Models\ProductionProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductionController extends Controller
{
    /**
     * DataTables server-side for productions
     * GET /api/productions/datatable
     */
    public function datatable(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $query = Production::query()
                ->withCount(['materials as materials_count', 'products as products_count'])
                ->leftJoin('products', 'products.id', '=', 'productions.product_id')
                ->select([
                    'productions.id',
                    'productions.code',
                    'productions.product_id',
                    'productions.qty_planned',
                    'productions.qty_produced',
                    'productions.status',
                    'productions.scheduled_at',
                    'productions.started_at',
                    'productions.finished_at',
                    'productions.created_at',
                    DB::raw('COALESCE(products.name, "-") as product_name'),
                ])
                ->orderByDesc('productions.created_at');

            $json = DataTables::eloquent($query)
                ->addIndexColumn()
                ->filter(function ($q) use ($request) {
                    $search   = trim((string) $request->input('q', ''));
                    $status   = trim((string) $request->input('status', ''));
                    $fromDate = trim((string) $request->input('date_from', '')); // filter by scheduled_at/start/created? pakai scheduled_at
                    $toDate   = trim((string) $request->input('date_to', ''));
                    $product  = trim((string) $request->input('product_id', ''));

                    if ($search !== '') {
                        $q->where(function ($w) use ($search) {
                            $w->where('productions.code', 'like', "%{$search}%")
                              ->orWhere('products.name', 'like', "%{$search}%")
                              ->orWhere('productions.status', 'like', "%{$search}%");
                        });
                    }
                    if ($status !== '') {
                        $q->where('productions.status', $status);
                    }
                    if ($product !== '') {
                        $q->where('productions.product_id', $product);
                    }
                    if ($fromDate !== '') {
                        $q->whereDate('productions.scheduled_at', '>=', $fromDate);
                    }
                    if ($toDate !== '') {
                        $q->whereDate('productions.scheduled_at', '<=', $toDate);
                    }
                })
                ->editColumn('qty_planned', fn ($row) => number_format((float) $row->qty_planned, 4, '.', ''))
                ->editColumn('qty_produced', fn ($row) => number_format((float) $row->qty_produced, 4, '.', ''))
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
     * Show a production with relations
     * GET /api/productions/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $production = Production::with([
                'product',
                'materials.material',
                'products.product',
            ])->findOrFail($id);

            DB::commit();
            return response()->json($production);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Load detail failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Create production + lines
     * POST /api/productions
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code'          => ['nullable', 'string', 'max:191', 'unique:productions,code'],
            'product_id'    => ['required', 'uuid'],
            'qty_planned'   => ['required', 'numeric', 'gt:0'],
            'qty_produced'  => ['nullable', 'numeric', 'gte:0'],
            'status'        => ['nullable', Rule::in(['draft','in_progress','done','cancelled'])],
            'scheduled_at'  => ['nullable', 'date'],
            'started_at'    => ['nullable', 'date'],
            'finished_at'   => ['nullable', 'date'],
            'notes'         => ['nullable', 'string'],

            'materials'                 => ['sometimes', 'array'],
            'materials.*.material_id'   => ['required_with:materials', 'uuid'],
            'materials.*.qty_required'  => ['required_with:materials', 'numeric', 'gt:0'],
            'materials.*.unit_cost'     => ['nullable', 'numeric', 'gte:0'],
            'materials.*.total_cost'    => ['nullable', 'numeric', 'gte:0'],

            'products'                  => ['sometimes', 'array'],
            'products.*.product_id'     => ['required_with:products', 'uuid'],
            'products.*.qty'            => ['required_with:products', 'numeric', 'gt:0'],
            'products.*.unit_cost'      => ['nullable', 'numeric', 'gte:0'],
            'products.*.total_cost'     => ['nullable', 'numeric', 'gte:0'],
            'products.*.produced_at'    => ['nullable', 'date'],
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

            $code = $v['code'] ?? $this->generateCode(now());
            if (Production::where('code', $code)->exists()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors'  => ['code' => ['The code has already been taken.']],
                ], 422);
            }

            $production = Production::create([
                'id'           => (string) Str::uuid(),
                'code'         => $code,
                'product_id'   => $v['product_id'],
                'qty_planned'  => $v['qty_planned'],
                'qty_produced' => $v['qty_produced'] ?? 0,
                'status'       => $v['status'] ?? 'draft',
                'scheduled_at' => isset($v['scheduled_at']) ? Carbon::parse($v['scheduled_at']) : null,
                'started_at'   => isset($v['started_at']) ? Carbon::parse($v['started_at']) : null,
                'finished_at'  => isset($v['finished_at']) ? Carbon::parse($v['finished_at']) : null,
                'notes'        => $v['notes'] ?? null,
            ]);

            // Materials (optional)
            if (!empty($v['materials'])) {
                foreach ($v['materials'] as $m) {
                    ProductionMaterial::create([
                        'id'            => (string) Str::uuid(),
                        'production_id' => $production->id,
                        'material_id'   => $m['material_id'],
                        'qty_required'  => $m['qty_required'],
                        'qty_issued'    => 0,
                        'unit_cost'     => $m['unit_cost'] ?? null,
                        'total_cost'    => $m['total_cost'] ?? null,
                    ]);
                }
            }

            // Products (optional)
            if (!empty($v['products'])) {
                foreach ($v['products'] as $p) {
                    ProductionProduct::create([
                        'id'            => (string) Str::uuid(),
                        'production_id' => $production->id,
                        'product_id'    => $p['product_id'],
                        'qty'           => $p['qty'],
                        'unit_cost'     => $p['unit_cost'] ?? null,
                        'total_cost'    => $p['total_cost'] ?? null,
                        'produced_at'   => isset($p['produced_at']) ? Carbon::parse($p['produced_at']) : null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message'    => 'Created',
                'production' => $production->load(['product', 'materials.material', 'products.product']),
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
     * Update production header and (optionally) reset lines
     * PUT /api/productions/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $production = Production::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code'          => ['sometimes', 'nullable', 'string', 'max:191', Rule::unique('productions', 'code')->ignore($production->id)],
            'product_id'    => ['sometimes', 'required', 'uuid'],
            'qty_planned'   => ['sometimes', 'required', 'numeric', 'gt:0'],
            'qty_produced'  => ['sometimes', 'nullable', 'numeric', 'gte:0'],
            'status'        => ['sometimes', 'nullable', Rule::in(['draft','in_progress','done','cancelled'])],
            'scheduled_at'  => ['sometimes', 'nullable', 'date'],
            'started_at'    => ['sometimes', 'nullable', 'date'],
            'finished_at'   => ['sometimes', 'nullable', 'date'],
            'notes'         => ['sometimes', 'nullable', 'string'],

            'materials'                 => ['sometimes', 'array'],
            'materials.*.material_id'   => ['required_with:materials', 'uuid'],
            'materials.*.qty_required'  => ['required_with:materials', 'numeric', 'gt:0'],
            'materials.*.unit_cost'     => ['nullable', 'numeric', 'gte:0'],
            'materials.*.total_cost'    => ['nullable', 'numeric', 'gte:0'],

            'products'                  => ['sometimes', 'array'],
            'products.*.product_id'     => ['required_with:products', 'uuid'],
            'products.*.qty'            => ['required_with:products', 'numeric', 'gt:0'],
            'products.*.unit_cost'      => ['nullable', 'numeric', 'gte:0'],
            'products.*.total_cost'     => ['nullable', 'numeric', 'gte:0'],
            'products.*.produced_at'    => ['nullable', 'date'],
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

            if (array_key_exists('code', $v))        $production->code = $v['code'];
            if (array_key_exists('product_id', $v))  $production->product_id = $v['product_id'];
            if (array_key_exists('qty_planned', $v)) $production->qty_planned = $v['qty_planned'];
            if (array_key_exists('qty_produced', $v))$production->qty_produced = $v['qty_produced'];
            if (array_key_exists('status', $v))      $production->status = $v['status'];
            if (array_key_exists('scheduled_at', $v))$production->scheduled_at = $v['scheduled_at'] ? Carbon::parse($v['scheduled_at']) : null;
            if (array_key_exists('started_at', $v))  $production->started_at = $v['started_at'] ? Carbon::parse($v['started_at']) : null;
            if (array_key_exists('finished_at', $v)) $production->finished_at = $v['finished_at'] ? Carbon::parse($v['finished_at']) : null;
            if (array_key_exists('notes', $v))       $production->notes = $v['notes'];

            $production->save();

            // If materials provided: reset & recreate
            if (array_key_exists('materials', $v)) {
                ProductionMaterial::where('production_id', $production->id)->delete();
                foreach ($v['materials'] as $m) {
                    ProductionMaterial::create([
                        'id'            => (string) Str::uuid(),
                        'production_id' => $production->id,
                        'material_id'   => $m['material_id'],
                        'qty_required'  => $m['qty_required'],
                        'qty_issued'    => 0,
                        'unit_cost'     => $m['unit_cost'] ?? null,
                        'total_cost'    => $m['total_cost'] ?? null,
                    ]);
                }
            }

            // If products provided: reset & recreate
            if (array_key_exists('products', $v)) {
                ProductionProduct::where('production_id', $production->id)->delete();
                foreach ($v['products'] as $p) {
                    ProductionProduct::create([
                        'id'            => (string) Str::uuid(),
                        'production_id' => $production->id,
                        'product_id'    => $p['product_id'],
                        'qty'           => $p['qty'],
                        'unit_cost'     => $p['unit_cost'] ?? null,
                        'total_cost'    => $p['total_cost'] ?? null,
                        'produced_at'   => isset($p['produced_at']) ? Carbon::parse($p['produced_at']) : null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message'    => 'Updated',
                'production' => $production->load(['product', 'materials.material', 'products.product']),
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
     * Delete a production
     * DELETE /api/productions/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $production = Production::findOrFail($id);

        try {
            DB::beginTransaction();

            // Children will be removed by FK cascade
            $production->delete();

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

    // ================== helper ==================
    private function generateCode(Carbon $date): string
    {
        $prefix = 'MO-' . $date->format('Ymd');
        $count  = Production::whereDate('created_at', $date->toDateString())->count();
        $seq    = str_pad((string) ($count + 1), 5, '0', STR_PAD_LEFT);
        return "{$prefix}-{$seq}";
    }
}
