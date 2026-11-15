<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\FirebaseIdTokenVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class FirebaseLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_firebase_token_creates_a_member_account_and_sanctum_token(): void
    {
        $this->mockVerifier();

        $this->postJson('/api/auth/firebase-login', ['id_token' => 'firebase-id-token'])
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user.email', 'reader@example.com')
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);

        $this->assertDatabaseHas('users', [
            'email' => 'reader@example.com',
            'firebase_uid' => 'firebase-uid-123',
            'firebase_provider' => 'google.com',
            'role' => 'member',
        ]);
    }

    public function test_firebase_uid_reuses_the_existing_local_account(): void
    {
        $this->mockVerifier();
        $user = User::factory()->create([
            'email' => 'reader@example.com',
            'firebase_uid' => 'firebase-uid-123',
            'role' => 'admin',
        ]);

        $this->postJson('/api/auth/firebase-login', ['id_token' => 'firebase-id-token'])
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.role', 'admin');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_firebase_login_does_not_link_an_existing_account_by_email(): void
    {
        $this->mockVerifier();
        $user = User::factory()->create([
            'email' => 'reader@example.com',
            'password' => Hash::make('old-account-password'),
        ]);

        $this->postJson('/api/auth/firebase-login', ['id_token' => 'firebase-id-token'])
            ->assertConflict()
            ->assertJsonPath('code', 'account_link_required');

        $this->assertNull($user->fresh()->firebase_uid);

        $this->postJson('/api/auth/firebase-login', [
            'id_token' => 'firebase-id-token',
            'existing_account_password' => 'old-account-password',
        ])->assertOk()->assertJsonPath('user.id', $user->id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'firebase_uid' => 'firebase-uid-123',
        ]);
    }

    public function test_firebase_login_rejects_an_invalid_token(): void
    {
        $verifier = Mockery::mock(FirebaseIdTokenVerifier::class);
        $verifier->shouldReceive('verify')->once()->with('invalid-token')->andReturnNull();
        $this->app->instance(FirebaseIdTokenVerifier::class, $verifier);

        $this->postJson('/api/auth/firebase-login', ['id_token' => 'invalid-token'])
            ->assertUnauthorized();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_sanctum_session_can_access_profile_and_logout_revokes_the_token(): void
    {
        $this->mockVerifier();

        $loginResponse = $this->postJson('/api/auth/firebase-login', ['id_token' => 'firebase-id-token'])
            ->assertOk();
        $token = $loginResponse->json('token');

        $this->withToken($token)
            ->getJson('/api/auth/profile')
            ->assertOk()
            ->assertJsonPath('user.email', 'reader@example.com');

        $this->withToken($token)
            ->postJson('/api/auth/logout')
            ->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $loginResponse->json('user.id'),
            'name' => 'android-app',
        ]);
        Auth::forgetGuards();

        $this->withToken($token)
            ->getJson('/api/auth/profile')
            ->assertUnauthorized();
    }

    private function mockVerifier(): void
    {
        $verifier = Mockery::mock(FirebaseIdTokenVerifier::class);
        $verifier->shouldReceive('verify')->andReturn([
            'sub' => 'firebase-uid-123',
            'email' => 'reader@example.com',
            'email_verified' => true,
            'name' => 'Reader Name',
            'picture' => null,
            'firebase' => ['sign_in_provider' => 'google.com'],
        ]);

        $this->app->instance(FirebaseIdTokenVerifier::class, $verifier);
    }

}
