<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Models\User;
use App\Models\Project;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use App\Models\Ticket;


Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        // System-wide counts
        $total       = Ticket::count();
        $open        = Ticket::where('status', 'open')->count();
        $inProgress  = Ticket::where('status', 'in_progress')->count();
        $closed      = Ticket::where('status', 'closed')->count();
        $critical    = Ticket::where('priority', 'critical')->whereIn('status', ['open', 'in_progress'])->count();
        $totalUsers  = User::count();
        $activeProjects = Project::where('is_active', true)->count();
        $assignedToMe = Ticket::where('assigned_to', $user->id)->count();
        $createdByMe  = Ticket::where('created_by', $user->id)->count();

        // Team workload: users with most assigned tickets
        $teamWorkload = User::withCount([
            'assignedTickets',
            'assignedTickets as open_count'        => fn($q) => $q->where('status', 'open'),
            'assignedTickets as in_progress_count' => fn($q) => $q->where('status', 'in_progress'),
            'assignedTickets as closed_count'      => fn($q) => $q->where('status', 'closed'),
        ])->orderByDesc('assigned_tickets_count')->take(8)->get();

        // Recent 5 tickets across the system
        $recentTickets = Ticket::with(['project', 'assignedTo', 'createdBy'])
            ->latest()->take(5)->get();

        $ticketsAssignedToMe = collect();
        $ticketsCreatedByMe  = collect();
    } else {
        $inProgress  = 0;
        $critical    = 0;
        $totalUsers  = 0;
        $activeProjects = 0;
        $teamWorkload   = collect();

        $assignedToMe = Ticket::where('assigned_to', $user->id)->count();
        $createdByMe  = Ticket::where('created_by', $user->id)->count();
        $total  = Ticket::where('assigned_to', $user->id)
                        ->orWhere('created_by', $user->id)->count();
        $open   = Ticket::where(function($q) use ($user) {
                        $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
                    })->where('status', 'open')->count();
        $closed = Ticket::where(function($q) use ($user) {
                        $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
                    })->where('status', 'closed')->count();

        $ticketsAssignedToMe = Ticket::with(['project', 'createdBy'])
            ->where('assigned_to', $user->id)->latest()->take(5)->get();
        $ticketsCreatedByMe = Ticket::with(['project', 'assignedTo'])
            ->where('created_by', $user->id)->latest()->take(5)->get();

        $recentTickets = collect();
    }

    return view('dashboard', compact(
        'total', 'open', 'closed', 'inProgress', 'critical',
        'assignedToMe', 'createdByMe',
        'totalUsers', 'activeProjects',
        'teamWorkload', 'recentTickets',
        'ticketsAssignedToMe', 'ticketsCreatedByMe'
    ));
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    // Projects
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');

    Route::resource('departments', DepartmentController::class);
    Route::resource('tickets', TicketController::class);

    // Admin-only: User management & profiles
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
});

Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');
