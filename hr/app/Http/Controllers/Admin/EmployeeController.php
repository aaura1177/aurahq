<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Exception;
use App\Models\Employee;
use App\Models\Counter;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;



use App\Helpers\Admin\CounterHelper;


class EmployeeController extends Controller
{

    public function index()
    {
        $employee = Employee::all();
        return view('admin.employee.index', compact('employee'));
    }
    public function addemployee()
    {

        $counter = Counter::where('counter_name', 'emp_id')->first(['prefix', 'count']);
        $depts = Department::all();
        $empid = $counter ? $counter->prefix . '/' . $counter->count : '';

        return view('admin.employee.add-employee', compact('empid', 'depts'));
    }


    public function createemployee(AdminRequests $request)
    {
        try {
             $validatedData = $request->validated();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $validatedData['user_id'] = Auth::check() ? Auth::user()->id : null;
            
            $validatedData['notice_period'] = $request->notice_period ?? 0; 
                        
            $validatedData['increment_date'] = $request->increment_date; 


            if ($request->hasFile('resume')) {
                $resumePath = $request->file('resume')->store('employee', 'public');
                $validatedData['resume'] = $resumePath;
            }
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('employee_images', 'public'); // Store image in a folder called 'employee_images'
                $validatedData['image'] = $imagePath;
            }

            CounterHelper::generateCode('emp_id');
            $employee = Employee::create($validatedData);
            return redirect()->route('admin.employee')->with([
                'success' => 'Employee added successfully',
                'data' => $employee
            ]);
        } catch (QueryException $e) {
         

            return redirect()->back()->with([
                'error' => $e->getMessage(),
                'message' => 'Database error occurred!',
            ]);



        } catch (Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage(),
                'message' => 'Database error occurred!',
            ]);

        }
    }


    public function editemployee($id)
    {

        $employee = Employee::where('id', $id)->first();
        $depts = Department::all();

        if (!$employee) {
            return response()->json([
                'error' => 'EMployee NOT found',
            ]);
        }
        return view('admin.employee.edit-employee', compact('employee','depts'));
    }

    public function editpostemployee(AdminRequests $request)
    {
        try {
            $validatedData = $request->validated();

            $employee = Employee::find($validatedData['id']);
            if ($employee) {
                if ($request->hasFile('image')) {
                    if ($employee->image) {
                        Storage::disk('public')->delete($employee->image);
                    }
    
                    $imagePath = $request->file('image')->store('employee_images', 'public');
                    $validatedData['image'] = $imagePath;
                }
            if ($employee) {
                $employee->update($validatedData);

                return redirect()->route('admin.employee')->with('success', 'Employee updated successfully');

            } else {
                return redirect()->route('admin.employee.edit.post')->with('error', 'Employee not found');

            }
        }
        } catch (QueryException $e) {
            return redirect()->route('admin.employee.edit.post')->with('error', 'Database error occurred!');

        } catch (Exception $e) {
            return redirect()->route('admin.employee.edit.post')->with('error', 'Something went wrong!');

        }
    }

    function emp_id()
    {

        $counter = Counter::where('counter_name', 'emp_id')->select('prefix', 'count')->first();
        $empcode = trim($counter->prefix . '/' . $counter->count);
        return response()->json(['empid' => $empcode]);
    }
}
