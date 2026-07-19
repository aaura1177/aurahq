<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'is_active' => 'boolean',
        'due_date' => 'date',
        'is_starred' => 'boolean',
    ];

    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function reports() { return $this->hasMany(TaskReport::class)->latest(); }
    public function todos() { return $this->hasMany(TaskTodo::class)->latest(); }

    public function scopeOpen($query) { return $query->whereNotIn('status', ['completed', 'reviewed', 'archived']); }
    public function scopeDone($query) { return $query->whereIn('status', ['completed', 'reviewed']); }
    public function scopeStarred($query) { return $query->where('is_starred', true); }
    public function scopePersonal($query) { return $query->where('category', 'admin_personal'); }
    public function scopeAssigned($query) { return $query->where('category', 'employee_assignment'); }

    public function isDone(): bool { return in_array($this->status, ['completed', 'reviewed']); }
}
