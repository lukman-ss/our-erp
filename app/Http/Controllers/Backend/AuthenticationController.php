<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->only('name','email','password');

        $v = Validator::make($data, [
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6'],
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $user = new User([
                'name'  => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            $user->save();

            $token = JWTAuth::fromUser($user);

            DB::commit();

            return response()->json([
                'message'      => 'Registered',
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth()->factory()->getTTL() * 60,
                'user'         => $user,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function do_login(Request $request): JsonResponse
    {
        $data = $request->only('email','password');

        $v = Validator::make($data, [
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        try {
            DB::beginTransaction();

            if (! $token = JWTAuth::attempt($data)) {
                DB::rollBack();
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            DB::commit();

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth()->factory()->getTTL() * 60,
                'user'         => auth()->user(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Login failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function refresh(): JsonResponse
    {
        try {
            $newToken = auth()->refresh();

            return response()->json([
                'access_token' => $newToken,
                'token_type'   => 'bearer',
                'expires_in'   => auth()->factory()->getTTL() * 60,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Token refresh failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 401);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            auth()->logout();
            return response()->json(['message' => 'Logged out']);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
