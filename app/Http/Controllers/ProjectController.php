<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filters'   => 'nullable|array',
            'filters.*' => 'nullable|string',
            'page'      => 'nullable|integer|min:1',
            'per_page'  => 'nullable|integer|min:1|max:100',
            'search'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        $query = Project::query();

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', "%$request->search%");
        }

        // Dynamic EAV Attribute Filters
        if ($request->has('filters')) {

            foreach ($request->filters as $key => $value) {
                $query->whereHas('attributes', function ($q) use ($key, $value) {
                    $q->with('attribute')->whereHas('attribute', function ($q) use ($key) {
                        $q->where('name', $key);
                    })
                        ->where('value', 'like', "%$value%");
                });
            }
        }

        $projects = $query->paginate($request->per_page ?? 15);

        return ResponseHelper::success('Projects retrieved successfully', $projects);
    }


    public function createProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|unique:projects',
            'status' => 'required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        $project = Project::create($request->only(['name', 'status']));

        return ResponseHelper::success('Project created successfully', $project, 201);
    }

    public function showProject($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:projects,id',
        ], [
            'id.exists' => 'Project not found',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        return ResponseHelper::success('Project retrieved successfully', Project::find($id));
    }

    public function updateProject(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:projects,id',
            'name'   => 'sometimes|string|unique:projects,name,' . $id,
            'status' => 'sometimes|string|in:active,inactive',
        ], [
            'id.exists' => 'Project not found',
            'name.unique' => 'Project name already exists',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        $project = Project::find($id);
        $project->update($request->only(['name', 'status']));

        return ResponseHelper::success('Project updated successfully', $project);
    }

    public function deleteProject($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:projects,id',
        ], [
            'id.exists' => 'Project not found',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        $project = Project::find($id);
        $project->delete();
        return ResponseHelper::success('Project deleted successfully');
    }
}
