<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DolibarrController extends Controller
{
    public $dolibarr;

    /**
     * Récupère les factures de ventes dans Dolibarr ayant une date de facturation > timestamp du formulaire
     */
    public function getBookings($timestamp){
        // URL
        $apiURL = $this->dolibarr->urlWS.'/invoices?sortfield=t.rowid&sortorder=ASC&limit=100&sqlfilters=(t.datef:>=:'.date("Y/m/d", strtotime($timestamp)).')';
  
        // Headers
        $headers = [
            'Accept' => 'application/json',
            'DOLAPIKEY' => $this->dolibarr->apiKey
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;

    }

    /**
     * Récupère la relation dans Dolibarr ayant l'id donnée
     */
    public function getRelationById($id){
        // URL
        $apiURL = $this->dolibarr->urlWS.'/thirdparties/'.$id;
  
        // Headers
        $headers = [
            'Accept' => 'application/json',
            'DOLAPIKEY' => $this->dolibarr->apiKey
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;

    }

    /**
     * Récupère le produit dans Dolibarr ayant la référence donnée
     */
    public function getProductByRef($ref){
        // URL
        $apiURL = $this->dolibarr->urlWS.'/products/ref/'.$ref;
  
        // Headers
        $headers = [
            'Accept' => 'application/json',
            'DOLAPIKEY' => $this->dolibarr->apiKey
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;

    }
}
