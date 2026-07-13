<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('schema_json'); // JSON Schema definition
            $table->integer('version')->default(1);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_forms');
    }
};
