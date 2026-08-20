<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'subject',
        'project_name',
        'description',
        'start',
        'end',
        'start_date',
        'due_date',
        'all_day',
        'status',
        'category',
        'location',
        'created_by',
        'assignee_id',
        'division_id',
        'assignees',
        'result',
        'documents',
        'read_by',
        'deleted_notification_by',
    ];

    protected $casts = [
        'all_day' => 'boolean',
        'assignees' => 'array',
        'documents' => 'array',
        'read_by' => 'array',
        'deleted_notification_by' => 'array',
        'start_date' => 'date:Y-m-d',
        'due_date' => 'date:Y-m-d',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id', 'id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id', 'id');
    }
}
