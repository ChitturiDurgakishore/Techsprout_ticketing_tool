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
        $user    = auth()->user();
        $section = request()->get('section', 'assigned'); // 'assigned' or 'created'
        $search   = request('search');
        $status   = request('status');
        $priority = request('priority');

        // Always compute project-wide stats (all tickets in project)
        $stats = [
            'total'       => $project->tickets()->count(),
            'open'        => $project->tickets()->where('status', 'open')->count(),
            'in_progress' => $project->tickets()->where('status', 'in_progress')->count(),
            'closed'      => $project->tickets()->where('status', 'closed')->count(),
        ];

        if ($user->role === 'admin') {
            // Admin sees all tickets in one list
            $query = $project->tickets()->with(['assignedTo', 'createdBy', 'department']);
            if ($search)   $query->where('title', 'like', '%' . $search . '%');
            if ($status)   $query->where('status', $status);
            if ($priority) $query->where('priority', $priority);
            $tickets        = $query->latest()->paginate(15)->withQueryString();
            $assignedTickets = null;
            $createdTickets  = null;
        } else {
            $tickets = null;

            // Tickets within this project assigned TO this user
            $assignedQuery = $project->tickets()->with(['createdBy', 'department'])
                ->where('assigned_to', $user->id);
            if ($search)   $assignedQuery->where('title', 'like', '%' . $search . '%');
            if ($status)   $assignedQuery->where('status', $status);
            if ($priority) $assignedQuery->where('priority', $priority);
            $assignedTickets = $assignedQuery->latest()->paginate(15, ['*'], 'assigned_page')->withQueryString();

            // Tickets within this project created BY this user
            $createdQuery = $project->tickets()->with(['assignedTo', 'department'])
                ->where('created_by', $user->id);
            if ($search)   $createdQuery->where('title', 'like', '%' . $search . '%');
            if ($status)   $createdQuery->where('status', $status);
            if ($priority) $createdQuery->where('priority', $priority);
            $createdTickets = $createdQuery->latest()->paginate(15, ['*'], 'created_page')->withQueryString();
        }

        return view('projects.show', compact(
            'project', 'stats', 'section',
            'tickets', 'assignedTickets', 'createdTickets'
        ));
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
