@extends('layouts.app')

@section('title', 'Create Ticket')

@section('content')
<div class="breadcrumb">
    @if(request('from_project'))
        <a href="{{ route('projects.index') }}">🏗️ Projects</a>
        <span>/</span>
        @php $proj = \App\Models\Project::find(request('from_project') ?: request('project_id')); @endphp
        @if($proj)
            <a href="{{ route('projects.show', $proj) }}">{{ $proj->name }}</a>
            <span>/</span>
        @endif
    @else
        <a href="{{ route('tickets.index') }}">📋 All Tickets</a>
        <span>/</span>
    @endif
    <span class="breadcrumb-current">Create New Ticket</span>
</div>

<div class="card card-wide">
    <div class="card-header">
        <h1 class="card-title">🎫 Create New Ticket</h1>
    </div>

    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
        @csrf
        {{-- Hidden field to track where we came from --}}
        @if(request('from_project'))
            <input type="hidden" name="from_project" value="{{ request('from_project') }}">
        @endif

        {{-- Title --}}
        <div class="form-group">
            <label for="title">📝 Ticket Title <span class="text-danger">*</span></label>
            <input
                type="text"
                id="title"
                name="title"
                placeholder="Briefly describe the issue..."
                value="{{ old('title') }}"
                class="@error('title') is-invalid @enderror"
                required
            >
            @error('title') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        {{-- Type & Priority --}}
        <div class="grid grid-2">
            <div class="form-group">
                <label for="type">🏷️ Ticket Type <span class="text-danger">*</span></label>
                <select id="type" name="type" class="@error('type') is-invalid @enderror" required>
                    <option value="">Select Type</option>
                    <option value="bug" {{ old('type') === 'bug' ? 'selected' : '' }}>🐛 Bug</option>
                    <option value="feature" {{ old('type') === 'feature' ? 'selected' : '' }}>✨ Feature</option>
                    <option value="task" {{ old('type') === 'task' ? 'selected' : '' }}>✅ Task</option>
                </select>
                @error('type') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="priority">⚡ Priority <span class="text-danger">*</span></label>
                <select id="priority" name="priority" class="@error('priority') is-invalid @enderror" required>
                    <option value="">Select Priority</option>
                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>🟢 Low</option>
                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>🟠 High</option>
                    <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                </select>
                @error('priority') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Project & Department --}}
        <div class="grid grid-2">
            <div class="form-group">
                <label for="project_id">🏗️ Project <span class="text-danger">*</span></label>
                <select id="project_id" name="project_id" class="@error('project_id') is-invalid @enderror" required>
                    <option value="">Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ (old('project_id') ?? $selectedProjectId) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="department_id">🏢 Department <span class="text-danger">*</span></label>
                <select id="department_id" name="department_id" class="@error('department_id') is-invalid @enderror" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Assign To --}}
        <div class="form-group">
            <label for="assigned_to">👤 Assign To <span class="text-muted" style="font-weight: 400;">(Optional)</span></label>
            <select id="assigned_to" name="assigned_to">
                <option value="">— Leave Unassigned —</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ ucfirst($user->role) }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Reference URL --}}
        <div class="form-group">
            <label for="page_url">🔗 Reference URL <span class="text-muted" style="font-weight: 400;">(Optional)</span></label>
            <input type="text" id="page_url" name="page_url" placeholder="https://example.com/affected-page" value="{{ old('page_url') }}">
        </div>

        {{-- Description --}}
        <div class="form-group">
            <label for="description">📋 Detailed Description <span class="text-danger">*</span></label>
            <textarea id="description" name="description" placeholder="Provide all necessary steps to reproduce, expected vs actual behavior..." required>{{ old('description') }}</textarea>
            @error('description') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        {{-- Screenshot Upload --}}
        <div class="form-group">
            <label for="screenshot">📸 Screenshot <span class="text-muted" style="font-weight: 400;">(Optional — max 5MB)</span></label>
            <div class="file-upload-zone" id="upload-zone">
                <input type="file" id="screenshot" name="screenshot" accept="image/*" class="file-upload-input" onchange="previewImage(this)">
                <div class="file-upload-content" id="upload-placeholder">
                    <div class="file-upload-icon">📷</div>
                    <div class="file-upload-text">
                        <strong>Click to upload</strong> or drag & drop a screenshot
                    </div>
                    <div class="file-upload-hint">PNG, JPG, GIF, WebP up to 5MB</div>
                </div>
                <div class="file-preview-container" id="preview-container" style="display: none;">
                    <img id="preview-img" src="" alt="Preview" class="file-preview-img">
                    <button type="button" class="file-preview-remove" onclick="removeImage()">✕ Remove</button>
                </div>
            </div>
            @error('screenshot') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">🎫 Create Ticket</button>
            @if(request('from_project'))
                <a href="{{ route('projects.show', request('from_project')) }}" class="btn btn-secondary">Cancel</a>
            @else
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Cancel</a>
            @endif
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const placeholder = document.getElementById('upload-placeholder');
    const previewContainer = document.getElementById('preview-container');
    const previewImg = document.getElementById('preview-img');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            placeholder.style.display = 'none';
            previewContainer.style.display = 'flex';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    const input = document.getElementById('screenshot');
    const placeholder = document.getElementById('upload-placeholder');
    const previewContainer = document.getElementById('preview-container');

    input.value = '';
    placeholder.style.display = 'flex';
    previewContainer.style.display = 'none';
}

// Drag & Drop
const uploadZone = document.getElementById('upload-zone');
uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadZone.classList.add('dragging');
});
uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragging'));
uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('dragging');
    const input = document.getElementById('screenshot');
    input.files = e.dataTransfer.files;
    previewImage(input);
});
</script>
@endsection
