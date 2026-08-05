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
        Schema::create('lead_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lead_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('assigned_users')->nullable();
            $table->date('date');
            $table->time('time');
            $table->string('location')->nullable();
            $table->string('status');
            $table->integer('recurrence')->default(0); // For recurrence type (e.g., Daily, Weekly, etc.)
            $table->integer('repeat_interval')->nullable();
            $table->date('end_recurrence')->nullable();
            $table->time('reminder')->nullable();
            $table->json('file_ids')->nullable();
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_views');
    }
};
