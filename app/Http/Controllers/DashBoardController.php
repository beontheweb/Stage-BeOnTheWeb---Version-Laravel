<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\BookingLine;
use App\Models\Relation;

class DashBoardController extends Controller
{

    public $token;
    public $dossierToken;

    public function index() {
        $this->token = $this->getToken();
        $this->dossierToken = $this->getDossierToken();
        $buyBookings = $this->getBookings(true);
        $sellBookings = $this->getBookings(false);

        $this->truncate();
        $this->fillDB($buyBookings);
        $this->fillDB($sellBookings);

 
        return View::make('dashboard.index', 
            [
             "token" => $this->token, 
             "dossierToken" => $this->dossierToken, 
             "modifiedBuyBookings" => $buyBookings,
             "modifiedSellBookings" => $sellBookings
            ]
        );
    }

    public function getToken() {
        // URL
        $apiURL = 'https://service.inaras.be/octopus-rest-api/v1/authentication';

        // POST Data
        $postInput = [
            'user' => env("OCTOPUS_API_USER"),
            'password' => env("OCTOPUS_API_PWD")
        ];
  
        // Headers
        $headers = [
            'Accept' => 'application/json',
            'softwareHouseUuid' => env("OCTOPUS_API_UUID"),
            'Content-Type' => 'application/json'
        ];
  
        $response = Http::withHeaders($headers)->post($apiURL, $postInput);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody["token"];
    }

    public function getDossierToken($dossierId = 45119) {
        // URL
        $apiURL = 'https://service.inaras.be/octopus-rest-api/v1/dossiers?dossierId='.$dossierId;
  
        // Headers
        $headers = [
            'Accept' => 'application/json',
            'token' => $this->token,
        ];
  
        $response = Http::withHeaders($headers)->post($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody["Dossiertoken"];
    }

    public function getBookings($isBuy, $dossierId = 45119) {

        //Switch between Buy and Sell Bookings
        $journalKey = $isBuy ? "A1" : "V1";

        // URL
        $apiURL = 'https://service.inaras.be/octopus-rest-api/v1/dossiers/'.$dossierId.'/buysellbookings/modified?bookyearId=1&journalKey='.$journalKey.'&modifiedTimeStamp=2020-02-08%2014%3A55%3A00.000';
  
        // Headers
        $headers = [
            'dossierToken' => $this->dossierToken
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function getRelation($relationId, $dossierId = 45119) {

        // URL
        $apiURL = 'https://service.inaras.be/octopus-rest-api/v1/dossiers/'.$dossierId.'/relations?relationId='.$relationId;
  
        // Headers
        $headers = [
            'dossierToken' => $this->dossierToken
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function getVatBasePercentage($tvaCodeKey, $dossierId = 45119){
        // URL
        $apiURL = 'https://service.inaras.be/octopus-rest-api/v1/dossiers/'.$dossierId.'/vatcodes';
  
        // Headers
        $headers = [
            'dossierToken' => $this->dossierToken
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        foreach ($responseBody as $value) {
            if($value["code"] == $tvaCodeKey){
                return $value["basePercentage"];
            }
        }

        return 21.0;

    }

    public function truncate(){
        DB::table('bookings')->truncate();
        DB::table('booking_lines')->truncate();
    }

    public function fillDB($array){

        foreach ($array as $value) {
            $id = $this->createBooking($value);

            foreach ($value["bookingLines"] as $line) {
                $this->createBookingLine($line, $id);
            }
        }
    }

    public function createBooking($value){
            
            $booking = new Booking;
 
            $booking->documentNumber = $value["documentSequenceNr"];
            $booking->alphaNumericalNumber = substr($value["bookyearPeriodeNr"], 0, 4)."-".$value["journalKey"]."-".sprintf("%03d", $value["documentSequenceNr"]);
            $booking->amount = $value["amount"];
            $booking->bookYearId = $value["bookyearKey"]["id"];
            $booking->bookYearNumber = $value["bookyearPeriodeNr"];
            $booking->comment = $value["comment"];
            $booking->currency = $value["currencyCode"];
            $booking->bookingDate = $value["documentDate"];
            $booking->expiryDate = $value["expiryDate"];
            $booking->echangeRate = $value["exchangeRate"];
            $booking->journalKey = $value["journalKey"];
            $booking->paymentMethod = $value["paymentMethod"];
            $booking->reference = $value["reference"];

            if (Relation::where('externalID', $value["relationIdentificationServiceData"]["relationKey"]["id"])->exists()) {
                $relationId = Relation::where('externalID', $value["relationIdentificationServiceData"]["relationKey"]["id"])->pluck('id')[0];
             }
             else{
                $relationId = $this->createRelation($value["relationIdentificationServiceData"]["relationKey"]["id"]);
             }

            $booking->relation_id = $relationId;
 
            $booking->save();

            return $booking->id;
    }

    public function createBookingLine($value, $id){
        
            $line = new BookingLine();
 
            $line->accountKey = $value["accountKey"];
            $line->baseAmount = $value["baseAmount"];
            $line->vatAmount = $value["vatAmount"];
            $line->vatCodeKey = $value["vatCodeKey"];
            $line->vatPercentage = array_key_exists("vatRecupPercentage", $value) ? $value["vatRecupPercentage"] : 100;
            $line->vatBasePercentage = $this->getVatBasePercentage($value["vatCodeKey"]);
            $line->comment = $value["comment"];
            $line->booking_id = $id;
 
            $line->save();

    }

    public function createRelation($id){

            $value = $this->getRelation($id);
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
