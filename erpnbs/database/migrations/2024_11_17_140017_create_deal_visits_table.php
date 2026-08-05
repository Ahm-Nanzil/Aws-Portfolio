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
        Schema::create('deal_visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('deal_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('assigned_users')->nullable();
            $table->date('date');
            $table->time('time');
            $table->string('location')->nullable();
            $table->string('status');
            $table->integer('recurrence')->default(0);
            $table->integer('repeat_interval')->nullable();
            $table->date('end_recurrence')->nullable();
            $table->time('reminder')->nullable();
            $table->json('file_ids')->nullable();
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_visits');
    }
};
