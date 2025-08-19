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
    /**
     * GET /api/customers/datatable
     * DataTables server-side
     */
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

    /**
     * GET /api/customers/{id}
     */
    public function show(string $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    /**
     * POST /api/customers
     * Terima payload besar, simpan hanya kolom yang ada di DB.
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi hanya kolom yang ada di DB
        $request->validate([
            'name'    => ['required','string','max:255'],
            'email'   => ['nullable','email','max:255','unique:customers,email'],
            'phone'   => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:500'],
        ]);

        // Whitelist kolom agar field ekstra diabaikan
        $data = collect($request->only(['name','email','phone','address']))
            ->map(fn($v) => is_string($v) ? trim($v) : $v)
            ->toArray();

        try {
            DB::beginTransaction();
            $customer = Customer::create($data);
            DB::commit();

            return response()->json([
                'message'  => 'Created',
                'customer' => $customer,
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
     * PUT /api/customers/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name'    => ['sometimes','required','string','max:255'],
            'email'   => [
                'nullable','email','max:255',
                Rule::unique('customers','email')->ignore($customer->id, 'id'),
            ],
            'phone'   => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:500'],
        ]);

        $data = collect($request->only(['name','email','phone','address']))
            ->filter(fn($v) => !is_null($v)) // hanya update field yang dikirim
            ->map(fn($v) => is_string($v) ? trim($v) : $v)
            ->toArray();

        try {
            DB::beginTransaction();
            $customer->fill($data)->save();
            DB::commit();

            return response()->json([
                'message'  => 'Updated',
                'customer' => $customer,
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
     * DELETE /api/customers/{id}
     */
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
