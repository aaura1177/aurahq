<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectUser;
use App\Models\ProjectAttandanc;
use App\Models\UserPayment;
use App\Models\HourlyRates;
class RecevedController extends Controller
{



    public function index($user_id, $project_id)
{
    $payments = UserPayment::where('project_id', $project_id)
        ->where('user_id', $user_id)
        ->with('project')
        ->orderByDesc('id')
        ->get();


    $projectUser = ProjectUser::where('user_id', $user_id)
        ->where('project_id', $project_id)
        ->first();

    $pendingAmount = $projectUser->pending_amount ?? 0;

    return view('admin.admin_receved.index', compact(
        'payments',
        'project_id',
        'user_id',
        'pendingAmount'
    ));
}


public function showadd($user_id, $project_id)
{
    
   
    $projectUser = DB::table('project_user')
        ->where('user_id', $user_id)
        ->where('project_id', $project_id)
        ->first();

    if (!$projectUser) {
        return redirect()->back()->with('error', 'Project-user mapping not found.');
    }

    $projectTitle = DB::table('projects')
        ->where('id', $project_id)
        ->value('title') ?? 'Unknown Project';

    
    $totalAmount = $projectUser->total_amount ?? 0;
    $pendingAmount = $projectUser->pending_amount ?? 0;
    $paidAmount = $projectUser->total_paid_amount ?? 0;


   $totalSeconds = ProjectAttandanc::where('user_id', $user_id)
        ->where('project_id', $project_id)
        ->selectRaw('SUM(TIME_TO_SEC(total_minutes)) as total_seconds')
        ->value('total_seconds');

    $totalSeconds = (int) $totalSeconds;

    
    $formattedTime = gmdate('H:i:s', $totalSeconds);


    
    return view('admin.admin_receved.add_receved', compact(
        'user_id',
        'project_id',
        'projectTitle',
        'totalAmount',
        'pendingAmount',
        'paidAmount',
        'formattedTime',
        // 'latestRate'
    ));
}


public function store(Request $request, $user_id)
{
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
        'paid_amount' => 'required|numeric|min:0',
        'payment_date' => 'required|date',
    ]);

    $projectId = $validated['project_id'];
    $paidAmount = $validated['paid_amount'];
    $paymentDate = $validated['payment_date'];

    $rateEntries = ProjectUser::where('user_id', $user_id)
        ->where('project_id', $projectId)
        ->orderBy('created_at') 
        ->get();

    if ($rateEntries->isEmpty()) {
        return back()->with('error', 'No project-user rate entries found.');
    }

    $remainingAmount = $paidAmount;

    foreach ($rateEntries as $entry) {
        if ($entry->pending_amount > 0) {
            if ($remainingAmount < $entry->pending_amount) {
                $entry->pending_amount -= $remainingAmount;
                $entry->total_paid_amount += $remainingAmount;
                $entry->save();

                UserPayment::create([
                    'user_id'     => $user_id,
                    'project_id'  => $projectId,
                    'amount_paid' => $remainingAmount,
                    'payment_date'=> $paymentDate,
                ]);

                return back()->with('success', 'Partial payment recorded for rate ₹' . $entry->rate);
            } else {
                $remainingAmount -= $entry->pending_amount;

                UserPayment::create([
                    'user_id'     => $user_id,
                    'project_id'  => $projectId,
                    'amount_paid' => $entry->pending_amount,
                    'payment_date'=> $paymentDate,
                ]);

                $entry->total_paid_amount += $entry->pending_amount;
                $entry->pending_amount = 0;
                $entry->save();
            }
        } elseif ($entry->pending_amount == 0 && $entry !== $rateEntries->last() && $remainingAmount > 0) {
            continue;
        } elseif ($entry->pending_amount > 0) {
            return back()->with('error', 'Please clear pending amount for rate ₹' . $entry->rate . ' first.');
        }
    }
    if ($remainingAmount > 0) {
        return back()->with('warning', 'Extra amount not used. All known rates are already cleared.');
    }
    return back()->with('success', 'Payment processed successfully.');
}





public function viewpayment($user_id, $project_id){

        $payments = UserPayment::where('project_id',$project_id)
       ->where('user_id', $user_id)->with('project')
       ->get();
       $totalWorkingTime = ProjectAttandanc::where('user_id', $user_id)
        ->where('project_id', $project_id)
        ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(total_minutes))) as total_minutes'))
        ->value('total_minutes'); 
      return view('admin.admin_receved.payment_view' , compact('payments', 'project_id','user_id',  'totalWorkingTime'));
}


 
public function deleteView($id)
{
    $projectUser = UserPayment::findOrFail($id);
    $projectUser->delete();
    return redirect()->back()->with('success', 'Project user deleted successfully.');
}


public function deleteProjectUser($id)
{
    $projectUser = UserPayment::findOrFail($id);
    $projectUser->delete();

    return redirect()->back()->with('success', 'Project user deleted successfully.');
}

}
