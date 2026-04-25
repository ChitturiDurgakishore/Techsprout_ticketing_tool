@extends('layouts.app')

@section('title', $project->name . ' — Project')

@section('content')

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('projects.index') }}">🏗️ Projects</a>
    <span>/</span>
    <span class="breadcrumb-current">{{ $project->name }}</span>
</div>

{{-- Project Hero Header --}}
<div class="project-hero">
    <div class="project-hero-left">
        <div class="project-icon-large">
            {{ strtoupper(substr($project->name, 0, 2)) }}
        </div>
        <div>
            <h1 class="project-hero-title">{{ $project->name }}</h1>
            <p class="project-hero-desc">{{ $project->description ?? 'No description provided.' }}</p>
            <div class="flex items-center gap-2" style="margin-top: 8px;">
                @if($project->is_active)
                    <span class="badge badge-status-closed" style="text-transform: none;">● Active</span>
                @else
                    <span class="badge" style="background: var(--danger-light); color: var(--danger);">○ Inactive</span>
                @endif
                <span class="text-xs text-muted">Created {{ $project->created_at->format('M d, Y') }}</span>
            </div>
        </div>
    </div>
    <div class="project-hero-actions">
        <a href="{{ route('tickets.create', ['project_id' => $project->id, 'from_project' => $project->id]) }}" class="btn btn-primary">
            <span>➕</span> Create Ticket
        </a>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary">
                ✏️ Edit Project
            </a>
        @endif
    </div>
</div>

{{-- Stats Row --}}
<div class="grid grid-4" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-label">Total Tickets</div>
        <div class="stat-number">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-card stat-open">
        <div class="stat-label">Open</div>
        <div class="stat-number" style="color: var(--warning);">{{ $stats['open'] }}</div>
    </div>
    <div class="stat-card stat-in-progress">
        <div class="stat-label">In Progress</div>
        <div class="stat-number" style="color: var(--accent);">{{ $stats['in_progress'] }}</div>
    </div>
    <div class="stat-card stat-closed">
        <div class="stat-label">Closed</div>
        <div class="stat-number" style="color: var(--success);">{{ $stats['closed'] }}</div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card" style="padding: 16px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('projects.show', $project) }}" id="filter-form">
        {{-- Preserve toggle section for non-admin --}}
        @if(auth()->user()->role !== 'admin')
            <input type="hidden" name="section" value="{{ $section }}">
        @endif
        <div class="flex items-center gap-4 flex-wrap">
            <div style="flex: 1; min-width: 200px; position: relative;">
                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-light);">🔍</span>
                <input type="text" name="search" placeholder="Search tickets in this project..." value="{{ request('search') }}" style="padding-left: 36px;">
            </div>
            <select name="status" onchange="this.form.submit()" style="width: auto;">
                <option value="">All Status</option>
                <option value="open"        {{ request('status') === 'open'        ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="review"      {{ request('status') === 'review'      ? 'selected' : '' }}>Review</option>
                <option value="closed"      {{ request('status') === 'closed'      ? 'selected' : '' }}>Closed</option>
            </select>
            <select name="priority" onchange="this.form.submit()" style="width: auto;">
                <option value="">All Priority</option>
                <option value="low"      {{ request('priority') === 'low'      ? 'selected' : '' }}>Low</option>
                <option value="medium"   {{ request('priority') === 'medium'   ? 'selected' : '' }}>Medium</option>
                <option value="high"     {{ request('priority') === 'high'     ? 'selected' : '' }}>High</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-secondary">Filter</button>
                @if(request()->anyFilled(['search', 'status', 'priority']))
                    <a href="{{ route('projects.show', array_merge([$project->id], auth()->user()->role !== 'admin' ? ['section' => $section] : [])) }}" class="btn btn-danger" style="padding: 10px;">✕</a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- ============================================================ --}}
{{-- ADMIN: Single unified list                                   --}}
{{-- ============================================================ --}}
@if(auth()->user()->role === 'admin')

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header" style="padding: 16px 24px; border-bottom: 1px solid var(--border); margin-bottom: 0;">
        <h3 class="card-title">All Tickets <span style="color: var(--text-muted); font-weight: 400; font-size: 0.85rem;">({{ $tickets->total() }})</span></h3>
    </div>
    @if($tickets->count())
        @include('projects._ticket_table', ['ticketList' => $tickets, 'showCreatedBy' => true])
        @if($tickets->hasPages())
            <div style="padding: 20px 24px; border-top: 1px solid var(--border);">
                {{ $tickets->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-state-icon">🎫</div>
            <h3>No Tickets Found</h3>
            <p class="text-muted">{{ request()->anyFilled(['search','status','priority']) ? 'Try adjusting your filters.' : 'No tickets in this project yet.' }}</p>
            <a href="{{ route('tickets.create', ['project_id' => $project->id, 'from_project' => $project->id]) }}" class="btn btn-primary" style="margin-top: 16px;">Create First Ticket</a>
        </div>
    @endif
</div>

@else
{{-- ============================================================ --}}
{{-- NON-ADMIN: Segmented Toggle                                  --}}
{{-- ============================================================ --}}

<div class="card" style="padding: 0; overflow: hidden;">

    {{-- Segmented Toggle Header --}}
    <div class="toggle-header">
        <div class="toggle-group">
            <a href="{{ route('projects.show', array_merge([$project->id], request()->except('section', 'assigned_page', 'created_page'), ['section' => 'assigned'])) }}"
               class="toggle-btn {{ $section === 'assigned' ? 'toggle-btn-active' : '' }}">
                <span>👤</span>
                <span>Assigned to Me</span>
                <span class="toggle-pill">{{ $assignedTickets->total() }}</span>
            </a>
            <a href="{{ route('projects.show', array_merge([$project->id], request()->except('section', 'assigned_page', 'created_page'), ['section' => 'created'])) }}"
               class="toggle-btn {{ $section === 'created' ? 'toggle-btn-active' : '' }}">
                <span>✏️</span>
                <span>Assigned by Me</span>
                <span class="toggle-pill">{{ $createdTickets->total() }}</span>
            </a>
        </div>
        <a href="{{ route('tickets.create', ['project_id' => $project->id, 'from_project' => $project->id]) }}"
           class="btn btn-primary" style="font-size: 0.8rem; padding: 8px 14px;">➕ New Ticket</a>
    </div>

    {{-- Section: Assigned to Me --}}
    @if($section === 'assigned')
        @if($assignedTickets->count())
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="padding-left: 24px; width: 72px;">#ID</th>
                            <th>Ticket Details</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignedTickets as $ticket)
                            <tr onclick="window.location.href='{{ route('tickets.show', $ticket) }}'" style="cursor: pointer;">
                                <td style="font-weight: 700; color: var(--text-light); padding-left: 24px;">#{{ $ticket->id }}</td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-main); margin-bottom: 4px;">{{ Str::limit($ticket->title, 55) }}</div>
                                    <div class="flex items-center gap-2">
                                        <span class="badge badge-type-{{ $ticket->type }}" style="font-size: 0.65rem; padding: 2px 8px;">{{ ucfirst($ticket->type) }}</span>
                                        @if($ticket->screenshot)
                                            <span style="font-size: 0.7rem; color: var(--text-light);">📎</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-status-{{ $ticket->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                                </td>
                                <td class="text-xs text-muted">{{ $ticket->createdBy->name ?? '—' }}</td>
                                <td class="text-xs text-muted">{{ $ticket->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($assignedTickets->hasPages())
                <div style="padding: 20px 24px; border-top: 1px solid var(--border);">
                    {{ $assignedTickets->links() }}
                </div>
            @endif
        @else
            <div class="toggle-empty">
                <div style="font-size: 2.5rem; margin-bottom: 12px;">🎉</div>
                <h3 style="margin-bottom: 6px; font-size: 1rem;">No tickets assigned to you in this project</h3>
                <p class="text-muted text-xs">You have no pending tickets here.</p>
            </div>
        @endif
    @endif

    {{-- Section: Created/Assigned by Me --}}
    @if($section === 'created')
        @if($createdTickets->count())
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="padding-left: 24px; width: 72px;">#ID</th>
                            <th>Ticket Details</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Assigned To</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($createdTickets as $ticket)
                            <tr onclick="window.location.href='{{ route('tickets.show', $ticket) }}'" style="cursor: pointer;">
                                <td style="font-weight: 700; color: var(--text-light); padding-left: 24px;">#{{ $ticket->id }}</td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-main); margin-bottom: 4px;">{{ Str::limit($ticket->title, 55) }}</div>
                                    <div class="flex items-center gap-2">
                                        <span class="badge badge-type-{{ $ticket->type }}" style="font-size: 0.65rem; padding: 2px 8px;">{{ ucfirst($ticket->type) }}</span>
                                        @if($ticket->screenshot)
                                            <span style="font-size: 0.7rem; color: var(--text-light);">📎</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-status-{{ $ticket->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="avatar-xs">{{ strtoupper(substr($ticket->assignedTo->name ?? '?', 0, 1)) }}</div>
                                        <span class="text-sm">{{ $ticket->assignedTo->name ?? 'Unassigned' }}</span>
                                    </div>
                                </td>
                                <td class="text-xs text-muted">{{ $ticket->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($createdTickets->hasPages())
                <div style="padding: 20px 24px; border-top: 1px solid var(--border);">
                    {{ $createdTickets->links() }}
                </div>
            @endif
        @else
            <div class="toggle-empty">
                <div style="font-size: 2.5rem; margin-bottom: 12px;">📭</div>
                <h3 style="margin-bottom: 6px; font-size: 1rem;">No tickets created by you in this project</h3>
                <p class="text-muted text-xs">Start by creating a ticket in this project.</p>
                <a href="{{ route('tickets.create', ['project_id' => $project->id, 'from_project' => $project->id]) }}"
                   class="btn btn-primary" style="margin-top: 16px;">➕ Create Ticket</a>
            </div>
        @endif
    @endif

</div>
@endif

@endsection
