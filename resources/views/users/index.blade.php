@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">👥 User Management</h1>
        <p class="page-subtitle">All registered users and their ticket activity</p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid var(--border); margin-bottom: 0;">
        <h3 class="card-title">All Users <span style="color: var(--text-muted); font-weight: 400; font-size: 0.9rem;">({{ $users->count() }})</span></h3>
    </div>

    @if($users->count())
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">User</th>
                        <th>Role</th>
                        <th>Assigned</th>
                        <th>Open</th>
                        <th>In Progress</th>
                        <th>Closed</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr onclick="window.location.href='{{ route('users.show', $user) }}'" style="cursor: pointer;">
                            <td style="padding-left: 24px;">
                                <div class="flex items-center gap-2">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), #7c3aed); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; font-weight: 700; flex-shrink: 0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 0.875rem; color: var(--text-main);">{{ $user->name }}</div>
                                        <div class="text-xs text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background: {{ $user->role === 'admin' ? 'var(--danger-light)' : 'var(--info-light)' }}; color: {{ $user->role === 'admin' ? 'var(--danger)' : '#1e40af' }};">
                                    {{ ucfirst($user->role ?? 'user') }}
                                </span>
                            </td>
                            <td style="font-weight: 700; font-size: 1rem;">{{ $user->assigned_tickets_count }}</td>
                            <td><span style="color: var(--warning); font-weight: 600;">{{ $user->open_tickets_count }}</span></td>
                            <td><span style="color: var(--accent); font-weight: 600;">{{ $user->in_progress_count }}</span></td>
                            <td><span style="color: var(--success); font-weight: 600;">{{ $user->closed_tickets_count }}</span></td>
                            <td class="text-xs text-muted">{{ $user->created_tickets_count }}</td>
                            <td onclick="event.stopPropagation()">
                                <a href="{{ route('users.show', $user) }}" class="btn btn-secondary" style="font-size: 0.75rem; padding: 5px 12px;">View Profile</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 64px 0;">
            <div style="font-size: 3rem; margin-bottom: 16px;">👥</div>
            <h3 style="margin-bottom: 8px;">No users found</h3>
        </div>
    @endif
</div>
@endsection
