@extends('layouts.app')
@section('content')
<div class="container py-4">
  <h2 class="mb-4">Admin Dashboard</h2>
  <p>Welcome, Admin! Here you can manage all tasks and users.</p>
  <a href="{{ route('admin.logout') }}" class="btn btn-danger mb-3" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">Logout</a>
  <form id="admin-logout-form" method="POST" action="{{ route('admin.logout') }}" style="display:none;">@csrf</form>

  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Total Tasks</h5>
          <p class="display-6">{{ \App\Models\Task::count() }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Completed Tasks</h5>
          <p class="display-6 text-success">{{ \App\Models\Task::where('is_completed', true)->count() }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Incomplete Tasks</h5>
          <p class="display-6 text-danger">{{ \App\Models\Task::where('is_completed', false)->count() }}</p>
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
    <h5 class="mb-0">All Tasks</h5>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTaskModal">Create Task</button>
  </div>
  <div class="card">
    <div class="card-body p-0">
      <table class="table table-striped mb-0" id="admin-task-table">
        <thead>
          <tr>
            <th></th>
            <th>Title</th>
            <th>Description</th>
            <th>User</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach(\App\Models\Task::with('user')->orderBy('position')->get() as $task)
            <tr data-id="{{ $task->id }}">
              <td class="text-center" style="cursor:grab;">
                <span class="bi bi-list"></span>
              </td>
              <td>{{ $task->title }}</td>
              <td>{{ $task->description }}</td>
              <td>{{ $task->user ? $task->user->name : '-' }}</td>
              <td>
                <span class="badge rounded-pill {{ $task->is_completed ? 'bg-success' : 'bg-secondary' }}">
                  {{ $task->is_completed ? 'Completed' : 'Incomplete' }}
                </span>
              </td>
              <td>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}">Edit</button>
                <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" style="display:inline">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this task?')">Delete</button>
                </form>
              </td>
            </tr>
            <!-- Edit Task Modal -->
            <div class="modal fade" id="editTaskModal{{ $task->id }}" tabindex="-1" aria-labelledby="editTaskModalLabel{{ $task->id }}" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="POST" action="{{ route('tasks.update', $task->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                      <h5 class="modal-title" id="editTaskModalLabel{{ $task->id }}">Edit Task</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input name="title" type="text" class="form-control" value="{{ $task->title }}" required>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control">{{ $task->description }}</textarea>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Assign to User (optional)</label>
                        <select name="user_id" class="form-select">
                          <option value="">-- None --</option>
                          @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" @if($task->user_id == $user->id) selected @endif>{{ $user->name }} ({{ $user->email }})</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Update Task</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Create Task Modal -->
  <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ route('tasks.store') }}">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="createTaskModalLabel">Create Task</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input name="title" type="text" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Assign to User (optional)</label>
              <select name="user_id" class="form-select">
                <option value="">-- None --</option>
                @foreach(\App\Models\User::all() as $user)
                  <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
  const adminTable = document.getElementById('admin-task-table').getElementsByTagName('tbody')[0];
  if (adminTable) {
    new Sortable(adminTable, {
      animation: 200,
      handle: '.bi-list',
      onEnd: function () {
        const ids = Array.from(adminTable.querySelectorAll('tr[data-id]')).map(tr => parseInt(tr.dataset.id));
        axios.post('/tasks/reorder', { order: ids })
          .then(res => {
            if (!res.data.success) alert('Reorder failed');
          })
          .catch(() => alert('Reorder failed'));
      }
    });
  }
</script>
@endpush
@endsection
