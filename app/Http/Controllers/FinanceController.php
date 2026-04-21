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

    public function store(Request $request)
    {
        $request->validate($this->financeRules($request, true, null));
        $data = $request->except('proof');
        $data['created_by'] = auth()->id();
        $data['venture'] = $request->input('venture', 'aurateria');
        $data['is_recurring'] = $request->boolean('is_recurring');
        $data['recurring_day'] = $data['is_recurring'] ? $request->input('recurring_day') : null;
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

    public function update(Request $request, Finance $finance)
    {
        $request->validate($this->financeRules($request, false, $finance));
        $data = $request->except('proof');
        $data['venture'] = $request->input('venture', $finance->venture ?? 'aurateria');
        $data['is_recurring'] = $request->boolean('is_recurring');
        $data['recurring_day'] = $data['is_recurring'] ? $request->input('recurring_day') : null;
        if ($request->hasFile('proof')) {
            if ($finance->proof_path) {
                Storage::disk('public')->delete($finance->proof_path);
            }
            $data['proof_path'] = $request->file('proof')->store('finance_proofs', 'public');
        }
        $finance->update($data);
        return redirect()->route('finance.index')->with('success', 'Transaction updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function financeRules(Request $request, bool $isCreate, ?Finance $finance): array
    {
        $ventureKeys = implode(',', Finance::VENTURES);

        return [
            'finance_contact_id' => ($isCreate ? 'required' : 'sometimes').'|exists:finance_contacts,id',
            'amount' => ($isCreate ? 'required' : 'sometimes').'|numeric',
            'type' => ($isCreate ? 'required' : 'sometimes').'|in:given,received',
            'method' => ($isCreate ? 'required' : 'sometimes').'|string|max:100',
            'transaction_date' => ($isCreate ? 'required' : 'sometimes').'|date',
            'remark' => 'nullable|string',
            'category' => [
                'nullable',
                'string',
                'max:100',
                function (string $attribute, mixed $value, \Closure $fail) use ($request, $finance): void {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $type = $request->input('type') ?? $finance?->type ?? 'given';
                    $allowed = $type === 'given'
                        ? array_keys(Finance::EXPENSE_CATEGORIES)
                        : array_keys(Finance::INCOME_CATEGORIES);
                    if (! in_array($value, $allowed, true)) {
                        $fail('The category does not match the transaction type.');
                    }
                },
            ],
            'venture' => 'required|string|in:'.$ventureKeys,
            'is_recurring' => 'boolean',
            'recurring_day' => 'nullable|integer|min:1|max:31',
        ];
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
