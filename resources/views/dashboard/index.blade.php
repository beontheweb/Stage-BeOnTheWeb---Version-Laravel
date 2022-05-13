@extends('template.index')

@section('main')
<main class="container p-2">
    <h3 class="mt-3">Base de Donnée</h3>
    <a href="/resetDatabase"><button type="submit" class="btn btn-primary">Réinitialiser base de donnée</button></a>
    @isset($reset)
        <br/>
        {{$reset}}
    @endisset
    <form action="{{ route('dashboard.updateDB') }}" method="get">
        @csrf

        <h3 class="mt-3">Octopus</h3>
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
        <button type="submit" class="btn btn-primary mt-3">Récupérer les journaux d'Octopus</button>
    </form>
    <h3 class="mt-3">Zoho Creator</h3>
    <a href="https://accounts.zoho.com/signin?servicename=AaaServer&serviceurl=https%3A%2F%2Faccounts.zoho.com%2Foauth%2Fv2%2Fauth%3Fresponse_type%3Dcode%26client_id%3D1000.KKL6WQ1Y5GKIG0B1KBAF7ZXUWY501L%26scope%3DZohoCreator.form.CREATE,ZohoCreator.report.CREATE,ZohoCreator.report.READ,ZohoCreator.report.UPDATE,ZohoCreator.report.DELETE,ZohoCreator.meta.form.READ,ZohoCreator.meta.application.READ,ZohoCreator.dashboard.READ%26redirect_uri%3Dhttps://hub.beontheweb.be%26access_type%3Doffline"><button type="submit" class="btn {{$lastZohoAuth ? 'btn-success' : 'btn-danger'}}">Auth Zoho</button></a>
    <a href="/sendDataZoho"><button type="submit" class="btn btn-primary">Envoyer les bookings vers Zoho Creator</button></a>
    <form action="{{ route('dashboard.transferDoliOcto') }}" method="get">
        @csrf

        <h3 class="mt-3">Dolibarr</h3>
        <div class="mb-3">
            <label for="timestamp" class="form-label">Timestamp</label>
            <input type="date" class="form-control" name="timestamp" id="timestamp" value={{now()}}>
        </div>
        <button type="submit" class="btn btn-primary">Récupérer les factures de Dolibarr et les envoyer vers Octopus</button>
    </form>
    <br>
    <?php
    if ($modifiedBookings) {
        echo '<h3 class="mt-2">Modified Bookings</h3>';
        echo '<pre>';
        print_r($modifiedBookings);
        echo '</pre>';
    }
    ?>
    <?php
    if (isset($octoBookingLog)) {
        echo '<h3 class="mt-2">Log</h3>';
        echo '<pre>';
            if(count($octoBookingLog) == 0){
                print_r("Les Factures trouvées existent déjà dans Octopus");
            }
            else{
                print_r($octoBookingLog);
            }
        echo '</pre>';
    }
    ?>
    <?php
    if (isset($zohoBookingLog)) {
        echo '<h3 class="mt-2">Log</h3>';
        echo '<pre>';
            if(count($zohoBookingLog) == 0){
                print_r("Aucunes factures ajoutées ou modifiées dans Zoho Creator");
            }
            else{
                print_r($zohoBookingLog);
            }
        echo '</pre>';
    }
    ?>
</main>
@endsection