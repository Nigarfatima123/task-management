@extends('layouts.app')

@section('content')
  <h1 class="mb-4">Task Manager</h1>

  <div class="card mb-4">
    <div class="card-body">
      <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        <div class="row g-2">
          <div class="col-md-4">
            <input name="title" type="text" class="form-control" placeholder="Task title" required>
          </div>
          <div class="col-md-6">
            <input name="description" type="text" class="form-control" placeholder="Short description">
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary w-100">Add Task</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="mb-3">
    <a href="?filter=all" class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
    <a href="?filter=completed" class="btn btn-sm {{ $filter === 'completed' ? 'btn-primary' : 'btn-outline-primary' }}">Completed</a>
    <a href="?filter=incomplete" class="btn btn-sm {{ $filter === 'incomplete' ? 'btn-primary' : 'btn-outline-primary' }}">Incomplete</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <ul id="task-list" class="list-group list-group-flush">
        @forelse($tasks as $task)
          <li class="list-group-item task-item" data-id="{{ $task->id }}" style="background: {{ $task->is_completed ? '#d1e7dd' : '#f8f9fa' }};">
            <div class="row align-items-center">
              <div class="col-auto pe-0">
                <span class="drag-handle" style="cursor:grab; font-size:1.5rem; color:#6c757d;">&#x2630;</span>
              </div>
              <div class="col">
                <div class="fw-bold mb-1">{{ $task->title }}</div>
                <div class="text-muted small">{{ $task->description }}</div>
              </div>
              <div class="col-auto">
                <div class="btn-group" role="group" aria-label="actions">
                  <button class="btn btn-sm btn-outline-success toggle-complete" data-id="{{ $task->id }}">{{ $task->is_completed ? 'Undo' : 'Complete' }}</button>
                  <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $task->id }}">Edit</button>
                  <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                  </form>
                </div>
              </div>
            </div>
            <!-- Edit Modal -->
            <div class="modal fade" id="editModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="POST" action="{{ route('tasks.update', $task) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                      <h5 class="modal-title">Edit Task</h5>
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
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </li>
        @empty
          <li class="list-group-item text-center text-muted">No tasks found.</li>
        @endforelse
      </ul>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  document.querySelectorAll('.toggle-complete').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.dataset.id;
      axios.post(`/tasks/${id}/toggle`)
        .then(res => { if (res.data.success) window.location.reload(); })
        .catch(err => console.error(err));
    });
  });

  const list = document.getElementById('task-list');
  let reorderToast;
  if (list) {
    new Sortable(list, {
      animation: 200,
      handle: '.drag-handle',
      onStart: function () {
        list.classList.add('reordering');
      },
      onEnd: function () {
        list.classList.remove('reordering');
        const ids = Array.from(list.querySelectorAll('li[data-id]')).map(li => parseInt(li.dataset.id));
        axios.post('/tasks/reorder', { order: ids })
          .then(res => {
            if (res.data.success) {
              showReorderToast('Order updated!');
            } else {
              showReorderToast('Reorder failed', true);
            }
          })
          .catch(() => showReorderToast('Reorder failed', true));
      }
    });
  }

  function showReorderToast(msg, error = false) {
    if (!reorderToast) {
      reorderToast = document.createElement('div');
      reorderToast.className = 'toast align-items-center text-white ' + (error ? 'bg-danger' : 'bg-success') + ' border-0 position-fixed bottom-0 end-0 m-3';
      reorderToast.setAttribute('role', 'alert');
      reorderToast.setAttribute('aria-live', 'assertive');
      reorderToast.setAttribute('aria-atomic', 'true');
      reorderToast.innerHTML = '<div class="d-flex"><div class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>';
      document.body.appendChild(reorderToast);
    }
    reorderToast.querySelector('.toast-body').textContent = msg;
    const toast = new bootstrap.Toast(reorderToast, { delay: 2000 });
    toast.show();
  }
</script>
@endpush
