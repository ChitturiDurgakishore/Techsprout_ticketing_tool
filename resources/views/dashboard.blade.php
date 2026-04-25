@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">📊 Dashboard</h1>
        <p class="page-subtitle">Welcome back, <strong>{{ auth()->user()->name }}</strong></p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">🏗️ Browse Projects</a>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">➕ Create Ticket</a>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-4">
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div class="stat-label">Total Tickets</div>
            <span style="font-size: 1.25rem;">📊</span>
        </div>
        <div class="stat-number">{{ $total }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">All your tickets</div>
    </div>

    <div class="stat-card stat-open">
        <div class="flex items-center justify-between">
            <div class="stat-label">Open</div>
            <span style="font-size: 1.25rem;">🟡</span>
        </div>
        <div class="stat-number" style="color: var(--warning);">{{ $open }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">Needs attention</div>
    </div>

    <div class="stat-card stat-closed">
        <div class="flex items-center justify-between">
            <div class="stat-label">Closed</div>
            <span style="font-size: 1.25rem;">✅</span>
        </div>
        <div class="stat-number" style="color: var(--success);">{{ $closed }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">Resolved</div>
    </div>

    <div class="stat-card stat-in-progress">
        <div class="flex items-center justify-between">
            <div class="stat-label">Assigned to Me</div>
            <span style="font-size: 1.25rem;">👤</span>
        </div>
        <div class="stat-number" style="color: var(--accent);">{{ $assignedToMe }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">I need to handle</div>
    </div>
</div>

{{-- Second row of stats: Created by me --}}
@if(auth()->user()->role !== 'admin')
<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 32px; margin-top: -12px;">
    <div class="stat-card" style="border-left: 4px solid var(--info);">
        <div class="flex items-center justify-between">
            <div class="stat-label">Created by Me</div>
            <span style="font-size: 1.25rem;">✏️</span>
        </div>
        <div class="stat-number" style="color: var(--info);">{{ $createdByMe }}</div>
        <div class="text-xs text-muted" style="margin-top: 4px;">Tickets I submitted</div>
    </div>
</div>
@endif

@if(auth()->user()->role === 'admin')
    {{-- Admin: single recent tickets table --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid var(--border); margin-bottom: 0;">
            <h3 class="card-title">📋 Recent Tickets</h3>
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 7px 14px;">View All</a>
        </div>
        @include('dashboard._ticket_table', ['tickets' => $recentTickets, 'showCreatedBy' => true])
    </div>

@else
    {{-- Non-admin: two separate sections --}}

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
                            <tr onclick="window.location.href='{{ route('tickets.show', $ticket) }}'" style="cursor: pointer;">
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
                                    <span class="badge badge-priority-{{ $ticket->priority }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </td>
                                <td class="text-xs text-muted">
                                    {{ $ticket->createdBy->name ?? '—' }}
                                </td>
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
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 7px 14px;">View All</a>
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
                            <tr onclick="window.location.href='{{ route('tickets.show', $ticket) }}'" style="cursor: pointer;">
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
                                    <span class="badge badge-priority-{{ $ticket->priority }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </td>
                                <td class="text-xs text-muted">
                                    {{ $ticket->assignedTo->name ?? '— Unassigned' }}
                                </td>
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
