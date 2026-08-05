<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $fillable = [
        'name','pipeline_id','created_by','order'
    ];

    public function deals(){
        if(\Auth::user()->type == 'client'){
            return Deal::select('deals.*')
            ->join('client_deals','client_deals.deal_id','=','deals.id')
            ->where('client_deals.client_id', '=', \Auth::user()->id)
            ->where('deals.stage_id', '=', $this->id)->orderBy('deals.order')->get();
        }else {
            return Deal::select('deals.*')
            ->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')
            ->where('user_deals.user_id', '=', \Auth::user()->id)
            ->where('deals.stage_id', '=', $this->id)
            ->orderBy('deals.order')->get();
        }
    }

    public function dealshierarchy(){
        $childrenUserIds = Hierarchy::getChildrenUserIds(\Auth::user()->type);

         $childrenUserIds[] = \Auth::user()->id;
        if(\Auth::user()->type == 'client'){
            return Deal::select('deals.*')
            ->join('client_deals','client_deals.deal_id','=','deals.id')
            ->where('client_deals.client_id', '=', \Auth::user()->id)
            ->where('deals.stage_id', '=', $this->id)->orderBy('deals.order')->get();
        }else {
            return Deal::select('deals.*')
            ->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')
            ->whereIn('user_deals.user_id', $childrenUserIds)
            ->where('deals.stage_id', '=', $this->id)
            ->orderBy('deals.order')->get();
        }
    }


}
