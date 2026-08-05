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
        Schema::create('deal_email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('to')->nullable(); // The recipient's email
            $table->string('subject')->nullable(); // Email subject
            $table->text('description')->nullable(); // Email content
            $table->timestamps(); // Created at & updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_email_templates');
    }
};
