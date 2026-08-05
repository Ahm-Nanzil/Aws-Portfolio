<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            // Lead Basic Info
            $table->unsignedBigInteger('lead_owner')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_entity_name')->nullable();
            $table->string('company_entity_logo')->nullable();
            $table->string('company_phone_ll1')->nullable();
            $table->string('company_phone_ll2')->nullable();
            $table->string('company_email')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('company_linkedin')->nullable();
            $table->string('company_location')->nullable();

            // Primary Contact Info
            $table->string('salutation')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile_primary')->nullable();
            $table->string('mobile_secondary')->nullable();
            $table->string('email_work')->nullable();
            $table->string('email_personal')->nullable();
            $table->string('phone_ll')->nullable();
            $table->string('company_phone_ll')->nullable();
            $table->integer('extension')->nullable();
            $table->string('linkedin_profile')->nullable();

            // Additional Info

            $table->string('currency')->nullable();
            $table->string('industry')->nullable();
            $table->text('note')->nullable();
            $table->json('additional_contacts'); // Hierarchy structure in JSON format

            // Add foreign key constraints if necessary
            // $table->foreign('lead_owner')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('pipeline')->references('id')->on('pipelines')->onDelete('set null');
            // $table->foreign('lead_stage')->references('id')->on('lead_stages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'lead_owner',
                'company_website',
                'company_entity_name',
                'company_entity_logo',
                'company_phone_ll1',
                'company_phone_ll2',
                'company_email',
                'address1',
                'address2',
                'city',
                'region',
                'country',
                'zip_code',
                'company_linkedin',
                'company_location',
                'salutation',
                'first_name',
                'last_name',
                'mobile_primary',
                'mobile_secondary',
                'email_work',
                'email_personal',
                'phone_ll',
                'company_phone_ll',
                'extension',
                'linkedin_profile',
                'currency',
                'industry',
                'note',
                'additional_contacts',
            ]);
        });
    }
}

