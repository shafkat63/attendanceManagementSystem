<?php

namespace App\Models\UserConfig;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchInfo extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'sms_branch_info';

    protected $fillable = [
        'id','uid', 'name', 'phone', 'email', 'address', 'status','create_by','create_date','update_by','update_date',
    ];

}
