<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'title',
        'type',
        'description',
        'page_url',
        'screenshot',
        'priority',
        'status',
        'project_id',
        'department_id',
        'created_by',
        'assigned_to'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }
}
