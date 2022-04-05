<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\BookingLine;
use App\Models\GeneralParam;
use App\Models\Relation;
use App\Models\Octopus;
use App\Models\Zoho;

class DashBoardController extends Controller
{

    public $token;
    public $dossierToken;
    public $octopus;
    public $zoho;

    public function index() {

        $journalKeys = explode(",", Octopus::get()->first()->journalKeys);
        foreach ($journalKeys as $key => $journalKey) {
            $journalKeys[$key] = trim($journalKey);
        }

        return View::make('dashboard.index', 
            [
             "modifiedBookings" => null,
             "lastUpdated" => GeneralParam::get()->first()->lastUpdated,
             "journals" => $journalKeys
            ]
        );
    }

    public function updateDB(Request $request) {
        $completeBookings = [];
        $journalKeys = $request->get("journals");
        $this->octopus = Octopus::get()->first();
        $this->zoho = Zoho::get()->first();
        $this->token = $this->getToken();
        $this->dossierToken = $this->getDossierToken();
        $params = GeneralParam::get()->first();

        $timestamp = $request->timestamp . " 00:00:00.000";

        //Pour chaque clé de journal, appelle l'api octopus pour récupérer les bookings correspondant et mets à jour la base de donnée
        if ($journalKeys) {
            foreach ($journalKeys as $journalKey) {
                //Retire les espaces
                $journalKey = trim($journalKey);
                
                $bookings = $this->getBookings($journalKey, $timestamp);
                if(!isset($bookings["technicalInfo"])){
                    $this->fillDB($bookings);
                }
                //Juste pour le débuggage
                array_push($completeBookings, $bookings);
            }

            $params->lastUpdated = date('Y-m-d H:i:s');
            $params->save();
        }

        $journalKeys = explode(",", $this->octopus->journalKeys);
        foreach ($journalKeys as $key => $journalKey) {
            $journalKeys[$key] = trim($journalKey);
        }

        return View::make('dashboard.index', 
            [
             "modifiedBookings" => $completeBookings,
             "lastUpdated" => $params->lastUpdated,
             "journals" => $journalKeys
            ]
        );
    }

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

    //Vide les tables de donnée. Seulement utilsé pour des tests
    public function truncate(){
        DB::table('bookings')->truncate();
        DB::table('booking_lines')->truncate();
    }

    public function fillDB($array){

        foreach ($array as $value) {
            $this->createBooking($value);
        }
    }

    //Crée ou update un booking si alphaNumericalNumber existe déjà
    public function createBooking($value){

            $alphaNumericalNumber = substr($value["bookyearPeriodeNr"], 0, 4)."-".$value["journalKey"]."-".sprintf("%03d", $value["documentSequenceNr"]);

            if (Relation::where('externalID', $value["relationIdentificationServiceData"]["relationKey"]["id"])->exists()) {
                $relationId = Relation::where('externalID', $value["relationIdentificationServiceData"]["relationKey"]["id"])->pluck('id')[0];
             }
             else{
                $relationId = $this->createRelation($value["relationIdentificationServiceData"]["relationKey"]["id"]);
             }

            $booking = Booking::updateOrCreate(
                ['alphaNumericalNumber' => $alphaNumericalNumber],
                [
                    'documentNumber' => $value["documentSequenceNr"],
                    'amount' => $value["amount"],
                    'bookYearId' => $value["bookyearKey"]["id"],
                    'bookYearNumber' => $value["bookyearPeriodeNr"],
                    'comment' => $value["comment"],
                    'currency' => $value["currencyCode"],
                    'bookingDate' => $value["documentDate"],
                    'expiryDate' => $value["expiryDate"],
                    'echangeRate' => $value["exchangeRate"],
                    'journalKey' => $value["journalKey"],
                    'paymentMethod' => $value["paymentMethod"],
                    'reference' => $value["reference"],
                    'relation_id' => $relationId
                ]
            );

            foreach ($value["bookingLines"] as $key => $line) {
                $this->createBookingLine($line, $booking->id, $key+1, $alphaNumericalNumber);
            }

            return $booking->id;
    }

    //Crée ou update une booking line si alphaNumericalNumber existe déjà
    public function createBookingLine($value, $id, $lineID, $alphaNumericalNumber){

        $alphaNumericalNumber = $alphaNumericalNumber."-".sprintf("%02d", $lineID);

        BookingLine::updateOrCreate(
            ['alphaNumericalNumber' => $alphaNumericalNumber],
            [
                'accountKey' => $value["accountKey"],
                'baseAmount' => $value["baseAmount"],
                'vatAmount' => $value["vatAmount"],
                'vatCodeKey' => $value["vatCodeKey"],
                'vatPercentage' => array_key_exists("vatRecupPercentage", $value) ? $value["vatRecupPercentage"] : 100,
                'vatBasePercentage' => $this->getVatBasePercentage($value["vatCodeKey"]),
                'comment' => $value["comment"],
                'booking_id' => $id,
            ]
        );

    }

    //Crée une nouvelle relation
    public function createRelation($id){

            $value = $this->getRelation($id);
            //Renvoie un tableau dans un tableau, il faut sélectionner le premier élément
            $value = $value[0];

            $relation = new Relation();
 
            $relation->externalID = $id;
            $relation->name = $value["name"];
            $relation->firstName = $value["firstName"];
            $relation->contactPerson = $value["contactPerson"];
            $relation->telephone = $value["telephone"];
            $relation->mobile = $value["mobile"];
            $relation->fax = $value["fax"];
            $relation->email = $value["email"];
            $relation->active = $value["active"];
            $relation->bankAccount = $value["bankAccountNr"];
            $relation->bicCode = $value["bicCode"];
            $relation->TVAType = $value["vatType"];
            $relation->TVANumber = $value["vatNr"];
            $relation->ibanAccount = $value["ibanAccountNr"];
            $relation->currency = $value["currencyCode"];
            $relation->corporationType = $value["corporationType"];
            $relation->financialDiscount = $value["financialDiscount"];
            $relation->defaultSupplier = 0;
            $relation->defaultClient = 0;
            $relation->hasClient = $value["client"];
            $relation->hasSupplier = $value["supplier"];
            $relation->expirationDays = $value["expirationDays"];
            $relation->expirationType = $value["expirationType"];
            $relation->country = $value["country"];
            $relation->city = array_key_exists("city", $value) ? $value["city"] : "";
            $relation->postalCode = $value["postalCode"];
            $relation->street = $value["streetAndNr"];
            $relation->deliveryCountry = $value["deliveryCountry"];
            $relation->deliveryCity = array_key_exists("deliveryCity", $value) ? $value["deliveryCity"] : "";
            $relation->deliveryPostalCode = $value["deliveryPostalCode"];
            $relation->deliveryStreet = $value["deliveryStreetAndNr"];
            $relation->remarks = $value["remarks"];
            $relation->sddActive = $value["sddActive"];
            $relation->sddMandateType = $value["sddMandateType"];
            $relation->sddSeqtype = $value["sddSeqtype"];
            $relation->searchField1 = $value["searchField1"];
            $relation->searchField2 = $value["searchField2"];
 
            $relation->save();
            return $relation->id;
    }
}
