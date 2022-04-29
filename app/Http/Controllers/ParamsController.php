<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use App\Models\Octopus;
use App\Models\Zoho;
use App\Models\Dolibarr;

class ParamsController extends Controller
{
    public function index() {

        $octoReceive = Octopus::where("action", "receive")->get()->first();
        $octoSend = Octopus::where("action", "send")->get()->first();

        $zoho = Zoho::get()->first();

        $dolibarr = Dolibarr::get()->first();

        return View::make('params.index', 
            [
                "octoReceive" => $octoReceive,
                "octoSend" => $octoSend,
                "zoho" => $zoho,
                "dolibarr" => $dolibarr
            ]
        );
    }

    public function update(Request $request){
        $octoReceive = Octopus::where("action", "receive")->get()->first();
        
        $octoReceive->urlWs = $request->orUrlWs ? $request->orUrlWs : $octoReceive->urlWs;
        $octoReceive->softwareHouseUuid = $request->orUuid ? $request->orUuid : $octoReceive->softwareHouseUuid;
        $octoReceive->user = $request->orUser ? $request->orUser : $octoReceive->user;
        $octoReceive->password = $request->orPassword ? $request->orPassword : $octoReceive->password;
        $octoReceive->idDossier = $request->orIdDossier ? $request->orIdDossier : $octoReceive->idDossier;
        $octoReceive->bookYearKey = $request->orBookyearKey ? $request->orBookyearKey : $octoReceive->bookYearKey;
        $octoReceive->journalKeys = $request->orJournalKeys ? $request->orJournalKeys : $octoReceive->journalKeys;
        $octoReceive->accountKeyDefault = $request->orAccountKey ? $request->orAccountKey : $octoReceive->accountKeyDefault;

        $octoReceive->save();

        $octoSend = Octopus::where("action", "send")->get()->first();
        
        $octoSend->urlWs = $request->osUrlWs ? $request->osUrlWs : $octoSend->urlWs;
        $octoSend->softwareHouseUuid = $request->osUuid ? $request->osUuid : $octoSend->softwareHouseUuid;
        $octoSend->user = $request->osUser ? $request->osUser : $octoSend->user;
        $octoSend->password = $request->osPassword ? $request->osPassword : $octoSend->password;
        $octoSend->idDossier = $request->osIdDossier ? $request->osIdDossier : $octoSend->idDossier;
        $octoSend->bookYearKey = $request->osBookyearKey ? $request->osBookyearKey : $octoSend->bookYearKey;
        $octoSend->journalKeys = $request->osJournalKeys ? $request->osJournalKeys : $octoSend->journalKeys;
        $octoSend->accountKeyDefault = $request->osAccountKey ? $request->osAccountKey : $octoSend->accountKeyDefault;

        $octoSend->save();

        $zoho = Zoho::get()->first();

        $zoho->urlWsCreator = $request->zUrlWsCreator ? $request->zUrlWsCreator : $zoho->urlWsCreator;
        $zoho->urlWsAuth = $request->zUrlWsAuth ? $request->zUrlWsAuth : $zoho->urlWsAuth;
        $zoho->appLinkName = $request->zAppLinkName ? $request->zAppLinkName : $zoho->appLinkName;
        $zoho->accountOwnerName = $request->zAccountOwnerName ? $request->zAccountOwnerName : $zoho->accountOwnerName;
        $zoho->redirectUri = $request->zRedirectUri ? $request->zRedirectUri : $zoho->redirectUri;
        $zoho-> clientId = $request->zClientId ? $request->zClientId : $zoho->clientId;
        $zoho->clientSecret = $request->zClientSecret ? $request->zClientSecret : $zoho->clientSecret;
        $zoho->scope = $request->zScope ? $request->zScope : $zoho->scope;
        $zoho->edition = $request->zEdition ? $request->zEdition : $zoho->edition;

        $zoho->save();

        $dolibarr = Dolibarr::get()->first();

        $dolibarr->urlWS = $request->dUrlWs ? $request->dUrlWs : $dolibarr->urlWS;
        $dolibarr->apiKey = $request->dApiKey ? $request->dApiKey : $dolibarr->apiKey;

        $dolibarr->save();

        return Redirect::route('params.index');
    }
}
