<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiJson;
use App\Models\GroceryDailyTemplate;
use App\Models\GroceryListItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GroceryApiController extends Controller
{
    public function index(Request $request)
    {
        $week = (int) $request->query('week', Carbon::now()->isoWeek);
        $type = $request->query('type', 'vegetables');
        $date = Carbon::now();
        $date->setISODate($date->year, $week);
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();
        $isCurrentWeek = $week === Carbon::now()->isoWeek;

        $query = GroceryListItem::query();
        if ($type === 'today') {
            $query->where('type', 'today')->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')]);
            if ($isCurrentWeek) {
                foreach (GroceryDailyTemplate::where('type', 'today')->get() as $tpl) {
                    GroceryListItem::firstOrCreate(
                        ['item_name' => $tpl->item_name, 'type' => 'today', 'date' => Carbon::today()],
                        ['qty' => $tpl->qty, 'estimated_price' => $tpl->estimated_price, 'is_active' => false, 'status' => 'pending']
                    );
                }
            }
        } else {
            if ($isCurrentWeek) {
                foreach (GroceryDailyTemplate::where('type', $type)->get() as $tpl) {
                    $exists = GroceryListItem::where('type', $type)->where('item_name', $tpl->item_name)
                        ->whereBetween('created_at', [$startOfWeek->format('Y-m-d 00:00:00'), $endOfWeek->format('Y-m-d 23:59:59')])->exists();
                    if (!$exists) {
                        GroceryListItem::create([
                            'item_name' => $tpl->item_name, 'qty' => $tpl->qty, 'estimated_price' => $tpl->estimated_price,
                            'type' => $type, 'is_active' => false, 'status' => 'pending',
                        ]);
                    }
                }
            }
            $query->where('type', $type)->whereBetween('created_at', [$startOfWeek->format('Y-m-d 00:00:00'), $endOfWeek->format('Y-m-d 23:59:59')]);
        }

        if (!$request->user()->hasRole('super-admin') && $type !== 'today') {
            $query->where('is_active', true);
        }

        $items = $query->latest()->get()->unique(fn ($item) => ($item->item_name.$item->type.($item->date ? $item->date->format('Y-m-d') : '')));

        return ApiJson::ok([
            'items' => $items->values()->map(fn ($i) => $this->itemJson($i))->values()->all(),
            'week' => $week,
            'type' => $type,
            'total_estimated' => $items->sum('estimated_price'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['item_name' => 'required', 'qty' => 'required', 'type' => 'required', 'estimated_price' => 'nullable|numeric']);
        $data = $request->only('item_name', 'qty', 'type', 'estimated_price', 'remark');
        $data['status'] = 'pending';
        if ($request->type === 'today') {
            $data['date'] = Carbon::today();
        }
        $item = GroceryListItem::create($data);
        return ApiJson::created($this->itemJson($item), 'Grocery item created successfully');
    }

    public function show(GroceryListItem $grocery)
    {
        return ApiJson::ok($this->itemJson($grocery));
    }

    public function update(Request $request, GroceryListItem $grocery)
    {
        $request->validate(['item_name' => 'required', 'qty' => 'required', 'type' => 'required', 'estimated_price' => 'nullable|numeric', 'remark' => 'nullable']);
        $grocery->update($request->only('item_name', 'qty', 'type', 'estimated_price', 'remark'));
        return ApiJson::ok($this->itemJson($grocery), 'Updated');
    }

    public function destroy(GroceryListItem $grocery)
    {
        $grocery->delete();

        return ApiJson::noContent();
    }

    public function toggle(GroceryListItem $grocery)
    {
        $grocery->is_active = !$grocery->is_active;
        $grocery->save();
        return ApiJson::ok(['is_active' => $grocery->is_active], 'Updated');
    }

    public function markPurchased(Request $request, GroceryListItem $grocery)
    {
        $request->validate(['actual_cost' => 'required|numeric']);
        $grocery->status = 'purchased';
        $grocery->actual_cost = $request->actual_cost;
        $grocery->save();
        return ApiJson::ok($this->itemJson($grocery), 'Updated');
    }

    public function markPending(GroceryListItem $grocery)
    {
        $grocery->status = 'pending';
        $grocery->actual_cost = null;
        $grocery->save();
        return ApiJson::ok($this->itemJson($grocery), 'Updated');
    }

    public function variableExpense(Request $request)
    {
        $request->validate(['amount' => 'required|numeric', 'remark' => 'required|string', 'date' => 'required|date']);
        $e = \App\Models\GroceryExpense::create($request->only('amount', 'remark', 'date'));
        return ApiJson::created(['id' => $e->id], 'Variable expense recorded successfully');
    }

    public function templates(Request $request)
    {
        $type = $request->query('type', 'vegetables');
        $tpls = GroceryDailyTemplate::where('type', $type)->get();
        return ApiJson::ok($tpls->map(fn ($t) => ['id' => $t->id, 'item_name' => $t->item_name, 'qty' => $t->qty, 'estimated_price' => $t->estimated_price, 'type' => $t->type])->values()->all());
    }

    public function storeTemplate(Request $request)
    {
        $request->validate(['item_name' => 'required', 'qty' => 'required', 'type' => 'required', 'estimated_price' => 'nullable|numeric']);
        $t = GroceryDailyTemplate::create($request->only('item_name', 'qty', 'type', 'estimated_price'));
        return ApiJson::created(['id' => $t->id], 'Template created successfully');
    }

    public function updateTemplate(Request $request, GroceryDailyTemplate $template)
    {
        $request->validate(['item_name' => 'required', 'qty' => 'required', 'estimated_price' => 'nullable|numeric']);
        $template->update($request->only('item_name', 'qty', 'estimated_price'));
        return ApiJson::ok(['id' => $template->id], 'Updated');
    }

    public function destroyTemplate(GroceryDailyTemplate $template)
    {
        $template->delete();

        return ApiJson::noContent();
    }

    private function itemJson(GroceryListItem $i): array
    {
        return [
            'id' => $i->id,
            'item_name' => $i->item_name,
            'qty' => $i->qty,
            'type' => $i->type,
            'estimated_price' => $i->estimated_price,
            'actual_cost' => $i->actual_cost,
            'status' => $i->status,
            'remark' => $i->remark,
            'is_active' => $i->is_active,
            'date' => $i->date?->format('Y-m-d'),
        ];
    }
}
