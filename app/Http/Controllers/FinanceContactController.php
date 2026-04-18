<?php
namespace App\Http\Controllers;
use App\Models\FinanceContact;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FinanceContactController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view finance contacts', only: ['index', 'show']),
            new Middleware('permission:create finance contacts', only: ['create', 'store']),
            new Middleware('permission:edit finance contacts', only: ['edit', 'update', 'toggleStatus']),
            new Middleware('permission:delete finance contacts', only: ['destroy']),
        ];
    }

    public function index() {
        $query = FinanceContact::with('finances')->latest();
        if (!auth()->user()->hasRole('super-admin')) {
            $query->where('is_active', true);
        }
        $contacts = $query->get();
        return view('finance.contacts.index', compact('contacts'));
    }

    public function create() { return view('finance.contacts.create'); }

    public function store(Request $request) {
        $request->validate(['name' => 'required']);
        FinanceContact::create($request->all());
        return redirect()->route('finance-contacts.index')->with('success', 'Contact added.');
    }

    public function show(FinanceContact $financeContact) {
        if (!$financeContact->is_active && !auth()->user()->hasRole('super-admin')) abort(403);
        $query = $financeContact->finances()->latest();
        if (!auth()->user()->hasRole('super-admin')) {
            $query->where('is_active', true);
        }
        $transactions = $query->get();
        return view('finance.contacts.show', compact('financeContact', 'transactions'));
    }

    public function edit(FinanceContact $financeContact) { return view('finance.contacts.edit', compact('financeContact')); }

    public function update(Request $request, FinanceContact $financeContact) {
        $request->validate(['name' => 'required']);
        $financeContact->update($request->all());
        return redirect()->route('finance-contacts.index')->with('success', 'Contact updated.');
    }

    public function destroy(FinanceContact $financeContact) {
        if ($financeContact->finances()->exists()) return back()->with('error', 'Cannot delete contact with existing transactions.');
        $financeContact->delete();
        return redirect()->route('finance-contacts.index')->with('success', 'Contact deleted.');
    }

    public function toggleStatus(FinanceContact $financeContact) {
        $financeContact->is_active = !$financeContact->is_active;
        $financeContact->save();
        return back()->with('success', 'Contact status updated.');
    }
}
