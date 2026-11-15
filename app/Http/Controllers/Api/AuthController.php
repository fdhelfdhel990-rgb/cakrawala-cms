<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseIdTokenVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function firebaseLogin(Request $request, FirebaseIdTokenVerifier $tokenVerifier): JsonResponse
    {
        $validated = $request->validate([
            'id_token' => ['required', 'string', 'max:10000'],
            'existing_account_password' => ['nullable', 'string', 'max:255'],
        ]);

        $claims = $tokenVerifier->verify($validated['id_token']);
        if ($claims === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Firebase ID Token tidak valid, kedaluwarsa, atau berasal dari project yang berbeda.',
            ], 401);
        }

        $firebaseUid = $claims['sub'];
        $email = mb_strtolower($claims['email']);
        $firebaseClaims = is_array($claims['firebase'] ?? null) ? $claims['firebase'] : [];
        $provider = is_string($firebaseClaims['sign_in_provider'] ?? null)
            ? mb_substr($firebaseClaims['sign_in_provider'], 0, 32)
            : null;
        $name = is_string($claims['name'] ?? null) ? mb_substr($claims['name'], 0, 255) : str($email)->before('@')->toString();
        $avatar = is_string($claims['picture'] ?? null) ? $claims['picture'] : null;

        $result = DB::transaction(function () use ($firebaseUid, $email, $provider, $name, $avatar, $claims, $validated): array {
            $user = User::query()->where('firebase_uid', $firebaseUid)->lockForUpdate()->first();

            if ($user) {
                $emailOwner = User::query()->whereRaw('LOWER(email) = ?', [$email])->where('id', '<>', $user->id)->exists();
                if ($emailOwner) {
                    return ['conflict' => true];
                }

                $user->forceFill([
                    'email' => $email,
                    'name' => $name,
                    'avatar' => $avatar ?? $user->avatar,
                    'firebase_provider' => $provider,
                    'email_verified_at' => ($claims['email_verified'] ?? false) === true ? ($user->email_verified_at ?? now()) : $user->email_verified_at,
                ])->save();

                return ['user' => $user];
            }

            $emailOwner = User::query()->whereRaw('LOWER(email) = ?', [$email])->lockForUpdate()->first();
            if ($emailOwner) {
                if (is_string($emailOwner->password)
                    && isset($validated['existing_account_password'])
                    && Hash::check($validated['existing_account_password'], $emailOwner->password)
                    && ($emailOwner->firebase_uid === null || $emailOwner->firebase_uid === $firebaseUid)) {
                    $emailOwner->forceFill([
                        'firebase_uid' => $firebaseUid,
                        'firebase_provider' => $provider,
                        'name' => $name,
                        'avatar' => $avatar ?? $emailOwner->avatar,
                        'email_verified_at' => ($claims['email_verified'] ?? false) === true ? ($emailOwner->email_verified_at ?? now()) : $emailOwner->email_verified_at,
                    ])->save();

                    return ['user' => $emailOwner];
                }

                return ['conflict' => true];
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => str()->random(64),
                'role' => 'member',
                'avatar' => $avatar,
                'firebase_uid' => $firebaseUid,
                'firebase_provider' => $provider,
                'email_verified_at' => ($claims['email_verified'] ?? false) === true ? now() : null,
            ]);

            return ['user' => $user];
        });

        if ($result['conflict'] ?? false) {
            return response()->json([
                'status' => 'error',
                'code' => 'account_link_required',
                'message' => 'Email ini sudah terdaftar pada akun lama. Masuk ke akun lama dan gunakan alur pengaitan akun untuk menghubungkan Firebase.',
            ], 409);
        }

        /** @var User $user */
        $user = $result['user'];

        $token = $user->createToken('android-app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Autentikasi Firebase berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();
        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil keluar (logout)',
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        $readingCount = $user->bookshelves()->where('type', 'reading')->count();
        $wishlistCount = $user->bookshelves()->where('type', 'wishlist')->count();

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role' => $user->role,
                'stats' => [
                    'reading_count' => $readingCount,
                    'wishlist_count' => $wishlistCount,
                ],
            ],
        ]);
    }
}
