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
        Schema::table('lead_views', function (Blueprint $table) {
            $table->boolean('recurrence_status')->default(false)->after('status');
            $table->renameColumn('file_ids', 'files');
            $table->string('created_by');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_views', function (Blueprint $table) {
            $table->dropColumn('recurrence_status');
            $table->dropColumn('created_by');

        });
    }
};
