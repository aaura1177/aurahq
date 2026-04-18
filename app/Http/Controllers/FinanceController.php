<?php
namespace App\Http\Controllers;
use App\Models\Finance;
use App\Models\FinanceContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FinanceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view finance', only: ['index', 'show']),
            new Middleware('permission:create finance', only: ['create', 'store']),
            new Middleware('permission:edit finance', only: ['edit', 'update', 'toggleStatus']),
            new Middleware('permission:delete finance', only: ['destroy']),
        ];
    }

    public function index() {
        $query = Finance::with('contact')->latest();
        if (!auth()->user()->hasRole('super-admin')) {
            $query->where('is_active', true);
        }
        $finances = $query->paginate(10);
        return view('finance.index', compact('finances'));
    }

    public function create() {
        $contacts = FinanceContact::where('is_active', true)->get();
        return view('finance.create', compact('contacts'));
    }

    public function store(Request $request) {
        $request->validate(['finance_contact_id' => 'required', 'amount' => 'required|numeric', 'type' => 'required', 'method' => 'required', 'transaction_date' => 'required|date']);
        $data = $request->except('proof');
        $data['created_by'] = auth()->id();
        if ($request->hasFile('proof')) {
            $data['proof_path'] = $request->file('proof')->store('finance_proofs', 'public');
        }
        Finance::create($data);
        return redirect()->route('finance.index')->with('success', 'Transaction added.');
    }

    public function show(Finance $finance) {
        if (!$finance->is_active && !auth()->user()->hasRole('super-admin')) abort(403);
        return view('finance.show', compact('finance'));
    }

    public function edit(Finance $finance) {
        $contacts = FinanceContact::all();
        return view('finance.edit', compact('finance', 'contacts'));
    }

    public function update(Request $request, Finance $finance) {
        $data = $request->except('proof');
        if ($request->hasFile('proof')) {
            if($finance->proof_path) Storage::disk('public')->delete($finance->proof_path);
            $data['proof_path'] = $request->file('proof')->store('finance_proofs', 'public');
        }
        $finance->update($data);
        return redirect()->route('finance.index')->with('success', 'Transaction updated.');
    }

    public function destroy(Finance $finance) {
        if($finance->proof_path) Storage::disk('public')->delete($finance->proof_path);
        $finance->delete();
        return redirect()->route('finance.index')->with('success', 'Transaction deleted.');
    }

    public function toggleStatus(Finance $finance) {
        $finance->is_active = !$finance->is_active;
        $finance->save();
        return back()->with('success', 'Transaction status updated.');
    }
}
