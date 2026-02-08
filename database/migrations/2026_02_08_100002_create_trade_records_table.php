<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trade_records', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('user_id', 36);
            $table->string('trade_id', 36)->unique();
            $table->string('asset_symbol', 20);
            $table->decimal('entry_price_amount', 20, 8);
            $table->string('entry_price_currency', 10)->default('BRL');
            $table->integer('quantity');
            $table->timestampTz('period_start');
            $table->timestampTz('period_end');
            $table->decimal('gross_result_amount', 20, 8)->nullable();
            $table->string('gross_result_currency', 10)->nullable();
            $table->decimal('net_result_amount', 20, 8)->nullable();
            $table->string('net_result_currency', 10)->nullable();
            $table->string('result_type', 15)->nullable();
            $table->decimal('realized_rr', 10, 8)->nullable();
            $table->boolean('followed_plan')->nullable();
            $table->text('deviation_reason')->nullable();
            $table->string('emotional_state', 20)->nullable();
            $table->text('keep_doing')->nullable();
            $table->text('improve_next_time')->nullable();
            $table->boolean('reviewed')->default(false);
            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');

            $table->index('user_id');
            $table->index(['user_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_records');
    }
};
