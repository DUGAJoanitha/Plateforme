<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_measures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_id')->constrained('kpis')->onDelete('cascade');
            $table->foreignId('collected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->float('value');
            $table->text('notes')->nullable();
            $table->dateTime('collected_at')->default(now());
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('kpi_id');
            $table->index('collected_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_measures');
    }
};
