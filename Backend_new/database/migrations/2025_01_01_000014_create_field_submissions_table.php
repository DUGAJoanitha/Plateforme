<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('field_forms')->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('activity_id')->nullable()->constrained('activities')->onDelete('set null');
            $table->json('data_json');
            $table->decimal('gps_lat', 10, 8)->nullable();
            $table->decimal('gps_lng', 11, 8)->nullable();
            $table->json('photos_json')->nullable(); // Array of photo URLs
            $table->enum('status', ['pending', 'synced', 'validated', 'rejected'])->default('pending');
            $table->text('validation_notes')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('form_id');
            $table->index('agent_id');
            $table->index('status');
            $table->index('synced_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_submissions');
    }
};
