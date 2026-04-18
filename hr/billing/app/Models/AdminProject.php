<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ProjectUser;

class AdminProject extends Model
{

    protected $table = 'projects';
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'client_name',
        'attachment',
        'user_id',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class,'project_user', 'project_id', 'user_id');
    }
    

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class, 'project_id','id');
    }
    public function assignedUsers()
{
    return $this->hasMany(ProjectUser::class, 'project_id');
}

public function projectUser()
{
    return $this->hasMany(ProjectUser::class);
}




}
