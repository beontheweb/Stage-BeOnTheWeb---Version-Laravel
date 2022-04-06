@extends('template.index')

@section('main')
    <main>
        <div class="container p-2">
            <h1>Users</h1>
            <a href="register" class="btn btn-primary mb-4">Enregistrer un nouvel utilisateur</a>
        </div>
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
                            <th scope="row">{{ $user->id }}</th>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at }}</td>
                            <td>{{ $user->updated_at }}</td>
                            <td>
                                <a href="/user/{{ $user->id }}" class="btn btn-primary">Modifier</a>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal" data-user-id="{{ $user->id }}">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Attention !</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Voulez-vous vraiment supprimer cet utilisateur ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form id="deleteUserForm" method="post">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-primary" type="submit">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#table1').DataTable();
        });
        
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            $('#deleteUserForm').attr('action', '/users/' + button.data('user-id'));
        });
    </script>
@endsection
