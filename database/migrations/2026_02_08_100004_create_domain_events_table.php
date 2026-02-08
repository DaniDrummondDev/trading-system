<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_events', function (Blueprint $table) {
            $table->string('event_id', 36)->primary();
            $table->string('event_type', 100);
            $table->string('aggregate_id', 36);
            $table->string('aggregate_type', 100);
            $table->jsonb('payload');
            $table->timestampTz('occurred_on');
            $table->timestampTz('created_at');

            $table->index('aggregate_id');
            $table->index('occurred_on');
            $table->index(['aggregate_id', 'occurred_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_events');
    }
};
