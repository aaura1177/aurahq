<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ContentTopic extends Model
{
    protected $guarded = [];
    protected $casts = ['used_at' => 'datetime', 'is_active' => 'boolean'];

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)->where('status', 'available');
    }
}
