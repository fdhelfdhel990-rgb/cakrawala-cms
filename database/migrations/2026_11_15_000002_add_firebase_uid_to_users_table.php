<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'firebase_uid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('firebase_uid', 128)->nullable()->unique();
                $table->string('firebase_provider', 32)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'firebase_uid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['firebase_uid']);
                $table->dropColumn(['firebase_uid', 'firebase_provider']);
            });
        }
    }
};
