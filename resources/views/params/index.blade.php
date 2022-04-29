@extends('template.index')

@section('main')
<main class="m-4">
    <form action="{{ route('params.update') }}" method="post">
        @csrf
        @method('PATCH')

        <h2>Octopus - Réception des factures</h2>
        <div class="mb-3">
            <label for="orUrlWs" class="form-label">URL WS</label>
            <input type="text" class="form-control" name="orUrlWs" id="orUrlWs" value="{{$octoReceive->urlWs}}">
        </div>
        <div class="mb-3">
            <label for="orUuid" class="form-label">Software House Uuid</label>
            <input type="text" class="form-control" name="orUuid" id="orUuid" value="{{$octoReceive->softwareHouseUuid}}">
        </div>
        <div class="mb-3">
            <label for="orUser" class="form-label">User</label>
            <input type="text" class="form-control" name="orUser" id="orUser" value="{{$octoReceive->user}}">
        </div>
        <div class="mb-3">
            <label for="orPassword" class="form-label">Password</label>
            <input type="text" class="form-control" name="orPassword" id="orPassword" value="{{$octoReceive->password}}">
        </div>
        <div class="mb-3">
            <label for="orIdDossier" class="form-label">ID Dossier</label>
            <input type="text" class="form-control" name="orIdDossier" id="orIdDossier" value="{{$octoReceive->idDossier}}">
        </div>
        <div class="mb-3">
            <label for="orBookyearKey" class="form-label">Bookyear Key</label>
            <input type="text" class="form-control" name="orBookyearKey" id="orBookyearKey" value="{{$octoReceive->bookYearKey}}">
        </div>
        <div class="mb-3">
            <label for="orJournalKeys" class="form-label">Journals' Keys (A1, V1, ...)</label>
            <input type="text" class="form-control" name="orJournalKeys" id="orJournalKeys" value="{{$octoReceive->journalKeys}}">
        </div>
        <div class="mb-3">
            <label for="orAccountKey" class="form-label">Default Account Key</label>
            <input type="text" class="form-control" name="orAccountKey" id="orAccountKey" value="{{$octoReceive->accountKeyDefault}}">
        </div>

        <h2>Octopus - Envoi des factures de vente</h2>
        <div class="mb-3">
            <label for="osUrlWs" class="form-label">URL WS</label>
            <input type="text" class="form-control" name="osUrlWs" id="osUrlWs" value="{{$octoSend->urlWs}}">
        </div>
        <div class="mb-3">
            <label for="osUuid" class="form-label">Software House Uuid</label>
            <input type="text" class="form-control" name="osUuid" id="osUuid" value="{{$octoSend->softwareHouseUuid}}">
        </div>
        <div class="mb-3">
            <label for="osUser" class="form-label">User</label>
            <input type="text" class="form-control" name="osUser" id="osUser" value="{{$octoSend->user}}">
        </div>
        <div class="mb-3">
            <label for="osPassword" class="form-label">Password</label>
            <input type="text" class="form-control" name="osPassword" id="osPassword" value="{{$octoSend->password}}">
        </div>
        <div class="mb-3">
            <label for="osIdDossier" class="form-label">ID Dossier</label>
            <input type="text" class="form-control" name="osIdDossier" id="osIdDossier" value="{{$octoSend->idDossier}}">
        </div>
        <div class="mb-3">
            <label for="osBookyearKey" class="form-label">Bookyear Key</label>
            <input type="text" class="form-control" name="osBookyearKey" id="osBookyearKey" value="{{$octoSend->bookYearKey}}">
        </div>
        <div class="mb-3">
            <label for="osJournalKeys" class="form-label">Journals' Keys (A1, V1, ...)</label>
            <input type="text" class="form-control" name="osJournalKeys" id="osJournalKeys" value="{{$octoSend->journalKeys}}">
        </div>
        <div class="mb-3">
            <label for="osAccountKey" class="form-label">Default Account Key</label>
            <input type="text" class="form-control" name="osAccountKey" id="osAccountKey" value="{{$octoSend->accountKeyDefault}}">
        </div>

        <h2>Zoho</h2>
        <div class="mb-3">
            <label for="zUrlWsCreator" class="form-label">URL WS Creator</label>
            <input type="text" class="form-control" name="zUrlWsCreator" id="zUrlWsCreator" value="{{$zoho->urlWsCreator}}">
        </div>
        <div class="mb-3">
            <label for="zUrlWsAuth" class="form-label">URL WS Auth</label>
            <input type="text" class="form-control" name="zUrlWsAuth" id="zUrlWsAuth" value="{{$zoho->urlWsAuth}}">
        </div>
        <div class="mb-3">
            <label for="zAppLinkName" class="form-label">App Link Name</label>
            <input type="text" class="form-control" name="zAppLinkName" id="zAppLinkName" value="{{$zoho->appLinkName}}">
        </div>
        <div class="mb-3">
            <label for="zAccountOwnerName" class="form-label">Account Owner Name</label>
            <input type="text" class="form-control" name="zAccountOwnerName" id="zAccountOwnerName" value="{{$zoho->accountOwnerName}}">
        </div>
        <div class="mb-3">
            <label for="zRedirectUri" class="form-label">Redirect URI</label>
            <input type="text" class="form-control" name="zRedirectUri" id="zRedirectUri" value="{{$zoho->redirectUri}}">
        </div>
        <div class="mb-3">
            <label for="zClientId" class="form-label">Client Id</label>
            <input type="text" class="form-control" name="zClientId" id="zClientId" value="{{$zoho->clientId}}">
        </div>
        <div class="mb-3">
            <label for="zClientSecret" class="form-label">Client Secret</label>
            <input type="text" class="form-control" name="zClientSecret" id="zClientSecret" value="{{$zoho->clientSecret}}">
        </div>
        <div class="mb-3">
            <label for="zScope" class="form-label">Scope</label>
            <input type="text" class="form-control" name="zScope" id="zScope" value="{{$zoho->scope}}">
        </div>
        <div class="mb-3">
            <label for="zEdition" class="form-label">Edition</label>
            <input type="text" class="form-control" name="zEdition" id="zEdition" value="{{$zoho->edition}}">
        </div>

        <h2>Dolibarr</h2>
        <div class="mb-3">
            <label for="dUrlWs" class="form-label">URL WS</label>
            <input type="text" class="form-control" name="dUrlWs" id="dUrlWs" value="{{$dolibarr->urlWS}}">
        </div>
        <div class="mb-3">
            <label for="dApiKey" class="form-label">API Key</label>
            <input type="text" class="form-control" name="dApiKey" id="dApiKey" value="{{$dolibarr->apiKey}}">
        </div>

        <button type="submit" class="btn btn-primary me-3">Modifier</button>
        <a href="../home" class="btn btn-primary me-3">Annuler</a>

    </form>
    
</main>
@endsection