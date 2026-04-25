@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">🏗️ Projects</h1>
        <p class="page-subtitle text-muted">Select a project to view and manage its tickets.</p>
    </div>
    @if(auth()->user()->role === 'admin')
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <span>➕</span> New Project
        </a>
    @endif
</div>

@if ($projects->count())
    <div class="project-grid">
        @foreach ($projects as $project)
            <a href="{{ route('projects.show', $project) }}" class="project-card">
                <div class="project-card-header">
                    <div class="project-card-icon">
                        {{ strtoupper(substr($project->name, 0, 2)) }}
                    </div>
                    @if($project->is_active)
                        <span class="badge badge-status-closed" style="text-transform: none;">Active</span>
                    @else
                        <span class="badge" style="background: var(--danger-light); color: var(--danger);">Inactive</span>
                    @endif
                </div>

                <h3 class="project-card-title">{{ $project->name }}</h3>
                <p class="project-card-desc">{{ Str::limit($project->description ?? 'No description provided.', 80) }}</p>

                <div class="project-card-stats">
                    <div class="project-stat">
                        <span class="project-stat-number">{{ $project->tickets_count }}</span>
                        <span class="project-stat-label">Total</span>
                    </div>
                    <div class="project-stat">
                        <span class="project-stat-number" style="color: var(--warning);">{{ $project->open_tickets_count }}</span>
                        <span class="project-stat-label">Open</span>
                    </div>
                    <div class="project-stat">
                        <span class="project-stat-number" style="color: var(--accent);">{{ $project->in_progress_tickets_count }}</span>
                        <span class="project-stat-label">In Progress</span>
                    </div>
                    <div class="project-stat">
                        <span class="project-stat-number" style="color: var(--success);">{{ $project->closed_tickets_count }}</span>
                        <span class="project-stat-label">Closed</span>
                    </div>
                </div>

                <div class="project-card-footer">
                    <span class="project-card-link">View Tickets →</span>
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.75rem;" onclick="event.stopPropagation()">
                            Edit
                        </a>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <div class="empty-state-icon">🏢</div>
        <h3>No Projects Yet</h3>
        <p class="text-muted">Create your first project to start tracking tickets.</p>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('projects.create') }}" class="btn btn-primary" style="margin-top: 16px;">Create Project</a>
        @endif
    </div>
@endif

@endsection
