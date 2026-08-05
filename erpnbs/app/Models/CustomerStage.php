<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerStage extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'pipeline_id',
        'created_by',
        'order',
    ];

    /**
     * Get the customers associated with this stage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        $childrenUserIds = Hierarchy::getChildrenUserIds(\Auth::user()->type);
        $childrenUserIds[] = \Auth::user()->id;

        if (\Auth::user()->type === 'company') {
            return \App\Models\Customer::where('stage_id', $this->id)
                ->get();
        } else {
            return \App\Models\Customer::where('stage_id', $this->id)
                ->whereIn('created_by', $childrenUserIds)
                ->get();
        }
    }

}
