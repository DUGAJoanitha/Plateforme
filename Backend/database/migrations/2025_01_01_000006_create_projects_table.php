<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_id')->constrained('organisations')->onDelete('cascade');
            $table->foreignId('programme_id')->nullable()->constrained('programmes')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('budget_total', 15, 2)->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['planning', 'active', 'on_hold', 'completed', 'cancelled'])->default('planning');
            $table->float('risk_score')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('org_id');
            $table->index('status');
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
