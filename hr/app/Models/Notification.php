<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'employee_id',
        'user_id',
        'title',
        'message',
        'status',
        'is_read',
        'read_at',
    ];

   
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

   
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

