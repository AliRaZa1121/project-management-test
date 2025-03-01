<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttributeController extends Controller
{

    public function getAttributes(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name'     => 'nullable|string',
            'type'     => 'nullable|in:text,date,number,select',
            'page'     => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }


        $attributes = Attribute::when($request->name, function ($query, $name) {
            return $query->where('name', 'like', "%$name%");
        })->when($request->type, function ($query, $type) {
            return $query->where('type', $type);
        })->paginate($request->per_page ?? 15, ['*'], 'page', $request->page ?? 1);

        return ResponseHelper::success('Attributes retrieved successfully', $attributes);
    }

    public function createAttribute(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:attributes',
            'type' => 'required|in:text,date,number,select',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        $attribute = Attribute::create($request->only(['name', 'type']));

        return ResponseHelper::success('Attribute created successfully', $attribute, 201);
    }

    public function setProjectAttributes(Request $request, $projectId)
    {
        $validator = Validator::make($request->all(), [
            'attributes'                => 'required|array',
            'attributes.*.attribute_id' => 'required|exists:attributes,id',
            'attributes.*.value'        => 'required|string',
        ]);


        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }

        $data = $request->all();
        $attributes = $data['attributes'];

        foreach ($attributes as $attribute) {
            AttributeValue::updateOrCreate(
                ['project_id' => $projectId, 'attribute_id' => $attribute['attribute_id']],
                ['value' => $attribute['value']]
            );
        }

        return ResponseHelper::success('Project attributes updated successfully');
    }

    public function getProjectAttributes($projectId)
    {
        $validator = Validator::make(['project_id' => $projectId], [
            'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed', 422, $validator->errors()->toArray());
        }


        $attributes = AttributeValue::where('project_id', $projectId)
            ->with('attribute')
            ->get();

        return ResponseHelper::success('Project attributes retrieved successfully', $attributes);
    }
}
