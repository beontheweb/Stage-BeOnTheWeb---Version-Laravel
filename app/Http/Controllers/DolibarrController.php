<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DolibarrController extends Controller
{
    public $dolibarr;

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
