<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['created_at' => 'datetime', 'transaction_date' => 'datetime', 'is_active' => 'boolean'];

    public function contact() {
        return $this->belongsTo(FinanceContact::class, 'finance_contact_id');
    }
    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
