<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Ticket;

class CommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message
        ]);

        return back();
    }
}
