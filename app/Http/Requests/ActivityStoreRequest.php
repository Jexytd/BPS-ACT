<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('user');
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'allDay' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'status' => 'required|string|in:planned,ongoing,done,cancelled,New,Confirmed,In progress,In specification,Closed',
            'category' => 'nullable|string|max:100',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'string',
            'project_id' => 'nullable|string',
            'project_name' => 'nullable|string',
        ];
    }
}
