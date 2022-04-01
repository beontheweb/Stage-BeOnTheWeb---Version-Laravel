@extends('template.index')

@section('main')
<main class="container p-2">
    <h1>Users</h1>
    <a href="register" class="btn btn-primary mb-4">Enregistrer un nouvel utilisateur</a>
    <table id="table1" class="display">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Date de création</th>
            <th scope="col">Dernière update</th>
            <th scope="col">Action</th>
            <th scope="col"></th>
          </tr>
        </thead>
        <tbody>
            @if ($users)
                @foreach ($users as $user)
                    <tr>
                        <th scope="row">{{$user->id}}</th>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->created_at}}</td>
                        <td>{{$user->updated_at}}</td>
                        <td>
                            <a href="/user/{{$user->id}}" class="btn btn-primary">Modifier</a>
                        </td>
                        <td>
                            <form action="{{ route('users.delete', ["id" => $user->id]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                
                                <button type="submit" class="btn btn-primary">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
      </table>
</main>
@endsection

@section('script')
    <script>
        $(document).ready( function () {
            $('#table1').DataTable();
        } );
    </script>
@endsection