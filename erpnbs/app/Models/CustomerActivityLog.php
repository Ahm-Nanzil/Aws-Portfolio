<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerActivityLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'customer_id',
        'log_type',
        'remark',
    ];

    private static $userData = NULL;


    public function logIcon()
    {
        $type = $this->log_type;
        $icon = '';

        if(!empty($type))
        {
            if($type == 'Move')
            {
                $icon = 'ti-arrows-maximize';
            }
            elseif($type == 'Add Product')
            {
                $icon = 'ti-layout-grid-add';
            }
            elseif($type == 'Upload File')
            {
                $icon = 'ti-cloud-upload';
            }
            elseif($type == 'Update Sources')
            {
                $icon = 'ti-brand-open-source';
            }
            elseif($type == 'create customer call')
            {
                $icon = 'ti-phone-plus';
            }
            elseif($type == 'create customer email')
            {
                $icon = 'ti-mail';
            }
        }

        return $icon;
    }

    public function getCustomerRemark()
        {
        if(self::$userData == null){
            self::$userData = self::fetchgetCustomerRemark();
        }
        return self::$userData;
        }

        public function fetchgetCustomerRemark()
        {
            $remark = json_decode($this->remark, true);
            if($remark)
            {
                $user = $this->user;

                if($user)
                {
                    $user_name = $user->name;
                }
                else
                {
                    $user_name = '';
                }

                if($this->log_type == 'Upload File')
                {
                    return $user_name . ' ' . __('Upload new file') . ' <b>' . $remark['file_name'] . '</b>';
                }
                elseif($this->log_type == 'Add Product')
                {
                    return $user_name . ' ' . __('Add new Products') . " <b>" . $remark['title'] . "</b>";
                }
                elseif($this->log_type == 'Update Sources')
                {
                    return $user_name . ' ' . __('Update Sources');
                }
                elseif($this->log_type == 'create customer call')
                {
                    return $user_name . ' ' . __('Create new Customer Call');
                }
                elseif($this->log_type == 'create customer email')
                {
                    return $user_name . ' ' . __('Create new Customer Email');
                }
                elseif($this->log_type == 'Move')
                {
                    return $user_name . " " . __('Moved the customer') . " <b>" . $remark['title'] . "</b> " . __('from') . " " . __(ucwords($remark['old_status'])) . " " . __('to') . " " . __(ucwords($remark['new_status']));
                }
            }
            else
            {
                return $this->remark;
            }
        }
}
