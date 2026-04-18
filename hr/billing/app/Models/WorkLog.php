<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkLog extends Model
{
    

    protected $table = 'work_logs';
    protected $fillable = [
        'user_id',
        'project_id',
        'working_time',
    ];

    // Relationships (optional but helpful)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(ProjectUser::class  );
    }
}
