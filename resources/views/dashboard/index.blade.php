@extends('template.index')

@section('main')
<main class="container p-2">
    <a href="../../updateDB/2020-02-08 14:55:00.000" class="btn btn-primary">Reset DB</a>
    <a href="../../updateDB/{{date_create()->modify('-1 days')->format('Y-m-d H:i:s.v')}}" class="btn btn-primary">Dernière 24h</a>
    <br>
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