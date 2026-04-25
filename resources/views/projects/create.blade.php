@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
<div class="breadcrumb">
    <a href="{{ route('projects.index') }}">🏗️ Projects</a>
    <span>/</span>
    <span class="breadcrumb-current">New Project</span>
</div>

<div class="card card-wide">
    <div class="card-header">
        <h1 class="card-title">🏗️ Create New Project</h1>
    </div>

    <form method="POST" action="{{ route('projects.store') }}">
        @csrf

        <div class="form-group">
            <label for="name">Project Name <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Customer Portal v2" class="@error('name') is-invalid @enderror" required>
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Describe the project scope and goals...">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label class="flex items-center gap-2" style="cursor: pointer; font-weight: 500;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} style="width: auto;">
                Mark as Active
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Project</button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
