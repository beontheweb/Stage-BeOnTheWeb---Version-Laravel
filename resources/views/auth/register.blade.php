@extends('template.index')

@section('main')
<main class="mx-auto container py-5" style="width: 40%;">

    <h1>Register</h1>

    <form action="{{ route('register') }}" method="post">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" name="name" id="name">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelp">
            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password">
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
        </div>
        <button type="submit" class="btn btn-primary me-3">Register</button>
        <a href="users" class="btn btn-primary me-3">Annuler</a>

    </form>
</main>
@endsection


