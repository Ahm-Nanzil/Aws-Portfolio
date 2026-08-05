<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('all_filters', function (Blueprint $table) {
            $table->id();
            $table->integer('saved_by');
            $table->integer('pipeline_id');
            $table->string('page_name');
            $table->string('title');
            $table->json('criteria')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_filters');
    }
};
