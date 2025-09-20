<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all'); // all, completed, incomplete
        $tasksQuery = Task::orderBy('position');

        if ($filter === 'completed') {
            $tasksQuery->completed();
        } elseif ($filter === 'incomplete') {
            $tasksQuery->incomplete();
        }

        $tasks = $tasksQuery->get();

        return view('tasks.index', compact('tasks', 'filter'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxPos = Task::max('position');
        $data['position'] = is_null($maxPos) ? 1 : $maxPos + 1;
        $data['user_id'] = $request->user_id;
        Task::create($data);

        return redirect()->route('admin.dashboard')->with('success', 'Task created.');
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $data['user_id'] = $request->user_id;
        $task->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Task deleted.');
    }

    // Toggle completion (AJAX)
    public function toggleComplete(Task $task)
    {
        $task->is_completed = ! $task->is_completed;
        $task->save();
        return redirect()->route('user.dashboard')->with('success', 'Task Updated.');
    }

    // Reorder tasks (AJAX)
    public function reorder(Request $request)
    {
        $order = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|distinct',
        ])['order'];

        DB::transaction(function () use ($order) {
            foreach ($order as $index => $id) {
                Task::where('id', $id)->update(['position' => $index + 1]);
            }
        });

        return response()->json(['success' => true]);
    }
}
