<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Project;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Comment;
class TicketController extends Controller
{
    public function create()
    {
        $projects = Project::where('is_active', true)->get();
        $departments = Department::all();
        $users = User::all();

        // Pre-select project if coming from a project page
        $selectedProjectId = request('project_id');

        return view('tickets.create', compact('projects', 'departments', 'users', 'selectedProjectId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|min:5|max:255',
            'type'          => 'required|in:bug,feature,task',
            'description'   => 'required|string|min:10',
            'page_url'      => 'nullable|string|max:500',
            'priority'      => 'required|in:low,medium,high,critical',
            'project_id'    => 'required|exists:projects,id',
            'department_id' => 'required|exists:departments,id',
            'assigned_to'   => 'nullable|exists:users,id',
            'screenshot'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('tickets/screenshots', 'public');
        }

        $ticket = Ticket::create([
            'title'         => $validated['title'],
            'type'          => $validated['type'],
            'description'   => $validated['description'],
            'page_url'      => $validated['page_url'] ?? null,
            'screenshot'    => $screenshotPath,
            'priority'      => $validated['priority'],
            'project_id'    => $validated['project_id'],
            'department_id' => $validated['department_id'],
            'assigned_to'   => $validated['assigned_to'] ?? null,
            'status'        => 'open',
            'created_by'    => auth()->id(),
        ]);

        // Redirect back to the project if we came from there
        if ($request->filled('from_project')) {
            return redirect()->route('projects.show', $request->from_project)
                ->with('success', 'Ticket created successfully!');
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully!');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Ticket::with(['project', 'assignedTo', 'department', 'createdBy'])->newQuery();

        // Base access control
        if ($user->role !== 'admin') {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            });
        }

        // Filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        // Only tickets assigned to me
        if ($request->filled('my_tickets')) {
            $query->where('assigned_to', $user->id);
        }
        // Only tickets created by me
        if ($request->filled('created_by_me')) {
            $query->where('created_by', $user->id);
        }

        $tickets = $query->latest()->paginate(15);

        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $user = auth()->user();

        if (
            $user->role !== 'admin' &&
            $ticket->assigned_to !== $user->id &&
            $ticket->created_by !== $user->id
        ) {
            abort(403);
        }

        $users = User::all();

        return view('tickets.show', compact('ticket', 'users'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $user = auth()->user();
        if (
            $user->role !== 'admin' &&
            $ticket->assigned_to !== $user->id &&
            $ticket->created_by !== $user->id
        ) {
            abort(403);
        }

        $validated = $request->validate([
            'status'      => 'required|in:open,in_progress,review,closed',
            'assigned_to' => 'nullable|exists:users,id',
            'screenshot'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($request->hasFile('screenshot')) {
            // Delete old screenshot
            if ($ticket->screenshot) {
                Storage::disk('public')->delete($ticket->screenshot);
            }
            $validated['screenshot'] = $request->file('screenshot')->store('tickets/screenshots', 'public');
        }

        $ticket->update($validated);

        return redirect()->back()->with('success', 'Ticket updated successfully!');
    }
}
