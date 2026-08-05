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
        Schema::create('customer_visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->index();
            $table->string('title', 191)->collation('utf8mb4_unicode_ci');
            $table->text('description')->nullable()->collation('utf8mb4_unicode_ci');
            $table->longText('assigned_users')->nullable()->collation('utf8mb4_bin');
            $table->date('date');
            $table->time('time');
            $table->string('location', 191)->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('status', 191)->collation('utf8mb4_unicode_ci');
            $table->tinyInteger('recurrence_status')->default(0);
            $table->integer('recurrence')->default(0);
            $table->integer('repeat_interval')->nullable();
            $table->date('end_recurrence')->nullable();
            $table->time('reminder')->nullable();
            $table->longText('files')->nullable()->collation('utf8mb4_bin');
            $table->timestamps();
            $table->string('created_by', 191)->collation('utf8mb4_unicode_ci');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_visits');
    }
};
