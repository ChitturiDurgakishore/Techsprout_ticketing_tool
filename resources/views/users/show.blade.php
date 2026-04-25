@extends('layouts.app')

@section('title', $user->name . ' — Profile')

@section('content')

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('users.index') }}">👥 Users</a>
    <span>/</span>
    <span class="breadcrumb-current">{{ $user->name }}</span>
</div>

{{-- Profile Header --}}
<div class="card" style="margin-bottom: 24px;">
    <div class="flex items-center gap-4" style="flex-wrap: wrap;">
        {{-- Avatar --}}
        <div style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), #7c3aed); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 800; flex-shrink: 0;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        {{-- Info --}}
        <div style="flex: 1; min-width: 200px;">
            <div class="flex items-center gap-2" style="flex-wrap: wrap; margin-bottom: 4px;">
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">{{ $user->name }}</h1>
                <span class="badge" style="background: {{ $user->role === 'admin' ? 'var(--danger-light)' : 'var(--info-light)' }}; color: {{ $user->role === 'admin' ? 'var(--danger)' : '#1e40af' }}; font-size: 0.75rem;">
                    {{ ucfirst($user->role ?? 'user') }}
                </span>
            </div>
            <div class="text-sm text-muted">{{ $user->email }}</div>
            <div class="text-xs text-muted" style="margin-top: 4px;">Member since {{ $user->created_at->format('M d, Y') }}</div>
        </div>

        {{-- Quick action --}}
        <div>
            <a href="{{ route('tickets.create') }}?assigned_to={{ $user->id }}" class="btn btn-primary" style="font-size: 0.85rem;">➕ Assign Ticket to {{ explode(' ', $user->name)[0] }}</a>
        </div>
    </div>
</div>

{{-- Stats row --}}
<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-label">Assigned</div>
        <div class="stat-number" style="font-size: 1.5rem;">{{ $stats['assigned'] }}</div>
    </div>
    <div class="stat-card stat-open">
        <div class="stat-label">Open</div>
        <div class="stat-number" style="font-size: 1.5rem; color: var(--warning);">{{ $stats['open'] }}</div>
    </div>
    <div class="stat-card stat-in-progress">
        <div class="stat-label">In Progress</div>
        <div class="stat-number" style="font-size: 1.5rem; color: var(--accent);">{{ $stats['in_progress'] }}</div>
    </div>
    <div class="stat-card stat-closed">
        <div class="stat-label">Closed</div>
        <div class="stat-number" style="font-size: 1.5rem; color: var(--success);">{{ $stats['closed'] }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid var(--info);">
        <div class="stat-label">Created</div>
        <div class="stat-number" style="font-size: 1.5rem; color: var(--info);">{{ $stats['created'] }}</div>
    </div>
</div>

{{-- Tickets Assigned TO this user --}}
<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid var(--border); margin-bottom: 0;">
        <div>
            <h3 class="card-title">👤 Tickets Assigned to {{ explode(' ', $user->name)[0] }}</h3>
            <p class="text-xs text-muted" style="margin-top: 4px;">{{ $assignedTickets->count() }} ticket(s) currently assigned to this user</p>
        </div>
    </div>

    @if($assignedTickets->count())
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
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignedTickets as $ticket)
                        <tr onclick="window.location.href='{{ route('tickets.show', $ticket) }}'" style="cursor: pointer;">
                            <td style="font-weight: 700; color: var(--text-light); padding-left: 24px;">#{{ $ticket->id }}</td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">{{ Str::limit($ticket->title, 45) }}</div>
                                <span class="badge badge-type-{{ $ticket->type }}" style="font-size: 0.65rem; padding: 2px 8px;">{{ ucfirst($ticket->type) }}</span>
                            </td>
                            <td class="text-sm" style="color: var(--accent); font-weight: 600;">{{ $ticket->project->name ?? '—' }}</td>
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
    @else
        <div style="text-align: center; padding: 48px 0;">
            <div style="font-size: 2.5rem; margin-bottom: 12px;">🎉</div>
            <h3 style="margin-bottom: 6px; font-size: 1rem;">No tickets assigned</h3>
            <p class="text-muted text-xs">{{ $user->name }} has no tickets assigned at the moment.</p>
        </div>
    @endif
</div>

{{-- Tickets Created BY this user --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid var(--border); margin-bottom: 0;">
        <div>
            <h3 class="card-title">✏️ Tickets Created by {{ explode(' ', $user->name)[0] }}</h3>
            <p class="text-xs text-muted" style="margin-top: 4px;">{{ $createdTickets->count() }} ticket(s) submitted by this user</p>
        </div>
    </div>

    @if($createdTickets->count())
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
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($createdTickets as $ticket)
                        <tr onclick="window.location.href='{{ route('tickets.show', $ticket) }}'" style="cursor: pointer;">
                            <td style="font-weight: 700; color: var(--text-light); padding-left: 24px;">#{{ $ticket->id }}</td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">{{ Str::limit($ticket->title, 45) }}</div>
                                <span class="badge badge-type-{{ $ticket->type }}" style="font-size: 0.65rem; padding: 2px 8px;">{{ ucfirst($ticket->type) }}</span>
                            </td>
                            <td class="text-sm" style="color: var(--accent); font-weight: 600;">{{ $ticket->project->name ?? '—' }}</td>
                            <td>
                                <span class="badge badge-status-{{ $ticket->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                            </td>
                            <td class="text-xs text-muted">{{ $ticket->assignedTo->name ?? '— Unassigned' }}</td>
                            <td class="text-xs text-muted">{{ $ticket->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 48px 0;">
            <div style="font-size: 2.5rem; margin-bottom: 12px;">📭</div>
            <h3 style="margin-bottom: 6px; font-size: 1rem;">No tickets created</h3>
            <p class="text-muted text-xs">{{ $user->name }} hasn't submitted any tickets yet.</p>
        </div>
    @endif
</div>

@endsection
