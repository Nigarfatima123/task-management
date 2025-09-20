@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h2 class="mb-4">Edit Task</h2>
  <form method="POST" action="{{ route('tasks.update', $task->id) }}">
    @csrf
    @method('PUT')
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
        @foreach(App\Models\User::all() as $user)
          <option value="{{ $user->id }}" @if($task->user_id == $user->id) selected @endif>{{ $user->name }} ({{ $user->email }})</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Update Task</button>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>
@endsection
