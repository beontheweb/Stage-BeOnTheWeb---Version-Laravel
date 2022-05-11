<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Dolibarr;

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

    public function getRelationByName($name) {

        // URL
        $apiURL = $this->octopus->urlWs.'/dossiers/'.$this->octopus->idDossier.'/relations?name='.$name;
  
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

    public function getVatCodeKey($tvaPercentage){
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
            if($value["basePercentage"] == $tvaPercentage){
                return $value["code"];
            }
        }

        return "D21";

    }

    public function getJournalKeys(){
        $journalKeys = explode(",", $this->octopus->journalKeys);
        foreach ($journalKeys as $key => $journalKey) {
            $journalKeys[$key] = trim($journalKey);
        }

        return $journalKeys;
    }

    public function createBooking($booking, $relationId, $externalRealtionId) {

        $lastId = 0;
        foreach ($this->getBookings("V1", "1980-01-01 00:00:00.000") as $el) {
            if(isset($el["amount"])){
                $lastId = $el["documentSequenceNr"] > $lastId ? $el["documentSequenceNr"] : $lastId;
            }
        }

        // URL
        $apiURL = $this->octopus->urlWs.'/dossiers/'.$this->octopus->idDossier.'/buysellbookings';

        // POST Data
        $postInput = [
            'buySellBookingServiceData' => [
                'bookyearKey' => [
                    'id' => $this->octopus->bookYearKey
                ],
                'journalKey' => 'V1',
                'documentSequenceNr' => $lastId+1,
                'relationIdentificationServiceData' => [
                    'relationKey' => [
                        'id' => $relationId
                    ],
                    'externalRelationId' => $externalRealtionId
                ],
                'bookyearPeriodeNr' => "20160".ceil(date("m", $booking["date_creation"])/3),
                //'bookyearPeriodeNr' => date("Y", $booking["date_creation"])."0".ceil(date("m", $booking["date_creation"])/3),
                'documentDate' => date("Y-m-d", $booking["date"]),
                'expiryDate' => date("Y-m-d", $booking["date_lim_reglement"]),
                'comment' => $booking["note_public"],
                'reference'=> $booking["ref"],
                'amount' => (double)$booking["total_ttc"],
                'currencyCode' => $booking["multicurrency_code"],
                'exchangeRate' => 1.0,
                'bookingLines' => [],
                'paymentMethod' => 1
            ]
        ];

        foreach ($booking["lines"] as $key => $line) { 
            $dolibarrController = new DolibarrController();
            $dolibarrController->dolibarr = Dolibarr::get()->first();
            if($line["product_ref"] != null){
                $product = $dolibarrController->getProductByRef($line["product_ref"]);
            }

            $octoLine = [
                'accountKey' => $product["accountancy_code_sell"] ?? $this->octopus->accountKeyDefault,
                'baseAmount' => abs((double)$line["total_ht"]),
                'vatCodeKey' => (string)(int)$line["tva_tx"],
                'vatAmount' => abs((double)$line["total_tva"]),
                'comment' => strip_tags(($line["product_ref"] ?? $this->octopus->accountKeyDefault)." - ".($product["label"] ?? "")." - ".$line["description"])
            ];
            $postInput['buySellBookingServiceData']['bookingLines'][$key] = $octoLine;
        };
  
        // Headers
        $headers = [
            'accept' => '*/*',
            'dossierToken' => $this->dossierToken,
            'Content-Type' => 'application/json'
        ];
  
        $response = Http::withHeaders($headers)->post($apiURL, $postInput);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function createRelation($relation) {

        // URL
        $apiURL = $this->octopus->urlWs.'/dossiers/'.$this->octopus->idDossier.'/relations';

        // POST Data
        $postInput = [
            'name' => $relation["name"],
            'client' => ($relation["client"] == 1 || $relation["client"]) == 3 ? true : false,
            'supplier' => $relation["fournisseur"] == 1 ? true : false,
            'streetAndNr' => $relation["address"],
            'postalCode' => $relation["zip"],
            'city' => $relation["town"],
            'country' => $relation["country_code"],
            'telephone' => $relation["phone"],
            'email'=> $relation["email"],
            'fax' => $relation["fax"],
            'url' => $relation["url"],
            'vatNr' => $relation["tva_intra"],
            'vatType' => $relation["tva_assuj"] == 1 ? 1 : 10
        ];
  
        // Headers
        $headers = [
            'accept' => '*/*',
            'dossierToken' => $this->dossierToken,
            'Content-Type' => 'application/json'
        ];
  
        $response = Http::withHeaders($headers)->put($apiURL, $postInput);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }
}
