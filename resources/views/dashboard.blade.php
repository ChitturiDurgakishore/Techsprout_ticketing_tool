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
    </div>

    <div class="stat-card stat-open">
        <div class="flex items-center justify-between">
            <div class="stat-label">Open</div>
            <span style="font-size: 1.25rem;">🟡</span>
        </div>
        <div class="stat-number" style="color: var(--warning);">{{ $open }}</div>
    </div>

    <div class="stat-card stat-closed">
        <div class="flex items-center justify-between">
            <div class="stat-label">Closed</div>
            <span style="font-size: 1.25rem;">✅</span>
        </div>
        <div class="stat-number" style="color: var(--success);">{{ $closed }}</div>
    </div>

    <div class="stat-card stat-in-progress">
        <div class="flex items-center justify-between">
            <div class="stat-label">Assigned to Me</div>
            <span style="font-size: 1.25rem;">👤</span>
        </div>
        <div class="stat-number" style="color: var(--accent);">{{ $my }}</div>
    </div>
</div>

{{-- Recent Tickets --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid var(--border); margin-bottom: 0;">
        <h3 class="card-title">📋 Recent Tickets</h3>
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 7px 14px;">View All</a>
    </div>

    @if ($recentTickets->count())
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">#ID</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentTickets as $ticket)
                        <tr onclick="window.location.href='{{ route('tickets.show', $ticket) }}'" style="cursor: pointer;">
                            <td style="font-weight: 700; color: var(--text-light); padding-left: 24px;">#{{ $ticket->id }}</td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">{{ Str::limit($ticket->title, 45) }}</div>
                                <div class="text-xs text-muted">{{ $ticket->assignedTo->name ?? 'Unassigned' }}</div>
                            </td>
                            <td>
                                @if($ticket->project)
                                    <a href="{{ route('projects.show', $ticket->project) }}" style="color: var(--accent); font-weight: 600; font-size: 0.85rem;" onclick="event.stopPropagation()">
                                        {{ $ticket->project->name }}
                                    </a>
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
                                {{ $ticket->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">📭</div>
            <h3>No Tickets Yet</h3>
            <p class="text-muted">Start by browsing projects and creating your first ticket.</p>
            <a href="{{ route('projects.index') }}" class="btn btn-primary" style="margin-top: 16px;">Browse Projects</a>
        </div>
    @endif
</div>
@endsection
