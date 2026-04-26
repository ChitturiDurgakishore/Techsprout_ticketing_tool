<div class="table-container">
    <table>
        <thead>
            <tr>
                <th style="padding-left: 24px; width: 72px;">#ID</th>
                <th>Ticket Details</th>
                <th>Status</th>
                <th>Priority</th>
                @if(isset($showCreatedBy) && $showCreatedBy)
                    <th>Created By</th>
                @endif
                <th>Assigned To</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ticketList as $ticket)
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
                    @if(isset($showCreatedBy) && $showCreatedBy)
                        <td class="text-sm text-muted">{{ $ticket->createdBy->name ?? '—' }}</td>
                    @endif
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
