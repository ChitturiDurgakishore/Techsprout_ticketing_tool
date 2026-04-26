@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            @if(auth()->user()->role === 'admin') 🛠️ Admin Dashboard
            @else 📊 Dashboard
            @endif
        </h1>
        <p class="page-subtitle">Welcome back, <strong>{{ auth()->user()->name }}</strong>
            @if(auth()->user()->role === 'admin')
                <span class="badge" style="background: var(--danger-light); color: var(--danger); margin-left: 8px; font-size: 0.7rem;">ADMIN</span>
            @endif
        </p>
    </div>
    <div class="flex gap-2" style="flex-wrap: wrap;">
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('users.index') }}" class="btn btn-secondary">👥 Manage Users</a>
            <a href="{{ route('projects.create') }}" class="btn btn-secondary">🏗️ New Project</a>
        @else
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">🏗️ Browse Projects</a>
        @endif
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">➕ Create Ticket</a>
    </div>
</div>

@if(auth()->user()->role === 'admin')
{{-- ============================================================ --}}
{{-- ADMIN DASHBOARD                                             --}}
{{-- ============================================================ --}}

{{-- Row 1: Core ticket stats --}}
<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="stat-card stat-card-animate">
        <div class="flex items-center justify-between">
            <div class="stat-label">Total Tickets</div>
            <span style="font-size: 1.5rem;">🎫</span>
        </div>
        <div class="stat-number">{{ $total }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">System-wide</div>
    </div>

    <div class="stat-card stat-open stat-card-animate">
        <div class="flex items-center justify-between">
            <div class="stat-label">Open</div>
            <span style="font-size: 1.5rem;">🟡</span>
        </div>
        <div class="stat-number" style="color: var(--warning);">{{ $open }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">Awaiting action</div>
    </div>

    <div class="stat-card stat-in-progress stat-card-animate">
        <div class="flex items-center justify-between">
            <div class="stat-label">In Progress</div>
            <span style="font-size: 1.5rem;">⚙️</span>
        </div>
        <div class="stat-number" style="color: var(--accent);">{{ $inProgress }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">Being worked on</div>
    </div>

    <div class="stat-card stat-closed stat-card-animate">
        <div class="flex items-center justify-between">
            <div class="stat-label">Closed</div>
            <span style="font-size: 1.5rem;">✅</span>
        </div>
        <div class="stat-number" style="color: var(--success);">{{ $closed }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">Resolved</div>
    </div>
</div>

{{-- Row 2: System + alert stats --}}
<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <div class="stat-card stat-card-animate stat-card-critical">
        <div class="flex items-center justify-between">
            <div class="stat-label">🔴 Critical</div>
            <span style="font-size: 1.5rem;">🚨</span>
        </div>
        <div class="stat-number" style="color: var(--danger);">{{ $critical }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">Critical & open/in-progress</div>
    </div>

    <div class="stat-card stat-card-animate" style="border-left: 4px solid #8b5cf6;">
        <div class="flex items-center justify-between">
            <div class="stat-label">Total Users</div>
            <span style="font-size: 1.5rem;">👥</span>
        </div>
        <div class="stat-number" style="color: #8b5cf6;">{{ $totalUsers }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">
            <a href="{{ route('users.index') }}" style="color: #8b5cf6;">View all users →</a>
        </div>
    </div>

    <div class="stat-card stat-card-animate" style="border-left: 4px solid var(--success);">
        <div class="flex items-center justify-between">
            <div class="stat-label">Active Projects</div>
            <span style="font-size: 1.5rem;">🏗️</span>
        </div>
        <div class="stat-number" style="color: var(--success);">{{ $activeProjects }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">
            <a href="{{ route('projects.index') }}" style="color: var(--success);">View all →</a>
        </div>
    </div>

    <div class="stat-card stat-card-animate stat-card-premium" style="border-left: 4px solid var(--accent);">
        <div class="flex items-center justify-between">
            <div class="stat-label">Assigned to Me</div>
            <span style="font-size: 1.5rem;">👤</span>
        </div>
        <div class="stat-number" style="color: var(--accent);">{{ $assignedToMe }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">My active workload</div>
    </div>
</div>

{{-- Recent Tickets --}}
<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid var(--border); margin-bottom: 0;">
        <div>
            <h3 class="card-title">📋 Recent Tickets</h3>
            <p class="text-xs text-muted" style="margin-top: 4px;">Latest tickets across the entire system</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 7px 14px;">View All Tickets</a>
    </div>
    @if($recentTickets->count())
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">#ID</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Created By</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Priority</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTickets as $ticket)
                        @php
                            $rowClass = '';
                            if ($ticket->status === 'closed') {
                                $rowClass = 'row-status-closed';
                            } elseif ($ticket->priority === 'critical') {
                                $rowClass = 'row-priority-critical';
                            } elseif ($ticket->priority === 'high') {
                                $rowClass = 'row-priority-high';
                            } elseif ($ticket->priority === 'medium') {
                                $rowClass = 'row-priority-medium';
                            }
                        @endphp
                        <tr onclick="window.location.href='{{ route('tickets.show', $ticket) }}'" class="{{ $rowClass }}" style="cursor: pointer;">
                            <td style="font-weight: 700; color: var(--text-light); padding-left: 24px;">#{{ $ticket->id }}</td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">{{ Str::limit($ticket->title, 40) }}</div>
                                <span class="badge badge-type-{{ $ticket->type }}" style="font-size: 0.65rem; padding: 2px 8px;">{{ ucfirst($ticket->type) }}</span>
                            </td>
                            <td class="text-sm" style="color: var(--accent); font-weight: 600;">{{ $ticket->project->name ?? '—' }}</td>
                            <td class="text-xs text-muted">{{ $ticket->createdBy->name ?? '—' }}</td>
                            <td class="text-xs text-muted">{{ $ticket->assignedTo->name ?? '— Unassigned' }}</td>
                            <td>
                                <span class="badge badge-status-{{ $ticket->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 48px 0;">
            <div style="font-size: 2.5rem; margin-bottom: 12px;">📭</div>
            <h3 style="margin-bottom: 6px; font-size: 1rem;">No tickets yet</h3>
        </div>
    @endif
</div>

{{-- Team Workload --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid var(--border); margin-bottom: 0;">
        <div>
            <h3 class="card-title">👥 Team Workload</h3>
            <p class="text-xs text-muted" style="margin-top: 4px;">Tickets assigned to each team member</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 7px 14px;">All Users</a>
    </div>
    @if($teamWorkload->count())
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">User</th>
                        <th>Role</th>
                        <th>Total Assigned</th>
                        <th>Open</th>
                        <th>In Progress</th>
                        <th>Closed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teamWorkload as $member)
                        <tr onclick="window.location.href='{{ route('users.show', $member) }}'" style="cursor: pointer;">
                            <td style="padding-left: 24px;">
                                <div class="flex items-center gap-2">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--accent); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0;">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; font-size: 0.875rem;">{{ $member->name }}</div>
                                        <div class="text-xs text-muted">{{ $member->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background: {{ $member->role === 'admin' ? 'var(--danger-light)' : 'var(--info-light)' }}; color: {{ $member->role === 'admin' ? 'var(--danger)' : '#1e40af' }};">
                                    {{ ucfirst($member->role) }}
                                </span>
                            </td>
                            <td style="font-weight: 700; font-size: 1rem; color: var(--text-main);">{{ $member->assigned_tickets_count }}</td>
                            <td><span style="color: var(--warning); font-weight: 600;">{{ $member->open_count }}</span></td>
                            <td><span style="color: var(--accent); font-weight: 600;">{{ $member->in_progress_count }}</span></td>
                            <td><span style="color: var(--success); font-weight: 600;">{{ $member->closed_count }}</span></td>
                            <td onclick="event.stopPropagation()">
                                <a href="{{ route('users.show', $member) }}" class="btn btn-secondary" style="font-size: 0.75rem; padding: 5px 10px;">View Profile</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 48px 0;">
            <p class="text-muted">No users found.</p>
        </div>
    @endif
</div>

@else
{{-- ============================================================ --}}
{{-- NON-ADMIN DASHBOARD                                          --}}
{{-- ============================================================ --}}

{{-- Stats Cards --}}
<div class="grid grid-4">
    <div class="stat-card stat-card-animate">
        <div class="flex items-center justify-between">
            <div class="stat-label">Total Tickets</div>
            <span style="font-size: 1.25rem;">📊</span>
        </div>
        <div class="stat-number">{{ $total }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">All your tickets</div>
    </div>

    <div class="stat-card stat-open stat-card-animate">
        <div class="flex items-center justify-between">
            <div class="stat-label">Open</div>
            <span style="font-size: 1.25rem;">🟡</span>
        </div>
        <div class="stat-number" style="color: var(--warning);">{{ $open }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">Needs attention</div>
    </div>

    <div class="stat-card stat-closed stat-card-animate">
        <div class="flex items-center justify-between">
            <div class="stat-label">Closed</div>
            <span style="font-size: 1.25rem;">✅</span>
        </div>
        <div class="stat-number" style="color: var(--success);">{{ $closed }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">Resolved</div>
    </div>

    <div class="stat-card stat-card-animate stat-card-premium" style="border-left: 4px solid var(--accent);">
        <div class="flex items-center justify-between">
            <div class="stat-label">Assigned to Me</div>
            <span style="font-size: 1.25rem;">👤</span>
        </div>
        <div class="stat-number" style="color: var(--accent);">{{ $assignedToMe }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">I need to handle</div>
    </div>
</div>

<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 32px; margin-top: -12px;">
    <div class="stat-card stat-card-animate stat-card-premium" style="border-left: 4px solid var(--info);">
        <div class="flex items-center justify-between">
            <div class="stat-label">Assigned by Me</div>
            <span style="font-size: 1.25rem;">✏️</span>
        </div>
        <div class="stat-number" style="color: var(--info);">{{ $createdByMe }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">Tickets I submitted</div>
    </div>
</div>

{{-- Tickets Assigned TO Me --}}
<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid var(--border); margin-bottom: 0;">
        <div>
            <h3 class="card-title">👤 Tickets Assigned to Me</h3>
            <p class="text-xs text-muted" style="margin-top: 4px;">Tickets others have assigned for you to handle</p>
        </div>
        <a href="{{ route('tickets.index', ['my_tickets' => 1]) }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 7px 14px;">View All</a>
    </div>

    @if($ticketsAssignedToMe->count())
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">#ID</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ticketsAssignedToMe as $ticket)
                        @php
                            $rowClass = '';
                            if ($ticket->status === 'closed') {
                                $rowClass = 'row-status-closed';
                            } elseif ($ticket->priority === 'critical') {
                                $rowClass = 'row-priority-critical';
                            } elseif ($ticket->priority === 'high') {
                                $rowClass = 'row-priority-high';
                            } elseif ($ticket->priority === 'medium') {
                                $rowClass = 'row-priority-medium';
                            }
                        @endphp
                        <tr onclick="window.location.href='{{ route('tickets.show', $ticket) }}'" class="{{ $rowClass }}" style="cursor: pointer;">
                            <td style="font-weight: 700; color: var(--text-light); padding-left: 24px;">#{{ $ticket->id }}</td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">{{ Str::limit($ticket->title, 45) }}</div>
                                <span class="badge badge-type-{{ $ticket->type }}" style="font-size: 0.65rem; padding: 2px 8px;">{{ ucfirst($ticket->type) }}</span>
                            </td>
                            <td>
                                @if($ticket->project)
                                    <span style="color: var(--accent); font-weight: 600; font-size: 0.85rem;">{{ $ticket->project->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 48px 0;">
            <div style="font-size: 2.5rem; margin-bottom: 12px;">🎉</div>
            <h3 style="margin-bottom: 6px; font-size: 1rem;">No tickets assigned to you</h3>
            <p class="text-muted text-xs">You're all caught up!</p>
        </div>
    @endif
</div>

{{-- Tickets Created BY Me --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid var(--border); margin-bottom: 0;">
        <div>
            <h3 class="card-title">✏️ Tickets I Created</h3>
            <p class="text-xs text-muted" style="margin-top: 4px;">Tickets you submitted — track their progress</p>
        </div>
        <a href="{{ route('tickets.index', ['created_by_me' => 1]) }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 7px 14px;">View All</a>
    </div>

    @if($ticketsCreatedByMe->count())
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">#ID</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assigned To</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ticketsCreatedByMe as $ticket)
                        @php
                            $rowClass = '';
                            if ($ticket->status === 'closed') {
                                $rowClass = 'row-status-closed';
                            } elseif ($ticket->priority === 'critical') {
                                $rowClass = 'row-priority-critical';
                            } elseif ($ticket->priority === 'high') {
                                $rowClass = 'row-priority-high';
                            } elseif ($ticket->priority === 'medium') {
                                $rowClass = 'row-priority-medium';
                            }
                        @endphp
                        <tr onclick="window.location.href='{{ route('tickets.show', $ticket) }}'" class="{{ $rowClass }}" style="cursor: pointer;">
                            <td style="font-weight: 700; color: var(--text-light); padding-left: 24px;">#{{ $ticket->id }}</td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">{{ Str::limit($ticket->title, 45) }}</div>
                                <span class="badge badge-type-{{ $ticket->type }}" style="font-size: 0.65rem; padding: 2px 8px;">{{ ucfirst($ticket->type) }}</span>
                            </td>
                            <td>
                                @if($ticket->project)
                                    <span style="color: var(--accent); font-weight: 600; font-size: 0.85rem;">{{ $ticket->project->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-status-{{ $ticket->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                            </td>
                            <td class="text-xs text-muted">{{ $ticket->assignedTo->name ?? '— Unassigned' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 48px 0;">
            <div style="font-size: 2.5rem; margin-bottom: 12px;">📭</div>
            <h3 style="margin-bottom: 6px; font-size: 1rem;">No tickets created yet</h3>
            <p class="text-muted text-xs">Start by creating your first ticket.</p>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary" style="margin-top: 16px;">➕ Create Ticket</a>
        </div>
    @endif
</div>
@endif

@endsection
