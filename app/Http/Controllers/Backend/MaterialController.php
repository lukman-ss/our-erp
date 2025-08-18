<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    public function datatable(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $query = Material::query()->orderByDesc('created_at');

            $json = DataTables::eloquent($query)
                ->addIndexColumn()
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

    public function show(string $id): JsonResponse
    {
        return response()->json(Material::findOrFail($id));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code'        => ['required','string','max:100','unique:materials,code'],
            'name'        => ['required','string','max:255'],
            'unit'        => ['nullable','string','max:20'],
            'cost_price'  => ['nullable','numeric'],
            'stock'       => ['nullable','numeric'],
            'description' => ['nullable','string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $material = Material::create($validator->validated());

            DB::commit();

            return response()->json([
                'message'  => 'Created',
                'material' => $material,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Create failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $material = Material::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code'        => ['sometimes','required','string','max:100', Rule::unique('materials','code')->ignore($material->id)],
            'name'        => ['sometimes','required','string','max:255'],
            'unit'        => ['nullable','string','max:20'],
            'cost_price'  => ['nullable','numeric'],
            'stock'       => ['nullable','numeric'],
            'description' => ['nullable','string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $material->fill($validator->validated())->save();

            DB::commit();

            return response()->json([
                'message'  => 'Updated',
                'material' => $material,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Update failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $material = Material::findOrFail($id);

        try {
            DB::beginTransaction();

            $material->delete();

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
}
