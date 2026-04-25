@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id . ' — ' . $ticket->title)

@section('content')
{{-- Breadcrumb --}}
<div class="breadcrumb">
    @if($ticket->project)
        <a href="{{ route('projects.index') }}">🏗️ Projects</a>
        <span>/</span>
        <a href="{{ route('projects.show', $ticket->project) }}">{{ $ticket->project->name }}</a>
        <span>/</span>
    @else
        <a href="{{ route('tickets.index') }}">📋 All Tickets</a>
        <span>/</span>
    @endif
    <span class="breadcrumb-current">Ticket #{{ $ticket->id }}</span>
</div>

<div class="ticket-detail-grid">
    {{-- LEFT COLUMN --}}
    <div style="display: flex; flex-direction: column; gap: 24px;">

        {{-- Ticket Header --}}
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: 16px; margin-bottom: 24px;">
                <div style="flex: 1;">
                    <h1 class="card-title" style="font-size: 1.4rem; margin-bottom: 6px; line-height: 1.3;">{{ $ticket->title }}</h1>
                    <div class="text-xs text-muted">
                        Created {{ $ticket->created_at->diffForHumans() }} by <strong>{{ $ticket->createdBy->name }}</strong>
                    </div>
                </div>
                <span class="badge badge-type-{{ $ticket->type }}" style="flex-shrink: 0; align-self: flex-start;">{{ ucfirst($ticket->type) }}</span>
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <h3 class="info-label" style="margin-bottom: 12px;">Description</h3>
                <div style="line-height: 1.8; color: var(--text-main); white-space: pre-wrap; font-size: 0.9rem;">{{ $ticket->description ?: 'No description provided.' }}</div>
            </div>

            {{-- Reference URL --}}
            @if($ticket->page_url)
                <div class="info-box" style="background: var(--info-light); border: 1px solid #bfdbfe;">
                    <div class="info-label">🔗 Reference URL</div>
                    <a href="{{ $ticket->page_url }}" target="_blank" style="color: var(--accent); font-size: 0.875rem; word-break: break-all; font-weight: 500;">
                        {{ $ticket->page_url }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Screenshot --}}
        @if($ticket->screenshot)
            <div class="card" style="margin-bottom: 0;">
                <h3 class="card-title" style="margin-bottom: 16px;">📸 Screenshot</h3>
                <div class="screenshot-container" onclick="openLightbox(this)">
                    <img src="{{ Storage::url($ticket->screenshot) }}" alt="Ticket Screenshot" class="screenshot-img">
                    <div class="screenshot-overlay">
                        <span>🔍 Click to expand</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Manage Ticket --}}
        <div class="card" style="margin-bottom: 0;">
            <h3 class="card-title" style="margin-bottom: 20px;">⚙️ Manage Ticket</h3>
            <form method="POST" action="{{ route('tickets.update', $ticket) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-2">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="review" {{ $ticket->status === 'review' ? 'selected' : '' }}>Review</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Assign To</label>
                        <select name="assigned_to">
                            <option value="">— Unassigned —</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Update Screenshot --}}
                <div class="form-group">
                    <label>📸 Update Screenshot <span class="text-muted" style="font-weight: 400;">(Optional)</span></label>
                    <div class="file-upload-zone file-upload-compact" id="update-zone">
                        <input type="file" id="update-screenshot" name="screenshot" accept="image/*" class="file-upload-input" onchange="previewUpdateImage(this)">
                        <div class="file-upload-content" id="update-placeholder">
                            <div class="file-upload-icon" style="font-size: 1.25rem;">📷</div>
                            <div class="file-upload-text" style="font-size: 0.8rem;">
                                {{ $ticket->screenshot ? 'Click to replace screenshot' : 'Click to add a screenshot' }}
                            </div>
                        </div>
                        <div class="file-preview-container" id="update-preview" style="display: none;">
                            <img id="update-preview-img" src="" alt="Preview" class="file-preview-img">
                            <button type="button" class="file-preview-remove" onclick="removeUpdateImage()">✕</button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center" style="margin-top: 8px;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <span class="text-xs text-muted">Last updated: {{ $ticket->updated_at->diffForHumans() }}</span>
                </div>
            </form>
        </div>

        {{-- Comments --}}
        <div class="card" style="margin-bottom: 0;">
            <h3 class="card-title" style="margin-bottom: 24px;">
                💬 Discussion
                <span style="background: var(--info-light); color: var(--accent); border-radius: 9999px; padding: 2px 10px; font-size: 0.75rem; margin-left: 8px;">{{ $ticket->comments->count() }}</span>
            </h3>

            <div class="comments-list">
                @forelse($ticket->comments as $comment)
                    <div class="comment {{ auth()->id() === $comment->user_id ? 'comment-own' : '' }}">
                        <div class="comment-header">
                            <div class="flex items-center gap-2">
                                <div class="avatar-xs">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</div>
                                <span class="comment-author">{{ $comment->user->name }}</span>
                            </div>
                            <span class="comment-date">{{ $comment->created_at->format('M d, H:i') }}</span>
                        </div>
                        <div class="comment-body">{{ $comment->message }}</div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 32px 0;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">💬</div>
                        <p class="text-muted">No comments yet. Start the discussion!</p>
                    </div>
                @endforelse
            </div>

            <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border);">
                <form method="POST" action="{{ route('comments.store', $ticket) }}">
                    @csrf
                    <div class="form-group">
                        <label style="font-size: 0.75rem;">Add a comment</label>
                        <textarea name="message" placeholder="Write your message here..." required style="min-height: 80px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-secondary">Post Comment</button>
                </form>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN — Meta Info --}}
    <div>
        <div class="card" style="position: sticky; top: 80px;">
            <h3 class="info-label" style="margin-bottom: 20px; font-size: 0.7rem; letter-spacing: 0.08em;">TICKET DETAILS</h3>

            <div class="info-box">
                <div class="info-label">Status</div>
                <div style="margin-top: 4px;">
                    <span class="badge badge-status-{{ $ticket->status }}">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </div>
            </div>

            <div class="info-box">
                <div class="info-label">Priority</div>
                <div style="margin-top: 4px;">
                    <span class="badge badge-priority-{{ $ticket->priority }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </div>
            </div>

            <div class="info-box">
                <div class="info-label">Project</div>
                <div class="info-value">
                    @if($ticket->project)
                        <a href="{{ route('projects.show', $ticket->project) }}" style="color: var(--accent); font-weight: 600;">
                            {{ $ticket->project->name }}
                        </a>
                    @else
                        —
                    @endif
                </div>
            </div>

            <div class="info-box">
                <div class="info-label">Department</div>
                <div class="info-value">{{ $ticket->department->name ?? '—' }}</div>
            </div>

            <div class="info-box">
                <div class="info-label">Assigned To</div>
                <div class="info-value">
                    <div class="flex items-center gap-2">
                        <div class="avatar-xs">{{ strtoupper(substr($ticket->assignedTo->name ?? '?', 0, 1)) }}</div>
                        {{ $ticket->assignedTo->name ?? 'Unassigned' }}
                    </div>
                </div>
            </div>

            <div class="info-box">
                <div class="info-label">Created By</div>
                <div class="info-value">{{ $ticket->createdBy->name ?? '—' }}</div>
            </div>

            <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 8px;">
                @if($ticket->project)
                    <a href="{{ route('projects.show', $ticket->project) }}" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                        ← Back to Project
                    </a>
                @endif
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                    All Tickets
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Lightbox Modal --}}
<div id="lightbox" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; padding:24px;" onclick="closeLightbox()">
    <img id="lightbox-img" src="" alt="Screenshot" style="max-width:90vw; max-height:90vh; border-radius: 8px; box-shadow: 0 25px 50px rgba(0,0,0,0.5);">
    <button onclick="closeLightbox()" style="position:absolute; top:20px; right:24px; background:white; border:none; border-radius:50%; width:36px; height:36px; font-size:1.1rem; cursor:pointer; display:flex; align-items:center; justify-content:center;">✕</button>
</div>

<script>
function openLightbox(container) {
    const img = container.querySelector('img');
    document.getElementById('lightbox-img').src = img.src;
    const lb = document.getElementById('lightbox');
    lb.style.display = 'flex';
}
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
}

function previewUpdateImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('update-preview-img').src = e.target.result;
            document.getElementById('update-placeholder').style.display = 'none';
            document.getElementById('update-preview').style.display = 'flex';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function removeUpdateImage() {
    document.getElementById('update-screenshot').value = '';
    document.getElementById('update-placeholder').style.display = 'flex';
    document.getElementById('update-preview').style.display = 'none';
}
</script>
@endsection
