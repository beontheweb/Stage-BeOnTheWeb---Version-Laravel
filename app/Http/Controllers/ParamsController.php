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
    /**
     * Renvoie vers la vue index dans paramètres
     */
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

    /**
     * Met à jour les paramètres si leur valeur a été changée dans le formulaire
     */
    public function update(Request $request){
        //Met à jour les paramètres de l'Octopus de réception
        $octoReceive = Octopus::where("action", "receive")->get()->first();
        
        $octoReceive->urlWs = $request->orUrlWs ?? $octoReceive->urlWs;
        $octoReceive->softwareHouseUuid = $request->orUuid ?? $octoReceive->softwareHouseUuid;
        $octoReceive->user = $request->orUser ?? $octoReceive->user;
        $octoReceive->password = $request->orPassword ?? $octoReceive->password;
        $octoReceive->idDossier = $request->orIdDossier ?? $octoReceive->idDossier;
        $octoReceive->bookYearKey = $request->orBookyearKey ?? $octoReceive->bookYearKey;
        $octoReceive->journalKeys = $request->orJournalKeys ?? $octoReceive->journalKeys;
        $octoReceive->accountKeyDefault = $request->orAccountKey ?? $octoReceive->accountKeyDefault;

        $octoReceive->save();

        //Met à jour les paramètres de l'Octopus d'envoi
        $octoSend = Octopus::where("action", "send")->get()->first();
        
        $octoSend->urlWs = $request->osUrlWs ?? $octoSend->urlWs;
        $octoSend->softwareHouseUuid = $request->osUuid ?? $octoSend->softwareHouseUuid;
        $octoSend->user = $request->osUser ?? $octoSend->user;
        $octoSend->password = $request->osPassword ?? $octoSend->password;
        $octoSend->idDossier = $request->osIdDossier ?? $octoSend->idDossier;
        $octoSend->bookYearKey = $request->osBookyearKey ?? $octoSend->bookYearKey;
        $octoSend->journalKeys = $request->osJournalKeys ?? $octoSend->journalKeys;
        $octoSend->accountKeyDefault = $request->osAccountKey ?? $octoSend->accountKeyDefault;

        $octoSend->save();

        //Met à jour les paramètres de Zoho
        $zoho = Zoho::get()->first();

        $zoho->urlWsCreator = $request->zUrlWsCreator ?? $zoho->urlWsCreator;
        $zoho->urlWsAuth = $request->zUrlWsAuth ?? $zoho->urlWsAuth;
        $zoho->appLinkName = $request->zAppLinkName ?? $zoho->appLinkName;
        $zoho->accountOwnerName = $request->zAccountOwnerName ?? $zoho->accountOwnerName;
        $zoho->redirectUri = $request->zRedirectUri ?? $zoho->redirectUri;
        $zoho-> clientId = $request->zClientId ?? $zoho->clientId;
        $zoho->clientSecret = $request->zClientSecret ?? $zoho->clientSecret;
        $zoho->scope = $request->zScope ?? $zoho->scope;
        $zoho->edition = $request->zEdition ?? $zoho->edition;

        $zoho->save();

        //Met à jour les paramètres de Dolibarr
        $dolibarr = Dolibarr::get()->first();

        $dolibarr->urlWS = $request->dUrlWs ?? $dolibarr->urlWS;
        $dolibarr->apiKey = $request->dApiKey ?? $dolibarr->apiKey;

        $dolibarr->save();

        return Redirect::route('params.index');
    }
}
