<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function datatable(): JsonResponse
    {
        try {
            $suppliers = Supplier::query()
                ->select('id', 'code', 'name', 'phone', 'email', 'city', 'status', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $suppliers
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load suppliers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all suppliers
     */
    public function index(): JsonResponse
    {
        try {
            $suppliers = Supplier::all();
            return response()->json([
                'success' => true,
                'data' => $suppliers
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch suppliers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new supplier
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();

        try {
            $supplier = Supplier::create([
                'id' => (string) Str::uuid(),
                'code' => 'SUP-' . strtoupper(Str::random(6)),
                'name' => $request->name,
                'contact_name' => $request->contact_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'status' => 'active',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supplier created successfully',
                'data' => $supplier
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show single supplier
     */
    public function show(string $id): JsonResponse
    {
        try {
            $supplier = Supplier::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $supplier
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update supplier
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive',
        ]);

        DB::beginTransaction();

        try {
            $supplier = Supplier::findOrFail($id);

            $supplier->update([
                'name' => $request->name,
                'contact_name' => $request->contact_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'status' => $request->status ?? $supplier->status,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supplier updated successfully',
                'data' => $supplier
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete supplier
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
