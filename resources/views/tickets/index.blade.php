@extends('layouts.app')

@section('title', 'Tickets')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">🎫 Tickets</h1>
        @if(auth()->user()->role !== 'admin')
            <p class="page-subtitle">Your assigned tickets and tickets you submitted</p>
        @else
            <p class="page-subtitle">All tickets across the system</p>
        @endif
    </div>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">➕ Create Ticket</a>
</div>

{{-- Filters --}}
<div class="card" style="padding: 16px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('tickets.index') }}" id="filter-form">
        {{-- Preserve active tab --}}
        @if(auth()->user()->role !== 'admin')
            <input type="hidden" name="tab" value="{{ $tab }}">
        @endif
        <div class="flex items-center gap-4 flex-wrap">
            <div style="flex: 1; min-width: 200px; position: relative;">
                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-light);">🔍</span>
                <input type="text" name="search" placeholder="Search tickets..." value="{{ request('search') }}" style="padding-left: 36px;">
            </div>

            <select name="status" class="filter-select" onchange="document.getElementById('filter-form').submit()" style="width: auto;">
                <option value="">All Status</option>
                <option value="open"        {{ request('status') === 'open'        ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="review"      {{ request('status') === 'review'      ? 'selected' : '' }}>Review</option>
                <option value="closed"      {{ request('status') === 'closed'      ? 'selected' : '' }}>Closed</option>
            </select>

            <select name="priority" class="filter-select" onchange="document.getElementById('filter-form').submit()" style="width: auto;">
                <option value="">All Priority</option>
                <option value="low"      {{ request('priority') === 'low'      ? 'selected' : '' }}>Low</option>
                <option value="medium"   {{ request('priority') === 'medium'   ? 'selected' : '' }}>Medium</option>
                <option value="high"     {{ request('priority') === 'high'     ? 'selected' : '' }}>High</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-secondary">Filter</button>
                @if(request()->anyFilled(['search', 'status', 'priority']))
                    <a href="{{ route('tickets.index', auth()->user()->role !== 'admin' ? ['tab' => $tab] : []) }}" class="btn btn-danger" style="padding: 10px;">✕</a>
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
        @include('tickets._table', ['tickets' => $tickets, 'extraCol' => 'created_by'])
        @if($tickets->hasPages())
            <div style="padding: 20px 24px; border-top: 1px solid var(--border);">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

@else
{{-- ============================================================ --}}
{{-- NON-ADMIN: Two-tab view                                      --}}
{{-- ============================================================ --}}

    {{-- Tab Navigation --}}
    <div class="ticket-tabs">
        <a href="{{ route('tickets.index', array_merge(request()->except('tab', 'assigned_page', 'created_page'), ['tab' => 'assigned'])) }}"
           class="ticket-tab {{ $tab === 'assigned' ? 'ticket-tab-active' : '' }}">
            👤 Assigned to Me
            <span class="tab-count">{{ $assignedTickets->total() }}</span>
        </a>
        <a href="{{ route('tickets.index', array_merge(request()->except('tab', 'assigned_page', 'created_page'), ['tab' => 'created'])) }}"
           class="ticket-tab {{ $tab === 'created' ? 'ticket-tab-active' : '' }}">
            ✏️ Assigned by Me
            <span class="tab-count">{{ $createdTickets->total() }}</span>
        </a>
    </div>

    {{-- Tab: Assigned to Me --}}
    @if($tab === 'assigned')
        <div class="card" style="padding: 0; overflow: hidden; border-top-left-radius: 0;">
            @if($assignedTickets->count())
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th style="padding-left: 24px;">#ID</th>
                                <th>Ticket Details</th>
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
                                        <div style="font-weight: 600; color: var(--text-main); margin-bottom: 4px;">{{ Str::limit($ticket->title, 50) }}</div>
                                        <span class="badge badge-type-{{ $ticket->type }}" style="font-size: 0.65rem; padding: 2px 8px;">{{ ucfirst($ticket->type) }}</span>
                                    </td>
                                    <td>
                                        <div class="text-sm font-semibold">{{ $ticket->project->name ?? '—' }}</div>
                                        <div class="text-xs text-muted">{{ $ticket->department->name ?? '' }}</div>
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
                <div style="text-align: center; padding: 64px 0;">
                    <div style="font-size: 3rem; margin-bottom: 16px;">🎉</div>
                    <h3 style="margin-bottom: 8px;">No tickets assigned to you</h3>
                    <p class="text-muted" style="margin-bottom: 0;">You're all caught up! No pending tickets.</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Tab: Created / Assigned by Me --}}
    @if($tab === 'created')
        <div class="card" style="padding: 0; overflow: hidden; border-top-left-radius: {{ $tab === 'created' ? '0' : 'var(--radius-md)' }};">
            @if($createdTickets->count())
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th style="padding-left: 24px;">#ID</th>
                                <th>Ticket Details</th>
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
                                        <div style="font-weight: 600; color: var(--text-main); margin-bottom: 4px;">{{ Str::limit($ticket->title, 50) }}</div>
                                        <span class="badge badge-type-{{ $ticket->type }}" style="font-size: 0.65rem; padding: 2px 8px;">{{ ucfirst($ticket->type) }}</span>
                                    </td>
                                    <td>
                                        <div class="text-sm font-semibold">{{ $ticket->project->name ?? '—' }}</div>
                                        <div class="text-xs text-muted">{{ $ticket->department->name ?? '' }}</div>
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
                <div style="text-align: center; padding: 64px 0;">
                    <div style="font-size: 3rem; margin-bottom: 16px;">📭</div>
                    <h3 style="margin-bottom: 8px;">No tickets created yet</h3>
                    <p class="text-muted" style="margin-bottom: 16px;">You haven't submitted any tickets.</p>
                    <a href="{{ route('tickets.create') }}" class="btn btn-primary">➕ Create Ticket</a>
                </div>
            @endif
        </div>
    @endif
@endif

@endsection
