<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPayment extends Model
{
    //

    use HasFactory;

    protected $table = 'project_user_payments';
    protected $fillable = [
        'user_id',
        'project_id',
        'amount_paid',
        'payment_date',
        'payment_note',
        'screenshot',
        'hourly_rate_at_payment',
        'minutes_paid',
        'pending_amount',
    ];


        public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function project()
    {
        return $this->belongsTo(AdminProject::class ,'project_id');
    }
}
