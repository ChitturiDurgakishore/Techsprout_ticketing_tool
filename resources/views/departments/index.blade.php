@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.5rem;">🏢 Departments</h1>
    <a href="{{ route('departments.create') }}" class="btn btn-primary">
        <span>➕</span> New Department
    </a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    @if ($departments->count())
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px; padding-left: 24px;">#ID</th>
                        <th>Department Name</th>
                        <th style="text-align: right; padding-right: 24px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $dept)
                        <tr>
                            <td style="font-weight: 700; color: var(--text-light); padding-left: 24px;">#{{ $dept->id }}</td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">{{ $dept->name }}</div>
                            </td>
                            <td style="text-align: right; padding-right: 24px;">
                                <a href="{{ route('departments.edit', $dept) }}" class="btn btn-secondary text-xs" style="padding: 6px 12px;">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 64px 0;">
            <div style="font-size: 3rem; margin-bottom: 16px;">🏢</div>
            <h3 style="margin-bottom: 8px;">No Departments Yet</h3>
            <p class="text-muted mb-4">Add departments to categorize your team and tickets.</p>
            <a href="{{ route('departments.create') }}" class="btn btn-primary">Add Department</a>
        </div>
    @endif
</div>
@endsection
