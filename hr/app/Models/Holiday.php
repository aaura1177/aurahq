<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $table = 'holidays'; 

    protected $fillable = [
        
        'name',
        'date',
        'approved_by',
        'is_active',
        'received_date',
        'color',
        'remark',
        
    ];
    
}
