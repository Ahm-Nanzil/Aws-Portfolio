<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('queue'); // Add queue column
            $table->longText('payload'); // Add payload column
            $table->tinyInteger('attempts')->unsigned()->default(0); // Add attempts column
            $table->unsignedInteger('reserved_at')->nullable(); // Add reserved_at column
            $table->unsignedInteger('available_at'); // Add available_at column

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('queue');
            $table->dropColumn('payload');
            $table->dropColumn('attempts');
            $table->dropColumn('reserved_at');
            $table->dropColumn('available_at');

        });
    }
}
