<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analyses', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('trade_id', 36)->nullable();
            $table->string('analysis_type', 50);
            $table->text('content');
            $table->jsonb('metadata')->nullable();
            $table->timestampTz('created_at');
            $table->timestampTz('updated_at');

            $table->index('trade_id');
        });

        DB::statement('ALTER TABLE ai_analyses ADD COLUMN embedding vector(1536)');
        DB::statement('CREATE INDEX ai_analyses_embedding_idx ON ai_analyses USING hnsw (embedding vector_cosine_ops)');
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analyses');
    }
};
