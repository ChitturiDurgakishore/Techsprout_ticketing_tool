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
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        $total = Ticket::count();
        $open = Ticket::where('status', 'open')->count();
        $closed = Ticket::where('status', 'closed')->count();
        $my = Ticket::where('assigned_to', $user->id)->count();
    } else {
        $total = Ticket::where('assigned_to', $user->id)->orWhere('created_by', $user->id)->count();
        $open = Ticket::where(function($q) use ($user) {
            $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
        })->where('status', 'open')->count();
        $closed = Ticket::where(function($q) use ($user) {
            $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
        })->where('status', 'closed')->count();
        $my = $total;
    }

    $recentTickets = Ticket::with(['project', 'assignedTo'])
        ->when($user->role !== 'admin', function($q) use ($user) {
            $q->where(function($inner) use ($user) {
                $inner->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
            });
        })
        ->latest()
        ->take(5)
        ->get();

    return view('dashboard', compact('total', 'open', 'closed', 'my', 'recentTickets'));
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
