<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List all users with their ticket stats — admin only.
     */
    public function index()
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $users = User::withCount([
            'assignedTickets',
            'assignedTickets as open_tickets_count'       => fn($q) => $q->where('status', 'open'),
            'assignedTickets as in_progress_count'        => fn($q) => $q->where('status', 'in_progress'),
            'assignedTickets as closed_tickets_count'     => fn($q) => $q->where('status', 'closed'),
            'createdTickets',
        ])->get();

        return view('users.index', compact('users'));
    }

    /**
     * Show a single user's profile with their tickets — admin only.
     */
    public function show(User $user)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $assignedTickets = Ticket::with(['project', 'createdBy'])
            ->where('assigned_to', $user->id)
            ->latest()
            ->get();

        $createdTickets = Ticket::with(['project', 'assignedTo'])
            ->where('created_by', $user->id)
            ->latest()
            ->get();

        $stats = [
            'assigned'    => $assignedTickets->count(),
            'created'     => $createdTickets->count(),
            'open'        => $assignedTickets->where('status', 'open')->count(),
            'in_progress' => $assignedTickets->where('status', 'in_progress')->count(),
            'closed'      => $assignedTickets->where('status', 'closed')->count(),
        ];

        return view('users.show', compact('user', 'assignedTickets', 'createdTickets', 'stats'));
    }
}
