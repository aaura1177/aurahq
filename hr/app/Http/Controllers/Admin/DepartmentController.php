<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequests;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Admin\CounterHelper;




class DepartmentController extends Controller
{

    public function index(){
           $department = Department::withCount('employees')->get();
        return view('admin.department.index',compact('department'));
    }
    public function adddepartment(){
        $counter = Counter::where('counter_name', 'it')->first(['prefix', 'count']);
         $departmetcode = $counter ? $counter->prefix . '/' . $counter->count : '';
        return view('admin.department.add-department',compact('departmetcode'));
    }
    public function createdepartment(AdminRequests $request)
    {
        try {
           
            $validatedData = $request->validated();
    
           
            $validatedData['name'] = strtoupper($validatedData['name']);
            
           
            $existingDepartment = Department::where('name', $validatedData['name'])->first();
            
           
            if ($existingDepartment) {
                return redirect()->route('admin.department')->with([
                    'error' => 'Department already exists!'
                ]);
            }
    
           
            $department = Department::create([
                'name' => $validatedData['name'],
                'code' => $validatedData['code'],
                'description' => $validatedData['description'] ?? null,
                'user_id' => Auth::check() ? Auth::user()->id : null, 
            ]);
    
           
            CounterHelper::generateCode('it');
            
           
            return redirect()->route('admin.department')->with([
                'success' => 'Department created successfully!',
            ]);
    
        } catch (\Exception $e) {
           
            return redirect()->route('admin.department')->with([
                'error' => 'Failed to create department!',
                'exception' => $e->getMessage()
            ]);
        }
    }
    public function editdepartment(AdminRequests $request)
{
    try {
        // Validate the data
        $validatedData = $request->validated();
        $validatedData['name'] = strtoupper($validatedData['name']);
        
        // Check if the department name already exists (except for the current department)
        $existingDepartment = Department::where('name', $validatedData['name'])
            ->where('id', '!=', $validatedData['id']) 
            ->first();
        
        if ($existingDepartment) {
            return redirect()->route('admin.department')->with([
                'error' => 'Department already exists!'
            ]);
        }

        // Find the department by ID
        $department = Department::findOrFail($validatedData['id']);
        
        // Only update name and description, don't touch other fields
        $department->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
        ]);

        return redirect()->route('admin.department')->with([
            'success' => 'Department updated successfully!'
        ]);

    } catch (\Exception $e) {
        \Log::error("Failed to update department: " . $e->getMessage());
        return redirect()->route('admin.department')->with([
            'error' => 'Failed to update department!'
        ]);
    }
}


public function destroy($id)
{
    $department = Department::find($id);

    if (!$department) {
        return redirect()->route('admin.department')
                         ->with('error', 'Department not found.');
    }

    $department->delete();

    return redirect()->route('admin.department')
                     ->with('success', 'Department deleted successfully.');
}
}
