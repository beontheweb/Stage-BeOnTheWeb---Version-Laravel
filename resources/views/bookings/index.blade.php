@extends('template.index')

@section('main')
<main class="container p-2">
    <h1>Bookings</h1>
    <table class="table">
        <thead>
          <tr>
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
                @foreach ($bookings as $booking)
                    <tr>
                        <th scope="row">{{$booking->id}}</th>
                        <td>{{$booking->alphaNumericalNumber}}</td>
                        <td>{{$booking->comment}}</td>
                        <td>{{$booking->relation->name}}</td>
                        <td>{{$booking->bookingDate}}</td>
                        <td>{{$booking->HTVA}}</td>
                        <td>{{$booking->TVA}}</td>
                        <td>{{$booking->amount}} {{$booking->currency}}</td>
                        <td>{{$booking->created_at}}</td>
                        <td>{{$booking->updated_at}}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
      </table>
</main>
@endsection