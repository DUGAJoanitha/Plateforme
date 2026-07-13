<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->float('target_value');
            $table->float('current_value')->default(0);
            $table->float('baseline')->nullable();
            $table->string('unit');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpis');
    }
};
