@extends('template.index')

@section('main')
    <main>
        <div class="container p-2">
            <h1>Bookings</h1>
        </div>
        <table id="table1" class="display">
            <thead>
                <tr>
                    <th></th>
                    <th scope="col">#</th>
                    <th scope="col">Code</th>
                    <th scope="col">Commentaire</th>
                    <th scope="col">Relation</th>
                    <th scope="col">Date de facture</th>
                    <th scope="col">Montant HTVA</th>
                    <th scope="col">TVA</th>
                    <th scope="col">Montant TVAC</th>
                    <th scope="col">Créé le</th>
                    <th scope="col">Modifié le</th>
                </tr>
            </thead>
            <tbody>
                @if ($bookings)
                    @foreach ($bookings as $key => $booking)
                        <tr data-child-value="{{ $booking->id }}">
                            <td></td>
                            <th scope="row">{{ $booking->id }}</th>
                            <td>{{ $booking->alphaNumericalNumber }}</td>
                            <td>{{ $booking->comment }}</td>
                            <td>{{ $booking->relation->name }}</td>
                            <td>{{ $booking->bookingDate }}</td>
                            <td>{{ $booking->HTVA }}</td>
                            <td>{{ $booking->TVA }}</td>
                            <td>{{ $booking->amount }} {{ $booking->currency }}</td>
                            <td>{{ $booking->created_at }}</td>
                            <td>{{ $booking->updated_at }}</td>
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
          var bookingLines = {!! json_encode($bookingLines) !!};

          var table = '';

          bookingLines.forEach(line => {
            if (line["booking_id"] == value) {
              table += '<tr><th scope="row">'+line["id"]+'</th><td>'+line["alphaNumericalNumber"]+'</td><td>'+line["baseAmount"]+'</td><td>'+line["vatAmount"]+'</td><td>'+line["comment"]+'</td></tr>'
            }
          });



          return '<table class="table"><thead><tr><th scope="col">#</th><th scope="col">Code</th><th scope="col">HTVA</th><th scope="col">TVA</th><th scope="col">Commentaire</th></tr></thead><tbody>'+table+'</tbody></table>';
        }
        $(document).ready(function() {
            var table = $('#table1').DataTable({
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
                        "data": "alphaNumericalNumber"
                    },
                    {
                        "data": "comment"
                    },
                    {
                        "data": "relation"
                    },
                    {
                        "data": "bookingDate"
                    },
                    {
                        "data": "HTVA"
                    },
                    {
                        "data": "TVA"
                    },
                    {
                        "data": "TVAC"
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
