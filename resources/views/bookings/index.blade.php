@extends('template.index')

@section('main')
<main class="container p-2">
    <h1>Bookings</h1>
    <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Montant HTVA</th>
            <th scope="col">TVA</th>
            <th scope="col">Total</th>
          </tr>
        </thead>
        <tbody>
            @if ($bookings)
                @foreach ($bookings as $booking)
                    <tr>
                        <th scope="row">{{$booking->alphaNumericalNumber}}</th>
                        <td>{{$booking->HTVA}}</td>
                        <td>{{$booking->TVA}}</td>
                        <td>{{$booking->amount}} {{$booking->currency}}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
      </table>
</main>
@endsection