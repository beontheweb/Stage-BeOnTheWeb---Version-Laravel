<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use App\Models\Octopus;
use App\Models\Zoho;

class ParamsController extends Controller
{
    public function index() {

        $octopus = Octopus::all()[0];

        $zoho = Zoho::all()[0];

        return View::make('params.index', 
            [
                "octopus" => $octopus,
                "zoho" => $zoho
            ]
        );
    }

    public function update($octopusId, $zohoId, Request $request){
        $octopus = Octopus::find($octopusId);
        
        $octopus->urlWs = $request->oUrlWs ? $request->oUrlWs : $octopus->urlWs;
        $octopus->softwareHouseUuid = $request->oUuid ? $request->oUuid : $octopus->softwareHouseUuid;
        $octopus->user = $request->oUser ? $request->oUser : $octopus->user;
        $octopus->password = $request->oPassword ? $request->oPassword : $octopus->password;
        $octopus->idDossier = $request->oIdDossier ? $request->oIdDossier : $octopus->idDossier;
        $octopus->bookYearKey = $request->oBookyearKey ? $request->oBookyearKey : $octopus->bookYearKey;
        $octopus->journalKeys = $request->oJournalKeys ? $request->oJournalKeys : $octopus->journalKeys;

        $octopus->save();

        $zoho = Zoho::find($zohoId);

        $zoho->urlWsCreator = $request->zUrlWsCreator ? $request->zUrlWsCreator : $zoho->urlWsCreator;
        $zoho->urlWsAuth = $request->zUrlWsAuth ? $request->zUrlWsAuth : $zoho->urlWsAuth;
        $zoho-> clientId = $request->zClientId ? $request->zClientId : $zoho->clientId;
        $zoho->clientSecret = $request->zClientSecret ? $request->zClientSecret : $zoho->clientSecret;
        $zoho->scope = $request->zScope ? $request->zScope : $zoho->scope;
        $zoho->edition = $request->zEdition ? $request->zEdition : $zoho->edition;

        $zoho->save();

        return Redirect::route('params.index');
    }
}
