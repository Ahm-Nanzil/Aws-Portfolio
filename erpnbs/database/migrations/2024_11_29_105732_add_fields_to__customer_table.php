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
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('pipeline_id')->nullable();
            $table->integer('stage_id')->nullable();
            $table->string('sources')->nullable();
            $table->string('products')->nullable();

            // basic info
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
             $table->text('notes')->nullable();

             $table->json('additional_contacts')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     */
/**
 * Reverse the migrations.
 */
public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop columns added in the up() method
            $table->dropColumn([
                'pipeline_id',
                'stage_id',
                'sources',
                'products',
                // Basic info
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

                // Primary Contact Info
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
                'notes',
                // Additional contacts
                'additional_contacts',
            ]);
        });
    }

};
