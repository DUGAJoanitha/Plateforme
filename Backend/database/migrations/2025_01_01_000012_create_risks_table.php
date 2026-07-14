<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->text('description');
            $table->integer('probability')->default(0); // 1-100
            $table->integer('impact')->default(0); // 1-100
            $table->float('score')->default(0); // calculated
            $table->text('mitigation')->nullable();
            $table->enum('status', ['identified', 'monitored', 'mitigated', 'closed'])->default('identified');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};
