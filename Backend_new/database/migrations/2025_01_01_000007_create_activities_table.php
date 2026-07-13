<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('responsible_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['pending', 'in_progress', 'blocked', 'completed', 'cancelled'])->default('pending');
            $table->integer('progress')->default(0); // 0-100
            $table->foreignId('depends_on')->nullable()->constrained('activities')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('project_id');
            $table->index('status');
            $table->index('responsible_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
