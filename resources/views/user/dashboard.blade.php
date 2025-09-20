@extends('layouts.app')
@section('content')
<div class="container py-4">
  <h2 class="mb-4">User Dashboard</h2>
  <p>Welcome! Here you can view and update your tasks.</p>
  <a href="{{ route('user.logout') }}" class="btn btn-danger mb-3" onclick="event.preventDefault(); document.getElementById('user-logout-form').submit();">Logout</a>
  <form id="user-logout-form" method="POST" action="{{ route('user.logout') }}" style="display:none;">@csrf</form>

  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Total Tasks</h5>
          <p class="display-6">{{ \App\Models\Task::where('user_id', session('user_id'))->count() }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Completed Tasks</h5>
          <p class="display-6 text-success">{{ \App\Models\Task::where('user_id', session('user_id'))->where('is_completed', true)->count() }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Incomplete Tasks</h5>
          <p class="display-6 text-danger">{{ \App\Models\Task::where('user_id', session('user_id'))->where('is_completed', false)->count() }}</p>
        </div>
      </div>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>My Tasks</span>
      <div>
        <a href="?filter=all" class="btn btn-sm {{ request('filter', 'all') === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
        <a href="?filter=completed" class="btn btn-sm {{ request('filter') === 'completed' ? 'btn-success' : 'btn-outline-success' }}">Completed</a>
        <a href="?filter=incomplete" class="btn btn-sm {{ request('filter') === 'incomplete' ? 'btn-danger' : 'btn-outline-danger' }}">Incomplete</a>
      </div>
    </div>
    <div class="card-body p-0">
      @php
        $filter = request('filter', 'all');
        $tasksQuery = \App\Models\Task::where('user_id', session('user_id'))->orderBy('position');
        if ($filter === 'completed') {
          $tasksQuery->where('is_completed', true);
        } elseif ($filter === 'incomplete') {
          $tasksQuery->where('is_completed', false);
        }
        $tasks = $tasksQuery->get();
      @endphp
      <table class="table table-striped mb-0" id="user-task-table">
        <thead>
          <tr>
            <th></th>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tasks as $task)
            <tr data-id="{{ $task->id }}">
              <td class="text-center" style="cursor:grab;">
                <span class="bi bi-list"></span>
              </td>
              <td>{{ $task->title }}</td>
              <td>{{ $task->description }}</td>
              <td>
                <span class="badge rounded-pill {{ $task->is_completed ? 'bg-success' : 'bg-secondary' }}">
                  {{ $task->is_completed ? 'Completed' : 'Incomplete' }}
                </span>
              </td>
              <td>
                <form method="POST" action="{{ route('tasks.toggle', $task->id) }}" style="display:inline">
                  @csrf
                  <button class="btn btn-sm btn-outline-success">{{ $task->is_completed ? 'Mark Incomplete' : 'Mark Completed' }}</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
  const userTable = document.getElementById('user-task-table').getElementsByTagName('tbody')[0];
  if (userTable) {
    new Sortable(userTable, {
      animation: 200,
      handle: '.bi-list',
      onEnd: function () {
        const ids = Array.from(userTable.querySelectorAll('tr[data-id]')).map(tr => parseInt(tr.dataset.id));
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
