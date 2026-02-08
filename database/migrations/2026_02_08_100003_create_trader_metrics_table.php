<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trader_metrics', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('user_id', 36);
            $table->string('period', 20);
            $table->decimal('win_rate', 10, 8);
            $table->decimal('expectancy', 20, 8);
            $table->decimal('profit_factor', 20, 8);
            $table->decimal('max_drawdown', 10, 8);
            $table->decimal('plan_discipline_score', 10, 8);
            $table->decimal('emotional_stability_index', 10, 8);
            $table->timestampTz('calculated_at');
            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');

            $table->unique(['user_id', 'period']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trader_metrics');
    }
};
