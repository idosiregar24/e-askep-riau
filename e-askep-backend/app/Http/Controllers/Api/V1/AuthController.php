<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'login'    => ['required', 'string'], // email atau nim_nip
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->login)
            ->orWhere('nim_nip', $request->login)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda berstatus non-aktif. Silakan hubungi admin akademik.',
            ], 403);
        }

        $token = $user->createToken('e-askep-mobile-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token' => $token,
                'user'  => [
                    'id'           => $user->id,
                    'name'         => $user->name,
                    'nim_nip'      => $user->nim_nip,
                    'email'        => $user->email,
                    'role'         => $user->role,
                    'phone_number' => $user->phone_number,
                ],
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Profil pengguna aktif.',
            'data'    => [
                'id'           => $user->id,
                'name'         => $user->name,
                'nim_nip'      => $user->nim_nip,
                'email'        => $user->email,
                'role'         => $user->role,
                'phone_number' => $user->phone_number,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil keluar dan token dinonaktifkan.',
        ]);
    }
}
