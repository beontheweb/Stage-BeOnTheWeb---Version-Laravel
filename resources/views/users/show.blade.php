@extends('template.index')

@section('main')
<main class="container p-2">
    <form action="{{ route('user.update', ["id" => $user->id]) }}" method="post">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="name" class="form-label">Nouveau Nom</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="{{$user->name}}">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Nouvelle Addresse Email</label>
            <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelp" placeholder="{{$user->email}}">
            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
        </div>
        <button type="submit" class="btn btn-primary me-3">Modifier</button>
        <a href="../users" class="btn btn-primary me-3">Annuler</a>

    </form>
</main>
@endsection