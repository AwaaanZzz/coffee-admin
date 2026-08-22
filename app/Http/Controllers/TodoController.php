<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $todo = Todo::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'is_completed' => false,
        ]);

        return response()->json(['todo' => $todo]);
    }

    public function update(Request $request, Todo $todo)
    {
        if ($todo->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->has('is_completed')) {
            $todo->is_completed = $request->boolean('is_completed');
        }

        if ($request->has('title')) {
            $todo->title = $request->title;
        }

        $todo->save();

        return response()->json($todo);
    }

    public function destroy(Todo $todo)
    {
        if ($todo->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $todo->delete();

        return response()->json(['success' => true]);
    }
}
