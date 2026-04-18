<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequests;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return view('admin/project/index', compact('projects'));
    }
    public function addproject(){
        return view('admin.project.add-project');
    }
    public function createproject(AdminRequests $request)

    { 
        
        try {

            $data = $request->validated();
            // return $data;
            $project = Project::create($data);

            return redirect()->route('admin.project')->with([
                'success' => 'Project created successfully!',
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('admin.project.create')->with([
                'error' => 'Something went wrong!',
            ]);
        }
    }
    public function editproject(AdminRequests $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validated();
            $validatedData['name'] = strtoupper($validatedData['name']); 
    
       
            $existingProject = Project::where('name', $validatedData['name'])
                ->where('id', '!=', $validatedData['id'])
                ->first();
    
            if ($existingProject) {
                return redirect()->route('admin.project')->with([
                    'error' => 'Project with this name already exists!'
                ]);
            }
    
            $project = Project::findOrFail($validatedData['id']);
    
          
            $project->update([
                'name' => $validatedData['name'],
                'code' => $validatedData['code'],
                'status' => $validatedData['status'],
                'priority' => $validatedData['priority'],
                'start_date' => $validatedData['start_date'],
                'received_date' => $validatedData['received_date'],
                'client_delivery_date' => $validatedData['client_delivery_date'],
                'company_delivery_date' => $validatedData['company_delivery_date'],
                'budget' => $validatedData['budget'],
                'actual_cost' => $validatedData['actual_cost'],
                'profit_loss' => $validatedData['profit_loss'],
                'team_size' => $validatedData['team_size'],
                'project_category' => $validatedData['project_category'],
                'location' => $validatedData['location'],
                'remark' => $validatedData['remark'],
            ]);
    
            return redirect()->route('admin.project')->with([
                'success' => 'Project updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to update project: " . $e->getMessage());
            return redirect()->route('admin.project')->with([
                'error' => 'Failed to update project!'
            ]);
        }
    }
    

    public function destroy($id)
    {
        $project = Project::find($id);
    
        if (!$project) {
            return redirect()->route('admin.project')
                             ->with('error', 'Project not found.');
        }
    
        $project->delete();
    
        return redirect()->route('admin.project')
                         ->with('success', 'Project deleted successfully.');
    }
    
}
