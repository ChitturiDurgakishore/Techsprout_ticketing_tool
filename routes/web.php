<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\User;
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
        $total    = Ticket::count();
        $open     = Ticket::where('status', 'open')->count();
        $closed   = Ticket::where('status', 'closed')->count();
        $assignedToMe  = Ticket::where('assigned_to', $user->id)->count();
        $createdByMe   = Ticket::where('created_by', $user->id)->count();
    } else {
        // Tickets assigned to this user
        $assignedToMe = Ticket::where('assigned_to', $user->id)->count();
        // Tickets created by this user
        $createdByMe  = Ticket::where('created_by', $user->id)->count();
        // Total = union of assigned + created (avoid double-count)
        $total  = Ticket::where('assigned_to', $user->id)
                        ->orWhere('created_by', $user->id)
                        ->count();
        $open   = Ticket::where(function($q) use ($user) {
                        $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
                    })->where('status', 'open')->count();
        $closed = Ticket::where(function($q) use ($user) {
                        $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
                    })->where('status', 'closed')->count();
    }

    // Recent tickets assigned TO this user
    $ticketsAssignedToMe = Ticket::with(['project', 'createdBy'])
        ->where('assigned_to', $user->id)
        ->latest()
        ->take(5)
        ->get();

    // Recent tickets created BY this user (assigned to others or unassigned)
    $ticketsCreatedByMe = Ticket::with(['project', 'assignedTo'])
        ->where('created_by', $user->id)
        ->latest()
        ->take(5)
        ->get();

    // For admin: show all recent tickets
    $recentTickets = Ticket::with(['project', 'assignedTo', 'createdBy'])
        ->when($user->role !== 'admin', function($q) use ($user) {
            $q->where(function($inner) use ($user) {
                $inner->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
            });
        })
        ->latest()
        ->take(5)
        ->get();

    return view('dashboard', compact(
        'total', 'open', 'closed',
        'assignedToMe', 'createdByMe',
        'ticketsAssignedToMe', 'ticketsCreatedByMe',
        'recentTickets'
    ));
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    // Projects — show is available to all authenticated users
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');

    Route::resource('departments', DepartmentController::class);
    Route::resource('tickets', TicketController::class);
});

Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');
