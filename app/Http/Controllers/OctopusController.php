<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OctopusController extends Controller
{
    public $octopus;
    public $token;
    public $dossierToken;

    public function getToken() {
        // URL
        $apiURL = $this->octopus->urlWs.'/authentication';

        // POST Data
        $postInput = [
            'user' => $this->octopus->user,
            'password' => $this->octopus->password
        ];
  
        // Headers
        $headers = [
            'Accept' => 'application/json',
            'softwareHouseUuid' => $this->octopus->softwareHouseUuid,
            'Content-Type' => 'application/json'
        ];
  
        $response = Http::withHeaders($headers)->post($apiURL, $postInput);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody["token"];
    }

    public function getDossierToken() {
        // URL
        $apiURL = $this->octopus->urlWs.'/dossiers?dossierId='.$this->octopus->idDossier;
  
        // Headers
        $headers = [
            'Accept' => 'application/json',
            'token' => $this->token,
        ];
  
        $response = Http::withHeaders($headers)->post($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody["Dossiertoken"];
    }

    public function getBookings($journalKey, $timestamp) {

        // URL
        $apiURL = $this->octopus->urlWs.'/dossiers/'.$this->octopus->idDossier.'/buysellbookings/modified?bookyearId='.$this->octopus->bookYearKey.'&journalKey='.$journalKey.'&modifiedTimeStamp='.$timestamp;
  
        // Headers
        $headers = [
            'dossierToken' => $this->dossierToken
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function getRelation($relationId) {

        // URL
        $apiURL = $this->octopus->urlWs.'/dossiers/'.$this->octopus->idDossier.'/relations?relationId='.$relationId;
  
        // Headers
        $headers = [
            'dossierToken' => $this->dossierToken
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function getVatBasePercentage($tvaCodeKey){
        // URL
        $apiURL = $this->octopus->urlWs.'/dossiers/'.$this->octopus->idDossier.'/vatcodes';
  
        // Headers
        $headers = [
            'dossierToken' => $this->dossierToken
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        //Cherche dans le tableau reçu pour la bonne valeur
        foreach ($responseBody as $value) {
            if($value["code"] == $tvaCodeKey){
                return $value["basePercentage"];
            }
        }

        return 21.0;

    }

    public function getJournalKeys(){
        $journalKeys = explode(",", $this->octopus->journalKeys);
        foreach ($journalKeys as $key => $journalKey) {
            $journalKeys[$key] = trim($journalKey);
        }

        return $journalKeys;
    }
}
