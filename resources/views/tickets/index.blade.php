@extends('layouts.app')

@section('title', 'All Tickets')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.5rem;">Tickets</h1>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
        <span>➕</span> Create Ticket
    </a>
</div>

<!-- Filters Section -->
<div class="card" style="padding: 16px;">
    <form method="GET" action="{{ route('tickets.index') }}" id="filter-form">
        <div class="flex items-center gap-4 flex-wrap">
            <div style="flex: 1; min-width: 240px; position: relative;">
                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-light);">🔍</span>
                <input type="text" name="search" placeholder="Search tickets..." value="{{ request('search') }}" style="padding-left: 36px;">
            </div>

            <select name="status" class="filter-select" onchange="document.getElementById('filter-form').submit()" style="width: auto;">
                <option value="">All Status</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="review" {{ request('status') === 'review' ? 'selected' : '' }}>Review</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>

            <select name="priority" class="filter-select" onchange="document.getElementById('filter-form').submit()" style="width: auto;">
                <option value="">All Priority</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
            </select>

            <label class="flex items-center gap-2" style="cursor: pointer; font-size: 0.875rem; font-weight: 500;">
                <input type="checkbox" name="my_tickets" value="1" {{ request('my_tickets') ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit()">
                <span>My Tickets</span>
            </label>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-secondary">Filter</button>
                @if(request()->anyFilled(['search', 'status', 'priority', 'my_tickets']))
                    <a href="{{ route('tickets.index') }}" class="btn btn-danger" style="padding: 10px;">✕</a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Tickets Table -->
<div class="card" style="padding: 0; overflow: hidden;">
    @if ($tickets->count())
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">#ID</th>
                        <th>Ticket Details</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $ticket)
                        <tr onclick="window.location.href='{{ route('tickets.show', $ticket) }}'" style="cursor: pointer;">
                            <td style="font-weight: 700; color: var(--text-light); padding-left: 24px;">#{{ $ticket->id }}</td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main); margin-bottom: 4px;">{{ Str::limit($ticket->title, 50) }}</div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-type-{{ $ticket->type }}" style="font-size: 0.65rem; padding: 2px 8px;">
                                        {{ ucfirst($ticket->type) }}
                                    </span>
                                    <span class="text-xs text-muted">Assigned to: {{ $ticket->assignedTo->name ?? 'None' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm font-semibold">{{ $ticket->project->name ?? '-' }}</div>
                                <div class="text-xs text-muted">{{ $ticket->department->name ?? '' }}</div>
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

        <!-- Pagination -->
        @if ($tickets->hasPages())
            <div style="padding: 24px; border-top: 1px solid var(--border);">
                {{ $tickets->links() }}
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 64px 0;">
            <div style="font-size: 3rem; margin-bottom: 16px;">🔍</div>
            <h3 style="margin-bottom: 8px;">No Tickets Found</h3>
            <p class="text-muted mb-4">Try adjusting your filters or search criteria.</p>
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Clear All Filters</a>
        </div>
    @endif
</div>
@endsection

