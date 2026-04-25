@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
<div class="breadcrumb">
    <a href="{{ route('projects.index') }}">🏗️ Projects</a>
    <span>/</span>
    <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
    <span>/</span>
    <span class="breadcrumb-current">Edit</span>
</div>

<div class="card card-wide">
    <div class="card-header">
        <h1 class="card-title">✏️ Edit Project</h1>
    </div>

    <form method="POST" action="{{ route('projects.update', $project) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Project Name <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $project->name) }}" class="@error('name') is-invalid @enderror" required>
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $project->description) }}</textarea>
        </div>

        <div class="form-group">
            <label class="flex items-center gap-2" style="cursor: pointer; font-weight: 500;">
                <input type="checkbox" name="is_active" value="1" {{ $project->is_active ? 'checked' : '' }} style="width: auto;">
                Mark as Active
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
