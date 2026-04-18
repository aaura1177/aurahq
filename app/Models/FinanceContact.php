<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceContact extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];

    public function finances() {
        return $this->hasMany(Finance::class);
    }
    public function totalGiven() {
        return $this->finances()->where('type', 'given')->where('is_active', true)->sum('amount');
    }
    public function totalReceived() {
        return $this->finances()->where('type', 'received')->where('is_active', true)->sum('amount');
    }
    public function netBalance() {
        return $this->totalReceived() - $this->totalGiven();
    }
}