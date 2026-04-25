<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::withCount([
            'tickets',
            'tickets as open_tickets_count' => fn($q) => $q->where('status', 'open'),
            'tickets as in_progress_tickets_count' => fn($q) => $q->where('status', 'in_progress'),
            'tickets as closed_tickets_count' => fn($q) => $q->where('status', 'closed'),
        ])->get();

        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        $user = auth()->user();

        $query = $project->tickets()->with(['assignedTo', 'createdBy', 'department']);

        // Non-admins only see their own tickets within the project
        if ($user->role !== 'admin') {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        }

        // Filters
        if (request()->filled('search')) {
            $query->where('title', 'like', '%' . request('search') . '%');
        }
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
        if (request()->filled('priority')) {
            $query->where('priority', request('priority'));
        }

        $tickets = $query->latest()->paginate(15);

        $stats = [
            'total'       => $project->tickets()->count(),
            'open'        => $project->tickets()->where('status', 'open')->count(),
            'in_progress' => $project->tickets()->where('status', 'in_progress')->count(),
            'closed'      => $project->tickets()->where('status', 'closed')->count(),
        ];

        return view('projects.show', compact('project', 'tickets', 'stats'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Project::create($validated);
        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $project->update($validated);
        return redirect()->route('projects.index')->with('success', 'Project updated successfully!');
    }
}
