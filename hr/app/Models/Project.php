<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects'; 

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'client_name',
        'start_date',
        'received_date',
        'client_delivery_date',
        'company_delivery_date',
        'status',
        'priority',
        'budget',
        'actual_cost',
        'profit_loss',
        'team_size',
        'project_category',
        'location',
        'remark'
    ];
}
