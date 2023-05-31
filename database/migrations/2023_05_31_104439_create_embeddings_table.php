<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('embeddings', function (Blueprint $table) {
            $table->uuid("uuid");
            $table->uuid("collection_id");
            $table->uuid("custom_id");
            $table->json("cmetadata");
            $table->longText("document");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE embeddings ADD embedding vector;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('langchain_pg_embedding');
    }
};
