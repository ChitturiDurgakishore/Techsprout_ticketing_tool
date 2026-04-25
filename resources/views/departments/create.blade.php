@extends('layouts.app')

@section('title', 'Create Department')

@section('content')
<div class="breadcrumb">
    <a href="{{ route('departments.index') }}" class="breadcrumb-item">🏢 Departments</a>
    <span class="breadcrumb-separator">›</span>
    <span class="breadcrumb-item">Create New</span>
</div>

<div class="card">
    <div class="card-header">
        <h1 class="card-title">🏢 Create New Department</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-error" style="margin-top: 24px;">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('departments.store') }}" style="margin-top: 24px;">
        @csrf

        <div class="form-group">
            <label for="name">🏷️ Department Name *</label>
            <input
                type="text"
                id="name"
                name="name"
                placeholder="e.g., Frontend, Backend, Design, QA"
                value="{{ old('name') }}"
                class="@error('name') is-invalid @enderror"
                required
            >
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">✅ Create Department</button>
            <a href="{{ route('departments.index') }}" class="btn btn-secondary">← Cancel</a>
        </div>
    </form>
</div>
@endsection

