<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trade_decisions', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('user_id', 36);
            $table->string('asset_symbol', 20);
            $table->string('asset_market', 20);
            $table->string('direction', 10);
            $table->string('timeframe', 10);
            $table->string('state', 20);
            $table->decimal('entry_price', 20, 8)->nullable();
            $table->string('entry_price_type', 10)->nullable();
            $table->decimal('stop_price', 20, 8)->nullable();
            $table->string('stop_price_type', 10)->nullable();
            $table->decimal('target_price', 20, 8)->nullable();
            $table->string('target_price_type', 10)->nullable();
            $table->decimal('risk_percentage', 10, 8)->nullable();
            $table->integer('position_size')->nullable();
            $table->decimal('executed_price', 20, 8)->nullable();
            $table->integer('executed_quantity')->nullable();
            $table->timestampTz('executed_at')->nullable();
            $table->string('result')->nullable();
            $table->timestampTz('closed_at')->nullable();
            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');

            $table->index('user_id');
            $table->index(['user_id', 'state']);
            $table->index(['user_id', 'created_at']);
            $table->index('asset_symbol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_decisions');
    }
};
