<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'phone_no',
        'address',
        'monthly_salary'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function attendances()
    {
        return $this->hasMany(ProjectAttandanc::class, 'user_id');
    }
    

    public function projects()
    {
        return $this->belongsToMany(AdminProject::class);
    } 


    public function assignedProjects()
{
    return $this->hasMany(ProjectUser::class, 'user_id');
}


public function projectUsers()
{
    return $this->hasMany(ProjectUser::class);
}
public function workLogs()
{
    return $this->hasMany(WorkLog::class);
}
}
