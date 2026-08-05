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
        Schema::table('deal_tasks', function (Blueprint $table) {
            $table->string('created_by');
            $table->json('assigned_users')->nullable();
            $table->boolean('recurrence_status')->default(false);
            $table->integer('recurrence')->default(0); // For recurrence type (e.g., Daily, Weekly, etc.)
            $table->integer('repeat_interval')->nullable();
            $table->date('end_recurrence')->nullable();
            $table->time('reminder')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deal_tasks', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('assigned_users');
            $table->dropColumn('recurrence_status');
            $table->dropColumn('recurrence');
            $table->dropColumn('repeat_interval');
            $table->dropColumn('end_recurrence');
            $table->dropColumn('reminder');

        });
    }
};
