@extends('template.index')

@section('main')
<main class="container p-2">
    <strong>Database Connected: </strong>
    <?php
    try {
        \DB::connection()->getPDO();
        echo \DB::connection()->getDatabaseName();
        echo '<br>';
    } catch (\Exception $e) {
        echo $e;
    }
    if ($dossierToken) {
        echo 'Token récupérer <br>';
    }
    if ($dossierToken) {
        echo 'Token de dossier récupérer <br>';
    }
    if ($modifiedBuyBookings) {
        echo '<h2>Modified Buy Bookings</h2>';
        echo '<pre>';
        print_r($modifiedBuyBookings);
        echo '</pre>';
    }
    if ($modifiedSellBookings) {
        echo '<h2>Modified Sell Bookings</h2>';
        echo '<pre>';
        print_r($modifiedSellBookings);
        echo '</pre>';
    }
    ?>
</main>
@endsection