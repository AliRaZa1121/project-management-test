<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class TimesheetController extends Controller
{
    public function getProjectTimesheets(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_name'  => 'nullable|string',
            'date'       => 'nullable|date',
            'hours'      => 'nullable|numeric',
            'user_id'    => 'nullable|integer|exists:users,id',
            'project_id' => 'required|integer|exists:projects,id',
            'page'       => 'nullable|integer|min:1',
            'per_page'   => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        $query = Timesheet::query()
            ->when($request->task_name, fn($q, $task_name) => $q->where('task_name', 'like', "%$task_name%"))
            ->when($request->date, fn($q, $date) => $q->where('date', $date))
            ->when($request->hours, fn($q, $hours) => $q->where('hours', $hours))
            ->when($request->user_id, fn($q, $user_id) => $q->where('user_id', $user_id))
            ->where('project_id', $request->project_id);

        $timesheets = $query->paginate($request->get('per_page', 15));

        return ResponseHelper::success('Timesheets retrieved successfully', $timesheets);
    }

    public function createProjectTimesheet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_name' => 'required|string',
            'date'      => 'required|date',
            'hours'     => 'required|numeric|min:0.1',
            'user_id'   => 'required|integer|exists:users,id',
            'project_id' => 'required|integer|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        $timesheet = Timesheet::create($request->all());
        return ResponseHelper::success('Timesheet created successfully', $timesheet, 201);
    }

    public function showProjectTimesheet(Request $request, $timesheetId)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        $timesheet = Timesheet::where('project_id', $request->project_id)->find($timesheetId);

        if (!$timesheet) {
            return ResponseHelper::error('Timesheet not found', 404);
        }

        return ResponseHelper::success('Timesheet retrieved successfully', $timesheet);
    }

    public function updateProjectTimesheet(Request $request, $timesheetId)
    {

        $validator = Validator::make($request->all(), [
            'task_name' => 'sometimes|string',
            'date'      => 'sometimes|date',
            'hours'     => 'sometimes|numeric|min:0.1',
            'user_id'   => 'required|integer|exists:users,id',
            'project_id' => 'required|integer|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        $timesheet = Timesheet::where('project_id', $request->project_id)->find($timesheetId);

        if (!$timesheet) {
            return ResponseHelper::error('Timesheet not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'task_name' => 'sometimes|string',
            'date'      => 'sometimes|date',
            'hours'     => 'sometimes|numeric|min:0.1',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        $timesheet->update($request->only(['task_name', 'date', 'hours']));
        return ResponseHelper::success('Timesheet updated successfully', $timesheet);
    }

    public function deleteProjectTimesheet($timesheetId)
    {
        $timesheet = Timesheet::find($timesheetId);

        if (!$timesheet) {
            return ResponseHelper::error('Timesheet not found', 404);
        }

        $timesheet->delete();
        return ResponseHelper::success('Timesheet deleted successfully');
    }
}
