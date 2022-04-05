@extends('template.index')

@section('main')
<main class="container p-2">
    <h1>Relations</h1>
    <table id="table1" class="display">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">ID Externe</th>
            <th scope="col">Nom</th>
            <th scope="col">Numéro de compte IBAN</th>
            <th scope="col">Numéro de TVA</th>
            <th scope="col">Téléphone</th>
            <th scope="col">E-mail</th>
            <th scope="col">Addresse</th>
            <th scope="col">Créé le</th>
            <th scope="col">Modifié le</th>
          </tr>
        </thead>
        <tbody>
            @if ($relations)
                @foreach ($relations as $relation)
                    <tr>
                        <th scope="row">{{$relation->id}}</th>
                        <td>{{$relation->externalID}}</td>
                        <td>{{$relation->name}}</td>
                        <td>{{$relation->ibanAccount}}</td>
                        <td>{{$relation->TVANumber}}</td>
                        <td>{{$relation->telephone}}</td>
                        <td>{{$relation->email}}</td>
                        <td>{{$relation->street}}, {{$relation->postalCode}} {{$relation->city}} {{$relation->country}}</td>
                        <td>{{$relation->created_at}}</td>
                        <td>{{$relation->updated_at}}</td>
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