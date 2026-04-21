<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\Finance;
use App\Models\FinanceContact;
use Illuminate\Http\Request;

class FinanceApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Finance::with('contact')->latest();
        if (! $request->user()->hasRole('super-admin')) {
            $query->where('is_active', true);
        }
        $perPage = (int) $request->query('per_page', 25);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 25;
        $finances = $query->paginate($perPage);

        return ApiJson::paginated($finances, fn ($f) => $this->financeToArray($f));
    }

    public function show(Request $request, Finance $finance)
    {
        if (! $finance->is_active && ! $request->user()->hasRole('super-admin')) {
            return ApiJson::unauthorized();
        }
        $finance->load('contact');

        return ApiJson::ok($this->financeToArray($finance));
    }

    public function store(Request $request)
    {
        $request->validate($this->financeValidationRules($request, true, null));
        $data = $this->buildFinanceData($request, null);
        $data['created_by'] = $request->user()->id;
        $finance = Finance::create($data);

        return ApiJson::created(['id' => $finance->id], 'Finance transaction created successfully');
    }

    public function update(Request $request, Finance $finance)
    {
        $request->validate($this->financeValidationRules($request, false, $finance));
        $finance->update($this->buildFinanceData($request, $finance));

        return ApiJson::ok(['id' => $finance->id], 'Updated');
    }

    public function destroy(Finance $finance)
    {
        $finance->delete();

        return ApiJson::noContent();
    }

    public function toggle(Finance $finance)
    {
        $finance->is_active = ! $finance->is_active;
        $finance->save();

        return ApiJson::ok(['is_active' => $finance->is_active], 'Updated');
    }

    public function contacts()
    {
        $contacts = FinanceContact::where('is_active', true)->get(['id', 'name', 'phone', 'email']);

        return ApiJson::ok($contacts);
    }

    /**
     * @return array<string, mixed>
     */
    private function financeToArray(Finance $f): array
    {
        $remark = $f->remark;

        return [
            'id' => $f->id,
            'finance_contact_id' => $f->finance_contact_id,
            'contact' => $f->contact ? ['id' => $f->contact->id, 'name' => $f->contact->name] : null,
            'amount' => $f->amount,
            'type' => $f->type,
            'method' => $f->method,
            'category' => $f->category,
            'venture' => $f->venture ?? 'aurateria',
            'is_recurring' => (bool) $f->is_recurring,
            'recurring_day' => $f->recurring_day,
            'transaction_date' => $f->transaction_date?->format('Y-m-d H:i:s'),
            'remark' => $remark,
            'notes' => $remark,
            'is_active' => $f->is_active,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function financeValidationRules(Request $request, bool $isCreate, ?Finance $finance): array
    {
        $ventureKeys = implode(',', Finance::VENTURES);

        return [
            'finance_contact_id' => ($isCreate ? 'required' : 'sometimes').'|exists:finance_contacts,id',
            'amount' => ($isCreate ? 'required' : 'sometimes').'|numeric',
            'type' => ($isCreate ? 'required' : 'sometimes').'|in:given,received',
            'method' => ($isCreate ? 'required' : 'sometimes').'|string|max:100',
            'transaction_date' => ($isCreate ? 'required' : 'sometimes').'|date',
            'remark' => 'nullable|string',
            'notes' => 'nullable|string',
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
            'venture' => ($isCreate ? 'required' : 'sometimes').'|string|in:'.$ventureKeys,
            'is_recurring' => 'boolean',
            'recurring_day' => 'nullable|integer|min:1|max:31',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFinanceData(Request $request, ?Finance $existing): array
    {
        $remark = $request->input('remark');
        if ($remark === null && $request->has('notes')) {
            $remark = $request->input('notes');
        }

        if ($existing === null) {
            $isRecurring = $request->boolean('is_recurring');

            return [
                'finance_contact_id' => $request->input('finance_contact_id'),
                'amount' => $request->input('amount'),
                'type' => $request->input('type'),
                'method' => $request->input('method'),
                'transaction_date' => $request->input('transaction_date'),
                'remark' => $remark,
                'category' => $request->input('category'),
                'venture' => $request->input('venture', 'aurateria'),
                'is_recurring' => $isRecurring,
                'recurring_day' => $isRecurring ? $request->input('recurring_day') : null,
            ];
        }

        $isRecurring = $request->has('is_recurring')
            ? $request->boolean('is_recurring')
            : $existing->is_recurring;

        return [
            'finance_contact_id' => $request->input('finance_contact_id', $existing->finance_contact_id),
            'amount' => $request->input('amount', $existing->amount),
            'type' => $request->input('type', $existing->type),
            'method' => $request->input('method', $existing->method),
            'transaction_date' => $request->input('transaction_date', $existing->transaction_date),
            'remark' => $remark ?? $existing->remark,
            'category' => $request->has('category') ? $request->input('category') : $existing->category,
            'venture' => $request->input('venture', $existing->venture ?? 'aurateria'),
            'is_recurring' => $isRecurring,
            'recurring_day' => $isRecurring ? $request->input('recurring_day', $existing->recurring_day) : null,
        ];
    }
}

