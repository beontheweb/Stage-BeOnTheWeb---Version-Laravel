<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
</head>

<body class="antialiased">
    <strong>Database Connected: </strong>
    <?php
    try {
        \DB::connection()->getPDO();
        echo \DB::connection()->getDatabaseName();
        echo "<br>";
    } catch (\Exception $e) {
        echo $e;
    }
    if($dossierToken){
        echo "Token récupérer <br>";
    }
    if($dossierToken){
        echo "Token de dossier récupérer <br>";
    }
    if($modifiedBuyBookings){
        echo "<h2>Modified Buy Bookings</h2>";
        echo '<pre>'; 
        print_r($modifiedBuyBookings); 
        echo '</pre>';
    }
    if($modifiedSellBookings){
        echo "<h2>Modified Sell Bookings</h2>";
        echo '<pre>'; 
        print_r($modifiedSellBookings); 
        echo '</pre>';
    }
    ?>
</body>

</html>
