<?php

namespace App\Models\WebSetup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarNav extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'ams_web_sidebar_menu';

    protected $fillable = [
        'id',
        'uid',
        'parent_id',
        'name',
        'icon',
        'url',
        'order',
        'is_collapsed',
        'is_heading',
        'status',
        'create_by',
        'create_date',
        'update_by',
        'update_date',
    ];

    public function children()
    {
        return $this->hasMany(SidebarNav::class, 'parent_id')
            ->where('status', 'A')  // Apply the status filter here
            ->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(SidebarNav::class, 'parent_id');
    }
}
