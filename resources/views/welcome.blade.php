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
        echo "Token de dossier récupérer";
    }
    ?>
</body>

</html>
