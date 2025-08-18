<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Customer::query()->orderByDesc('created_at');

        return DataTables::eloquent($query)
            ->filter(function ($q) use ($request) {
                if ($s = $request->get('search')['value'] ?? null) {
                    $q->where(function ($qq) use ($s) {
                        $qq->where('name', 'like', "%{$s}%")
                           ->orWhere('email', 'like', "%{$s}%")
                           ->orWhere('phone', 'like', "%{$s}%");
                    });
                }
            })
            ->toJson();
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(Customer::findOrFail($id));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => ['required','string','max:255'],
            'email'   => ['nullable','email','max:255','unique:customers,email'],
            'phone'   => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:500'],
        ]);

        try {
            DB::beginTransaction();
            $customer = Customer::create($data);
            DB::commit();

            return response()->json(['message' => 'Created', 'customer' => $customer], 201);
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
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'name'    => ['sometimes','required','string','max:255'],
            'email'   => [
                'nullable','email','max:255',
                Rule::unique('customers','email')->ignore($customer->id, 'id'),
            ],
            'phone'   => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:500'],
        ]);

        try {
            DB::beginTransaction();
            $customer->fill($data)->save();
            DB::commit();

            return response()->json(['message' => 'Updated', 'customer' => $customer]);
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
        $customer = Customer::findOrFail($id);

        try {
            DB::beginTransaction();
            $customer->delete();
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
