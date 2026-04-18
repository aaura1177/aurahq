<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequests;
use App\Models\Counter;
use Illuminate\Http\Request;

class CounterController extends Controller
{


    public function index()
    {

        $counters = Counter::all();
        return  view('admin.counter.index', compact('counters'));
    }

    public function addcounter()
    {
        $counter = Counter::where('counter_name', 'it')->first(['prefix', 'count']);
        $cod = $counter ? $counter->prefix . '/' . $counter->count : '';
        return  view('admin.counter.add-counter',compact('cod'));
    }

    public function createcounter(AdminRequests $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['count']  =  1001;
            $counter = Counter::create($validatedData);
            return redirect()->route('admin.counter')->with('success', 'Counter added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }



    function editcounter(AdminRequests $request)
    {

        try {
            $validatedData = $request->validated();
            $counter = Counter::findOrFail($validatedData['id']);
            $counter->update([
                'counter_name' => $validatedData['counter_name'] ?? $counter->counter_name,
                'prefix' => $validatedData['prefix'] ?? $counter->prefix,
            ]);
            return redirect()->route('admin.counter')->with('success', 'Counter added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        $counter = Counter::find($id);
    
        if (!$counter) {
            return redirect()->route('admin.counter')
                             ->with('error', 'Counter not found.');
        }
    
        $counter->delete();
    
        return redirect()->route('admin.counter')
                         ->with('success', 'Counter deleted successfully.');
    }
}
