<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_line_id')->constrained('budget_lines')->onDelete('cascade');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->string('proof_url')->nullable();
            $table->enum('status', ['pending', 'validated', 'rejected'])->default('pending');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('budget_line_id');
            $table->index('status');
            $table->index('submitted_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
