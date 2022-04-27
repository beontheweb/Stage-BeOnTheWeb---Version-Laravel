@extends('template.index')

@section('main')
<main class="m-4">
    <form action="{{ route('params.update', ["octopusId" => $octopus->id, "zohoId" => $zoho->id, "dolibarrId" => $dolibarr->id]) }}" method="post">
        @csrf
        @method('PATCH')

        <h2>Octopus</h2>
        <div class="mb-3">
            <label for="oUrlWs" class="form-label">URL WS</label>
            <input type="text" class="form-control" name="oUrlWs" id="oUrlWs" value="{{$octopus->urlWs}}">
        </div>
        <div class="mb-3">
            <label for="oUuid" class="form-label">Software House Uuid</label>
            <input type="text" class="form-control" name="oUuid" id="oUuid" value="{{$octopus->softwareHouseUuid}}">
        </div>
        <div class="mb-3">
            <label for="oUser" class="form-label">User</label>
            <input type="text" class="form-control" name="oUser" id="oUser" value="{{$octopus->user}}">
        </div>
        <div class="mb-3">
            <label for="oPassword" class="form-label">Password</label>
            <input type="text" class="form-control" name="oPassword" id="oPassword" value="{{$octopus->password}}">
        </div>
        <div class="mb-3">
            <label for="oIdDossier" class="form-label">ID Dossier</label>
            <input type="text" class="form-control" name="oIdDossier" id="oIdDossier" value="{{$octopus->idDossier}}">
        </div>
        <div class="mb-3">
            <label for="oBookyearKey" class="form-label">Bookyear Key</label>
            <input type="text" class="form-control" name="oBookyearKey" id="oBookyearKey" value="{{$octopus->bookYearKey}}">
        </div>
        <div class="mb-3">
            <label for="oJournalKeys" class="form-label">Journals' Keys (A1, V1, ...)</label>
            <input type="text" class="form-control" name="oJournalKeys" id="oJournalKeys" value="{{$octopus->journalKeys}}">
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