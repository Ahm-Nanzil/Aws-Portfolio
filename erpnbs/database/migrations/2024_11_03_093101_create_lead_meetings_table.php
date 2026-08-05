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
        Schema::create('lead_meetings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('members');
            $table->string('title');
            $table->date('date');
            $table->time('time');
            $table->text('note')->nullable();
            $table->integer('created_by');
            $table->unsignedBigInteger('lead_id');  // Adding the lead_id column
            $table->timestamps();
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_meetings');
    }
};
