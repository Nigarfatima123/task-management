@extends('layouts.app')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <h3 class="mb-4">User Login</h3>
      <form method="POST" action="{{ route('user.login.submit') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Login</button>
        @if($errors->any())
          <div class="alert alert-danger mt-3">{{ $errors->first() }}</div>
        @endif
      </form>
    </div>
  </div>
</div>
@endsection
