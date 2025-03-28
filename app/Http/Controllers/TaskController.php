<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('todo', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate(['task' => 'required|unique:tasks']);

        $task = Task::create(['task' => $request->task]);

        return response()->json($task);
    }

    public function complete($id)
    {
        $task = Task::findOrFail($id);
        $task->completed = true; // Mark task as completed
        $task->save();

        return response()->json(['success' => true, 'completed' => $task->completed]);
    }
    public function showAll()
    {
        return response()->json(Task::all());
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(['success' => true]);
    }

    public function DeleteCompleted()
    {
        try {
            // Get count before deletion for success message
            $count = Task::where('completed', true)->count();

            // Delete all completed tasks
            Task::where('completed', true)->delete();
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted $count completed tasks",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'count' => "Failed to delete  completed tasks",
            ], 500);
        }
    }
}
