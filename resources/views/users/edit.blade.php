@extends('dashboard.layout')

@section('content')
<div class="container">
    <h3>Edit User</h3>
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('users.form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection