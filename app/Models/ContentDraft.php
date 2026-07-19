<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ContentDraft extends Model
{
    protected $guarded = [];
    protected $casts = ['scheduled_for' => 'datetime', 'posted_at' => 'datetime', 'metrics' => 'array'];
    public function topic() { return $this->belongsTo(ContentTopic::class, 'content_topic_id'); }
    public function scopeStatus($q, $s) { return $q->where('status', $s); }
}
