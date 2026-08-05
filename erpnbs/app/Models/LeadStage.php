<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadStage extends Model
{
    protected $fillable = [
        'name',
        'pipeline_id',
        'created_by',
        'order',
    ];

    public function lead()
    {
        if(\Auth::user()->type=='company'){
            return Lead::select('leads.*')->where('leads.created_by', '=', \Auth::user()->creatorId())->where('leads.stage_id', '=', $this->id)->orderBy('leads.order')->get();
        }else{
            return Lead::select('leads.*')->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')->where('user_leads.user_id', '=', \Auth::user()->id)->where('leads.stage_id', '=', $this->id)->orderBy('leads.order')->get();

        }

    }

    public function leads()
    {
        // Start building the query
        $query = Lead::select('leads.*')->where('leads.stage_id', '=', $this->id);

        // Check for user type and apply the appropriate conditions
        if(\Auth::user()->type == 'company') {
            // Apply filter for 'company' type users
            $query->where('leads.created_by', '=', \Auth::user()->creatorId());
        } else {
            // Apply filter for non-company (user-based) types
            $query->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')
                  ->where('user_leads.user_id', '=', \Auth::user()->id);
        }

        // Return the query builder so additional conditions can be applied later
        return $query->orderBy('leads.order');
    }
    public function leadshierarchy()
    {
         $childrenUserIds = Hierarchy::getChildrenUserIds(\Auth::user()->type);

         $childrenUserIds[] = \Auth::user()->id;
        // dd($childrenUserIds);
        $query = Lead::select('leads.*')->distinct()->where('leads.stage_id', '=', $this->id);

        if(\Auth::user()->type == 'company') {
            // $query->where('leads.created_by', '=', \Auth::user()->creatorId());
        } else {
            $query->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')
              ->whereIn('user_leads.user_id', $childrenUserIds);
        }

        return $query->orderBy('leads.order');
    }
}
