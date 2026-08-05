<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'user_id',
        'pipeline_id',
        'stage_id',
        'sources',
        'products',
        'notes',
        'labels',
        'order',
        'created_by',
        'is_active',
        'date',

        // Lead Basic Info
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

        // Additional Info

        'currency',
        'industry',
        'note',

        // additional Contact

        'additional_contacts',
    ];

    public function labels()
    {
        if($this->labels)
        {
            return Label::whereIn('id', explode(',', $this->labels))->get();
        }

        return false;
    }

    public function stage()
    {
        return $this->hasOne('App\Models\LeadStage', 'id', 'stage_id');
    }

    public function files()
    {
        return $this->hasMany('App\Models\LeadFile', 'lead_id', 'id');
    }

    public function pipeline()
    {
        return $this->hasOne('App\Models\Pipeline', 'id', 'pipeline_id');
    }

    public function products()
    {
        if($this->products)
        {
            return ProductService::whereIn('id', explode(',', $this->products))->get();
        }

        return [];
    }

    public function sources()
    {
        if($this->sources)
        {
            return Source::whereIn('id', explode(',', $this->sources))->get();
        }

        return [];
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_leads', 'lead_id', 'user_id');
    }

    public function activities()
    {
        return $this->hasMany('App\Models\LeadActivityLog', 'lead_id', 'id')->orderBy('id', 'desc');
    }

    public function discussions()
    {
        return $this->hasMany('App\Models\LeadDiscussion', 'lead_id', 'id')->orderBy('id', 'desc');
    }

    public function calls()
    {
        return $this->hasMany('App\Models\LeadCall', 'lead_id', 'id');
    }

    public function emails()
    {
        return $this->hasMany('App\Models\LeadEmail', 'lead_id', 'id')->orderByDesc('id');
    }
    public function tasks()
    {
        return $this->hasMany('App\Models\LeadTasks', 'lead_id', 'id');
    }
    public function meetings()
    {
        return $this->hasMany('App\Models\LeadMeeting', 'lead_id', 'id');
    }
    public function visits()
    {
        return $this->hasMany('App\Models\LeadView', 'lead_id', 'id');
    }
}
