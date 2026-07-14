<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new columns to users table
            $table->foreignId('org_id')->nullable()->constrained('organisations')->onDelete('restrict');
            $table->enum('role', ['super_admin', 'coordinator', 'se_manager', 'field_agent', 'accountant', 'funder', 'partner'])->default('field_agent');
            $table->string('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('org_id');
            $table->dropColumn('role');
            $table->dropColumn('two_factor_secret');
            $table->dropColumn('two_factor_enabled');
            $table->dropColumn('two_factor_confirmed_at');
            $table->dropColumn('is_active');
            $table->dropColumn('last_login_at');
            $table->dropColumn('phone');
            $table->dropColumn('bio');
            $table->dropColumn('avatar_url');
        });
    }
};
