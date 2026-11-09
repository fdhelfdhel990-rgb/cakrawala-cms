<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'membership_status')) {
                $table->string('membership_status')->default('approved')->after('role');
            }
            if (! Schema::hasColumn('users', 'admin_level')) {
                $table->string('admin_level')->default('super_admin')->after('membership_status');
            }
        });

        Schema::table('articles', function (Blueprint $table) {
            if (! Schema::hasColumn('articles', 'status')) {
                $table->string('status')->default('published')->after('type');
            }
        });

        Schema::table('reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('reviews', 'reply')) {
                $table->text('reply')->nullable()->after('comment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['membership_status', 'admin_level']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['reply']);
        });
    }
};
