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
        Schema::create('customer_meetings', function (Blueprint $table) {
            $table->id();
            $table->longText('members')->collation('utf8mb4_bin');
            $table->string('title', 191)->collation('utf8mb4_unicode_ci');
            $table->date('date');
            $table->time('time');
            $table->text('note')->nullable()->collation('utf8mb4_unicode_ci');
            $table->integer('created_by');
            $table->unsignedBigInteger('customer_id')->index();
            $table->timestamps();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_meetings');
    }
};
