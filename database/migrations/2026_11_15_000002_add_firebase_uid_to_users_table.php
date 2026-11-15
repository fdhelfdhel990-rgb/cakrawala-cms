<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'firebase_uid')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('firebase_uid', 128)->nullable()->unique();
            });
        } elseif (! Schema::hasIndex('users', ['firebase_uid'], 'unique')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->unique('firebase_uid');
            });
        }

        if (! Schema::hasColumn('users', 'firebase_provider')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('firebase_provider', 32)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'firebase_uid')
            && DB::table('users')->whereNotNull('firebase_uid')->exists()) {
            throw new RuntimeException('Firebase UID values exist; refusing to drop Firebase account mappings.');
        }

        $hasFirebaseUid = Schema::hasColumn('users', 'firebase_uid');
        $hasFirebaseProvider = Schema::hasColumn('users', 'firebase_provider');

        if ($hasFirebaseUid || $hasFirebaseProvider) {
            Schema::table('users', function (Blueprint $table) use ($hasFirebaseUid, $hasFirebaseProvider): void {
                if ($hasFirebaseUid) {
                    $table->dropUnique(['firebase_uid']);
                    $table->dropColumn('firebase_uid');
                }

                if ($hasFirebaseProvider) {
                    $table->dropColumn('firebase_provider');
                }
            });
        }
    }
};
