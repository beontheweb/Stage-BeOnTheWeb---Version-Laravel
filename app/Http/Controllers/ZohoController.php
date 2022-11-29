<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ZohoController extends Controller
{
    public $zoho;

    /**
     * Récupère le token nécessaire pour les appels à l'api d'Octopus, valable 1h
     */
    public function getToken($code) {
        // URL
        $apiURL = $this->zoho->urlWsAuth.'/oauth/v2/token?grant_type=authorization_code&client_id='.$this->zoho->clientId.'&client_secret='.$this->zoho->clientSecret.'&redirect_uri='.$this->zoho->redirectUri.'&code='.$code;
  
        $response = Http::post($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    /**
     * Crée un booking dans Zoho Creator
     */
    public function createBooking($booking, $relationId) {

        if($booking->journalKey[0] == "A"){
            $type = "Achat";
        }
        elseif($booking->journalKey[0] == "V"){
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
                'Edition_FIFCL' => $this->zoho->edition,
                'N' => $booking->alphaNumericalNumber,
                'Num_facture' => $booking->reference,
                'PERIODE' => $booking->bookYearNumber,
                'DATE1' => date_format($booking->bookingDate, "Y-m-d"),
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

        //Ces deux lignes fix un bug touchant parfois les montants htva, tva et tvac
        ini_set('precision', 10);
        ini_set('serialize_precision', 10);

        $response = Http::withHeaders($headers)->post($apiURL, $postInput);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    /**
     * Met à jour un booking dans Zoho Creator selon une id donnée
     */
    public function updateBooking($bookingId, $booking, $relationId) {

        if($booking->journalKey[0] == "A"){
            $type = "Journals_Report";
        }
        elseif($booking->journalKey[0] == "V"){
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
                'Montant_HTVA' => $booking->HTVA,
                'TVA' => $booking->TVA,
                'Montant_TVAC' => $booking->amount,
                'DATE1' => date_format($booking->bookingDate, "Y-m-d")
            ]
        ];

        //Ces deux lignes fix un bug touchant parfois les montants htva, tva et tvac
        ini_set('precision', 10);
        ini_set('serialize_precision', 10);

        $response = Http::withHeaders($headers)->patch($apiURL, $postInput);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    /**
     * Récupère tout les bookings de Zoho Creator
     */
    public function getBookings() {
        $offset = 0;
        $bool = true;
        $bookings = [];

        //Le nombre de bookings pouvant être récupéré en un appel est limité à 200
        //Le code qui suit va rechercher les bookings 200 à la fois d'abord pour les achats puis pour les ventes 
        //et les rassembles tous dans l'array "bookings"
        

        //Achat

        //Appelé une première fois avec un offset de 199, nécessaire pour ne pas avoir un booking en double
        $bookingsWithOffset = $this->getBookingsWithOffset("A", $offset, 199);
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

        //Le reste des appels se fait avec un offset de 200
        while($bool){
            $bookingsWithOffset = $this->getBookingsWithOffset("A", $offset, 200);
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


        //Vente

        $offset = 0;
        $bool = true;

        //Appelé une première fois avec un offset de 199, nécessaire pour ne pas avoir un booking en double
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

        //Le reste des appels se fait avec un offset de 200
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

    /**
     * Récupère la liste des bookings soit d'achat, soit de vente et selon un offset donné. Ne renvoie pas plus de 200 bookings
     */
    public function getBookingsWithOffset($journalKey, $offset, $limit) {

        if($journalKey[0] == "A"){
            $type = "Journals_Report";
        }
        elseif($journalKey[0] == "V"){
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

    /**
     * Vérifie si le booking existe déjà dans l'array donnée
     */
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

    /**
     * Crée une relation dans Zoho Creator
     */
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

    /**
     * Récupère toute les relations de Zoho Creator
     */
    public function getRelations() {
        $offset = 0;
        $bool = true;
        $relations = [];

        //Le nombre de relations pouvant être récupérées en un appel est limité à 200
        //Le code qui suit va rechercher les relations 200 à la fois et les rassembles toutes dans l'array "relations"

        //Appelé une première fois avec un offset de 199, nécessaire pour ne pas avoir une relation en double
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

        //Le reste des appels se fait avec un offset de 200
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

    /**
     * Récupère la liste des relations selon un offset donné. Ne renvoie pas plus de 200 relations
     */
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

    /**
     * Vérifie si la relation existe déjà dans l'array donnée
     */
    public function hasRelation($relations, $name) {
        if($relations == ""){
            return false;
        }
        foreach ($relations as $key => $relation) {
            if(strcasecmp(trim($relation["Nom"]), trim($name)) == 0){
                return $relations[$key];
            }
        }
        return false;
    }
}
