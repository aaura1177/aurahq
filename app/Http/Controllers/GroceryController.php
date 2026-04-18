<?php
namespace App\Http\Controllers;
use App\Models\GroceryListItem;
use App\Models\GroceryDailyTemplate;
use App\Models\GroceryExpense;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Carbon\Carbon;

class GroceryController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [
            new Middleware('permission:view grocery', only: ['index']),
            new Middleware('permission:create grocery', only: ['create', 'store', 'storeVariableExpense', 'storeTemplate', 'templates']),
            new Middleware('permission:edit grocery', only: ['edit', 'update', 'toggleStatus', 'markPurchased', 'markPending', 'editTemplate', 'updateTemplate']),
            // Added explicit delete protection
            new Middleware('permission:delete grocery', only: ['destroy', 'destroyTemplate']),
        ];
    }

    public function index(Request $request) {
        $query = GroceryListItem::latest();
        $expenseQuery = GroceryExpense::query();
        
        $week = $request->query('week');
        $type = $request->query('type', 'vegetables');

        if (!$week && !$request->has('week')) {
            $week = Carbon::now()->isoWeek; 
        }
        
        $date = Carbon::now();
        $date->setISODate($date->year, $week);
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();
        $isCurrentWeek = ($week == Carbon::now()->isoWeek);

        // SEEDING LOGIC: Uses firstOrCreate to prevent duplicates
        if ($type === 'today') {
            $query->where('type', 'today')->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])->orderBy('date', 'desc');
            $expenseQuery->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])->orderBy('date', 'desc');
            
            // Seed "Today" Templates only if we are in the current week (and checking today)
            if ($isCurrentWeek) {
                $templates = GroceryDailyTemplate::where('type', 'today')->get();
                foreach($templates as $tpl) {
                    GroceryListItem::firstOrCreate(
                        [
                            'item_name' => $tpl->item_name,
                            'type' => 'today',
                            'date' => Carbon::today()
                        ],
                        [
                            'qty' => $tpl->qty, 
                            'estimated_price' => $tpl->estimated_price,
                            'is_active' => false, 
                            'status' => 'pending'
                        ]
                    );
                }
            }
        } else {
            // Seed Category Templates (Weekly)
            if ($isCurrentWeek) {
                $templates = GroceryDailyTemplate::where('type', $type)->get();
                foreach($templates as $tpl) {
                    $exists = GroceryListItem::where('type', $type)
                        ->where('item_name', $tpl->item_name)
                        ->whereBetween('created_at', [$startOfWeek->format('Y-m-d 00:00:00'), $endOfWeek->format('Y-m-d 23:59:59')])
                        ->exists();

                    if (!$exists) {
                        GroceryListItem::create([
                            'item_name' => $tpl->item_name, 
                            'qty' => $tpl->qty, 
                            'estimated_price' => $tpl->estimated_price,
                            'type' => $type, 
                            'is_active' => false,
                            'status' => 'pending'
                        ]);
                    }
                }
            }

            $query->where('type', $type);
            $query->whereBetween('created_at', [$startOfWeek->format('Y-m-d 00:00:00'), $endOfWeek->format('Y-m-d 23:59:59')]);
            $expenseQuery->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')]);
        }

        // Hide disabled items for non-admins
        if (!auth()->user()->hasRole('super-admin')) {
             if ($type !== 'today') { $query->where('is_active', true); }
        }
        
        // FIX for duplicates: Deduplicate the results collection in memory
        $groceryItems = $query->get()->unique(function ($item) {
             $dateStr = $item->date ? Carbon::parse($item->date)->format('Y-m-d') : '';
             return $item->item_name . $item->type . $dateStr;
        });

        $totalEstimated = $groceryItems->sum('estimated_price');
        $totalActual = $groceryItems->sum('actual_cost') + $expenseQuery->sum('amount');

        $weeks = [];
        $startDate = Carbon::now()->startOfYear();
        for ($i = 1; $i <= 52; $i++) {
            $start = $startDate->copy()->setISODate($startDate->year, $i)->startOfWeek();
            $end = $start->copy()->endOfWeek();
            $label = "Week $i (" . $start->format('M d') . " - " . $end->format('M d') . ")";
            if ($i == Carbon::now()->isoWeek) $label .= " (Current)";
            $weeks[$i] = ['value' => $i, 'label' => $label];
        }

        return view('grocery.index', compact('groceryItems', 'type', 'totalEstimated', 'totalActual', 'weeks', 'week'));
    }

    public function create() { 
        return view('grocery.create', ['type' => request('type')]); 
    }

    public function store(Request $request) {
        $request->validate(['item_name' => 'required', 'qty' => 'required', 'type' => 'required']);
        $data = $request->all();
        $data['status'] = 'pending';
        if($request->type == 'today') { $data['date'] = Carbon::today(); }
        GroceryListItem::create($data);
        return redirect()->route('grocery.index', ['type' => $request->type])->with('success', 'Item added.');
    }

    public function edit(GroceryListItem $grocery) { return view('grocery.edit', compact('grocery')); }

    public function update(Request $request, GroceryListItem $grocery) {
        $request->validate(['item_name' => 'required', 'qty' => 'required', 'type' => 'required']);
        $grocery->update($request->only('item_name', 'qty', 'type', 'estimated_price', 'remark'));
        return redirect()->route('grocery.index', ['type' => $grocery->type])->with('success', 'Item updated.');
    }

    public function destroy(GroceryListItem $grocery) {
        $type = $grocery->type;
        $grocery->delete();
        return redirect()->route('grocery.index', ['type' => $type])->with('success', 'Item deleted.');
    }

    public function toggleStatus(GroceryListItem $grocery) { 
        $grocery->is_active = !$grocery->is_active;
        $grocery->save();
        return back()->with('success', 'Item status updated.');
    }

    public function markPurchased(Request $request, GroceryListItem $grocery) {
        $request->validate(['actual_cost' => 'required|numeric']);
        $grocery->status = 'purchased';
        $grocery->actual_cost = $request->actual_cost;
        $grocery->save();
        return back()->with('success', 'Item purchased.');
    }

    // New: Revert to Pending
    public function markPending(GroceryListItem $grocery) {
        $grocery->status = 'pending';
        $grocery->actual_cost = null;
        $grocery->save();
        return back()->with('success', 'Item reverted to pending.');
    }

    public function storeVariableExpense(Request $request) {
        $request->validate(['amount' => 'required|numeric', 'remark' => 'required']);
        GroceryExpense::create(['amount' => $request->amount, 'remark' => $request->remark, 'date' => Carbon::today()]);
        return back()->with('success', 'Variable expense logged.');
    }

    public function templates() {
        $templates = GroceryDailyTemplate::all();
        return view('grocery.templates', compact('templates'));
    }

    public function editTemplate(GroceryDailyTemplate $template) {
        return view('grocery.templates.edit', compact('template'));
    }

    public function storeTemplate(Request $request) {
        // Prevent duplicate templates for same type and item name
        $exists = GroceryDailyTemplate::where('type', $request->type)
            ->where('item_name', $request->item_name)
            ->exists();
        
        if ($exists) {
            return back()->with('error', 'Template item already exists for this category.');
        }

        GroceryDailyTemplate::create($request->all());
        return back()->with('success', 'Template item added.');
    }
    
    public function updateTemplate(Request $request, GroceryDailyTemplate $template) {
        $template->update($request->all());
        return redirect()->route('grocery.templates')->with('success', 'Template item updated.');
    }

    public function destroyTemplate(GroceryDailyTemplate $template) {
        $template->delete();
        return back()->with('success', 'Template item removed.');
    }
}