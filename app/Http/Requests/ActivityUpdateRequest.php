<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('user');
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start' => 'sometimes|required|date',
            'end' => 'sometimes|required|date|after_or_equal:start',
            'allDay' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'status' => 'sometimes|required|string|in:planned,ongoing,done,cancelled,New,Confirmed,In progress,In specification,Closed',
            'category' => 'nullable|string|max:100',
            'assignees' => 'sometimes|required|array|min:1',
            'assignees.*' => 'string',
            'project_id' => 'nullable|string',
            'project_name' => 'nullable|string',
        ];
    }
}
