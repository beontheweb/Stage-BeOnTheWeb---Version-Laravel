@extends('template.index')

@section('main')
<main>
  <div class="ms-4">
    <h1>Relations</h1>
  </div>
    <table id="table1" class="display">
        <thead>
          <tr>
            <th></th>
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
                    <tr data-child-value="{{ $relation->id }}">
                        <td></td>
                        <th scope="row">{{$relation->id}}</th>
                        <td>{{$relation->externalID}}</td>
                        <td>{{$relation->name}}</td>
                        <td>{{$relation->ibanAccount}}</td>
                        <td>{{$relation->TVANumber}}</td>
                        <td>{{$relation->telephone}}</td>
                        <td>{{$relation->email}}</td>
                        <td>{{$relation->street}}, {{$relation->postalCode}} {{$relation->city}} {{$relation->country}}</td>
                        <td>{{$relation->created_at->format("Y-m-d")}}</td>
                        <td>{{$relation->updated_at->format("Y-m-d")}}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
      </table>
</main>
@endsection

@section('script')
  <script>
      function format(value) {
          var bookings = {!! json_encode($bookings) !!};

          var table = '';

          bookings.forEach(line => {
            if (line["relation_id"] == value) {
              table += '<tr><th scope="row">'+line["id"]+'</th><td>'+line["alphaNumericalNumber"]+'</td><td>'+line["comment"]+'</td><td>'+line["HTVA"]+'</td><td>'+line["TVA"]+'</td><td>'+line["amount"]+' '+line["currency"]+'</td><td>'+line["reference"]+'</td></tr>'
            }
          });



          return '<table class="table"><thead><tr><th scope="col">#</th><th scope="col">Code</th><th scope="col">Commentaire</th><th scope="col">Montant HTVA</th><th scope="col">TVA</th><th scope="col">Montant TVAC</th><th scope="col">Reference</th></tr></thead><tbody>'+table+'</tbody></table>';
        }
        $(document).ready(function() {
            var table = $('#table1').DataTable({
                "pageLength": 25,
                "columns": [{
                        "className": 'dt-control',
                        "orderable": false,
                        "data": null,
                        "defaultContent": ''
                    },
                    {
                        "data": "id"
                    },
                    {
                        "data": "externalID"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "ibanAccount"
                    },
                    {
                        "data": "TVANumber"
                    },
                    {
                        "data": "telephone"
                    },
                    {
                        "data": "email"
                    },
                    {
                        "data": "address"
                    },
                    {
                        "data": "created_at"
                    },
                    {
                        "data": "updated_at"
                    }
                ],
                "order": [
                    [1, 'asc']
                ]
            });

            // Add event listener for opening and closing details
            $('#table1 tbody').on('click', 'td.dt-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child(format(tr.data('child-value'))).show();
                    tr.addClass('shown');
                }
            });
        });
  </script>
@endsection