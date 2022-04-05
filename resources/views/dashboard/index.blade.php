@extends('template.index')

@section('main')
<main class="container p-2">
    <form action="{{ route('dashboard.updateDB') }}" method="get">
        @csrf

        <div class="mb-3">
            <label for="timestamp" class="form-label">Timestamp</label>
            <input type="date" class="form-control" name="timestamp" id="timestamp" value="{{$lastUpdated->format('Y-m-d')}}">
        </div>
        <div>
            <label class="me-2">Journaux :</label>
            @foreach ($journals as $key => $journal)
                <input type="checkbox" id="journal{{$key+1}}" name="journals[]" value="{{$journal}}" checked>
                <label for="journal{{$key+1}}">{{$journal}}</label>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary mt-3">Update DB</button>
    </form>
    <br>
    <?php
    if ($modifiedBookings) {
        echo '<h2>Modified Bookings</h2>';
        echo '<pre>';
        print_r($modifiedBookings);
        echo '</pre>';
    }
    ?>
</main>
@endsection