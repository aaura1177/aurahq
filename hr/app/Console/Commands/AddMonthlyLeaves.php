<?php



namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\LeaveBalance; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AddMonthlyLeaves extends Command
{
    protected $signature = 'leaves:add-monthly';
    protected $description = 'Add monthly casual leave to all employees';

    public function __construct()
    {
        parent::__construct();
    }

  public function handle()
{
   $employees = Employee::whereNotNull('salary')
                     ->where('salary', '!=', 0)
                      ->where('is_active', 1)
                     ->get();

    $addedCount = 0;

    foreach ($employees as $employee) {
        $employee->monthly_leave += 1;
        $employee->save();

        Log::info("Leave added for Employee ID: {$employee->id}");
        $addedCount++;
    }

    Log::info("Total employees updated: $addedCount");
}
}
