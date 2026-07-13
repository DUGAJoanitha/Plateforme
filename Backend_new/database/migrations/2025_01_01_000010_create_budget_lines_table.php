<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('category'); // Personnel, Travel, Equipment, etc.
            $table->text('description')->nullable();
            $table->decimal('allocated', 15, 2);
            $table->decimal('spent', 15, 2)->default(0);
            $table->integer('alert_threshold')->default(90); // percentage
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('project_id');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_lines');
    }
};
