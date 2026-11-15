<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class FirebaseUidMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rollback_refuses_to_remove_existing_firebase_uid_mappings(): void
    {
        $user = User::factory()->create([
            'firebase_uid' => 'firebase-uid-to-preserve',
        ]);
        $migration = require base_path('database/migrations/2026_11_15_000002_add_firebase_uid_to_users_table.php');

        try {
            $migration->down();
            $this->fail('The migration should refuse to remove populated Firebase UID mappings.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Firebase UID values exist; refusing to drop Firebase account mappings.',
                $exception->getMessage(),
            );
        }

        $this->assertTrue(Schema::hasColumn('users', 'firebase_uid'));
        $this->assertTrue(Schema::hasColumn('users', 'firebase_provider'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'firebase_uid' => 'firebase-uid-to-preserve',
        ]);
    }
}
