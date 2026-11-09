<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JoinUsController extends Controller
{
    /**
     * Handle membership registration from the mobile app.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = User::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'reason' => $validated['reason'] ?? null,
                'password' => Hash::make(Str::random(16)),
                'role' => 'member',
                'membership_status' => 'pending',
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran anggota komunitas berhasil dikirim. Menunggu verifikasi admin.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'reason' => $user->reason,
                'membership_status' => $user->membership_status,
            ],
        ], 201);
    }
}
