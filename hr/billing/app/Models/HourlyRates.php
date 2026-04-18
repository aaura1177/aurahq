<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HourlyRates extends Model
{
    //
    use HasFactory;

       protected $table = 'hourly_rates';
    protected $fillable = [
        'user_id',
        'project_id',
        'h_rate',
        'm_rate',
        'date',
    ];

    protected $dates = ['effective_from'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(AdminProject::class, 'project_id');
    }


       public static function latestRateForUser(int $userId, ?int $projectId = null)
    {
        $query = self::where('user_id', $userId)
            ->whereNotNull('rate')
            ->where('rate', '>', 0);

        if ($projectId !== null) {
            $query->where('project_id', $projectId);
        }

        return $query->orderByDesc('created_at')->first();
    }
}
