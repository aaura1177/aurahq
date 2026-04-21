<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'transaction_date' => 'datetime',
        'is_active' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    /** @var array<string, string> */
    public const EXPENSE_CATEGORIES = [
        'salary' => 'Salary',
        'emi' => 'EMI / Loan Payment',
        'office' => 'Office Expenses',
        'subscription' => 'Subscriptions (AI, Microsoft, etc.)',
        'rent' => 'Rent',
        'medical' => 'Medical Expenses',
        'house' => 'House Expenses',
        'server' => 'Server / Hosting',
        'travel' => 'Travel',
        'misc' => 'Miscellaneous',
    ];

    /** @var array<string, string> */
    public const INCOME_CATEGORIES = [
        'client_payment' => 'Client Payment',
        'maintenance' => 'Maintenance/Retainer',
        'refund' => 'Refund',
        'other' => 'Other Income',
    ];

    public const VENTURES = ['aurateria', 'gicogifts', 'aigather', 'medical_ai', 'personal'];

    public function contact()
    {
        return $this->belongsTo(FinanceContact::class, 'finance_contact_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function categoryLabel(?string $category, string $type): string
    {
        if ($category === null || $category === '') {
            return 'Uncategorized';
        }
        if ($type === 'given') {
            return self::EXPENSE_CATEGORIES[$category] ?? ucfirst(str_replace('_', ' ', $category));
        }

        return self::INCOME_CATEGORIES[$category] ?? ucfirst(str_replace('_', ' ', $category));
    }

    public static function ventureLabel(string $venture): string
    {
        return $venture === 'medical_ai' ? 'Medical AI' : ucfirst(str_replace('_', ' ', $venture));
    }
}
