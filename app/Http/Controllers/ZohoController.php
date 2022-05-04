<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ZohoController extends Controller
{
    public $zoho;

    public function getToken($code) {
        // URL
        $apiURL = $this->zoho->urlWsAuth.'/oauth/v2/token?grant_type=authorization_code&client_id='.$this->zoho->clientId.'&client_secret='.$this->zoho->clientSecret.'&redirect_uri='.$this->zoho->redirectUri.'&code='.$code;
  
        $response = Http::post($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function refreshToken() {
        // URL
        $apiURL = $this->zoho->urlWsAuth.'/oauth/v2/token?refresh_token='.$this->zoho->refreshToken.'&client_id='.$this->zoho->clientId.'&client_secret='.$this->zoho->clientSecret.'&grant_type=refresh_token';
  
        $response = Http::post($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody["access_token"];
    }

    public function createBooking($booking, $relationId) {

        if($booking->journalKey == "A1"){
            $type = "Achat";
        }
        else{
            $type = "Vente";
        }

        // URL
        $apiURL = $this->zoho->urlWsCreator.'/api/v2/'.$this->zoho->accountOwnerName.'/'.$this->zoho->appLinkName.'/form/'.$type;

        // Headers
        $headers = [
            'Authorization' => 'Zoho-oauthtoken '.$this->zoho->accessToken
        ];

        // POST Data
        $postInput = [
            'data' => [
                'Edition_FIFCL' => "56564000000583003",
                'N' => $booking->alphaNumericalNumber,
                'Num_facture' => $booking->reference,
                'PERIODE' => $booking->bookYearNumber,
                'DATE1' => $booking->expiryDate,
                'Tiers' => $relationId,
                'Article_budg_taire' => null,
                'Libell' => $booking->comment,
                'Montant_HTVA' => $booking->HTVA,
                'TVA' => $booking->TVA,
                'Montant_TVAC' => $booking->amount,
                'subside' => null,
                'Pay' => "Non",
                'Date_paiement' => null,
                'Paye_HTVA' => null,
                'Paye_TVAC' => null,
                'Extrait_compte' => null,
                'Page_extrait' => null,
                'Preuve' => "Non",
                'Achat_Echange' => "Non",
                'Subventionnable' => "Oui",
            ]
        ];

        ini_set('precision', 10);
        ini_set('serialize_precision', 10);

        $response = Http::withHeaders($headers)->post($apiURL, $postInput);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function updateBooking($bookingId, $booking, $relationId) {

        if($booking->journalKey == "A1"){
            $type = "Journals_Report";
        }
        else{
            $type = "Vente_Report";
        }

        // URL
        $apiURL = $this->zoho->urlWsCreator.'/api/v2/'.$this->zoho->accountOwnerName.'/'.$this->zoho->appLinkName.'/report/'.$type.'/'.$bookingId;

        // Headers
        $headers = [
            'Authorization' => 'Zoho-oauthtoken '.$this->zoho->accessToken
        ];

        // POST Data
        $postInput = [
            'data' => [
                'Edition_FIFCL' => "56564000000583003",
                'N' => $booking->alphaNumericalNumber,
                'Num_facture' => $booking->reference,
                'PERIODE' => $booking->bookYearNumber,
                'DATE1' => $booking->expiryDate,
                'Tiers' => $relationId,
                'Article_budg_taire' => null,
                'Libell' => $booking->comment,
                'Montant_HTVA' => $booking->HTVA,
                'TVA' => $booking->TVA,
                'Montant_TVAC' => $booking->amount,
                'subside' => null,
                'Pay' => null,
                'Date_paiement' => null,
                'Paye_HTVA' => null,
                'Paye_TVAC' => null,
                'Extrait_compte' => null,
                'Page_extrait' => null,
                'Preuve' => "Non",
                'Achat_Echange' => "Non",
                'Subventionnable' => "Oui",
            ]
        ];

        ini_set('precision', 10);
        ini_set('serialize_precision', 10);

        $response = Http::withHeaders($headers)->patch($apiURL, $postInput);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function getBookings() {
        $offset = 0;
        $bool = true;
        $bookings = [];

        $bookingsWithOffset = $this->getBookingsWithOffset("A1", $offset, 199);
        if($bookingsWithOffset["code"] == 3100){
            $bool = false;
        }
        elseif($bookingsWithOffset["code"] == 3000){
            $bookings = [...$bookings,...$bookingsWithOffset["data"]];
        }
        else{
            dd($bookingsWithOffset);
        }
        $offset += 200;

        while($bool){
            $bookingsWithOffset = $this->getBookingsWithOffset("A1", $offset, 200);
            if($bookingsWithOffset["code"] == 3100){
                $bool = false;
            }
            elseif($bookingsWithOffset["code"] == 3000){
                $bookings = [...$bookings,...$bookingsWithOffset["data"]];
            }
            else{
                dd($bookingsWithOffset);
            }
            $offset += 200;
        }

        $offset = 0;
        $bool = true;

        $bookingsWithOffset = $this->getBookingsWithOffset("V1", $offset, 199);
        if($bookingsWithOffset["code"] == 3100){
            $bool = false;
        }
        elseif($bookingsWithOffset["code"] == 3000){
            $bookings = [...$bookings,...$bookingsWithOffset["data"]];
        }
        else{
            dd($bookingsWithOffset);
        }
        $offset += 200;

        while($bool){
            $bookingsWithOffset = $this->getBookingsWithOffset("V1", $offset, 200);
            if($bookingsWithOffset["code"] == 3100){
                $bool = false;
            }
            elseif($bookingsWithOffset["code"] == 3000){
                $bookings = [...$bookings,...$bookingsWithOffset["data"]];
            }
            else{
                dd($bookingsWithOffset);
            }
            $offset += 200;
        }

        return $bookings;
    }

    public function getBookingsWithOffset($journalKey, $offset, $limit) {

        if($journalKey == "A1"){
            $type = "Journals_Report";
        }
        else{
            $type = "Vente_Report";
        }

        // URL
        $apiURL = $this->zoho->urlWsCreator.'/api/v2/'.$this->zoho->accountOwnerName.'/'.$this->zoho->appLinkName.'/report/'.$type.'?limit='.$limit.'&from='.$offset;

        // Headers
        $headers = [
            'Authorization' => 'Zoho-oauthtoken '.$this->zoho->accessToken
        ];

        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function hasBooking($bookings, $alphaNumericalNumber) {
        if($bookings == ""){
            return false;
        }
        foreach ($bookings as $key => $booking) {
            if($booking["N"] == $alphaNumericalNumber){
                return $bookings[$key];
            }
        }
        return false;
    }

    public function createRelation($relation) {

        // URL
        $apiURL = $this->zoho->urlWsCreator.'/api/v2/'.$this->zoho->accountOwnerName.'/'.$this->zoho->appLinkName.'/form/Tiers';

        // Headers
        $headers = [
            'Authorization' => 'Zoho-oauthtoken '.$this->zoho->accessToken
        ];

        // POST Data
        $postInput = [
            'data' => [
                'Nom' => $relation->name,
                'Adresse' => $relation->street,
                'Code_postal' => $relation->postalCode,
                'Localite' => $relation->city,
                'Pays' => $relation->country,
                'Num_ro_BCE' => $relation->TVANumber,
                'Secteur' => "Privé",
                'Niveau_public' => "Non applicable",
                'Compte_bancaire' => null,
                'Prenom' => null,
                'Nom1' => null,
                'Email' => $relation->email,
                'Phone_Number' => $relation->telephone,
                'GSM' => $relation->mobile,
            ]
        ];

        $response = Http::withHeaders($headers)->post($apiURL, $postInput);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function getRelations() {
        $offset = 0;
        $bool = true;
        $relations = [];

        $relationsWithOffset = $this->getRelationsWithOffset($offset, 199);
        if($relationsWithOffset["code"] == 3100){
            $bool = false;
        }
        elseif($relationsWithOffset["code"] == 3000){
            $relations = [...$relations,...$relationsWithOffset["data"]];
        }
        else{
            dd($relationsWithOffset);
        }
        $offset += 200;

        while($bool){
            $relationsWithOffset = $this->getRelationsWithOffset($offset, 200);
            if($relationsWithOffset["code"] == 3100){
                $bool = false;
            }
            elseif($relationsWithOffset["code"] == 3000){
                $relations = [...$relations,...$relationsWithOffset["data"]];
            }
            else{
                dd($relationsWithOffset);
            }
            $offset += 200;
        }

        return $relations;
    }

    public function getRelationsWithOffset($offset, $limit) {

        // URL
        $apiURL = $this->zoho->urlWsCreator.'/api/v2/'.$this->zoho->accountOwnerName.'/'.$this->zoho->appLinkName.'/report/Liste_des_tiers?limit='.$limit.'&from='.$offset;

        // Headers
        $headers = [
            'Authorization' => 'Zoho-oauthtoken '.$this->zoho->accessToken
        ];

        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function hasRelation($relations, $name) {
        if($relations == ""){
            return false;
        }
        foreach ($relations as $key => $relation) {
            if($relation["Nom"] == $name){
                return $relations[$key];
            }
        }
        return false;
    }
}
