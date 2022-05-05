<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DolibarrController extends Controller
{
    public function getBookings(){
        // URL
        $apiURL = 'https://dorian-dolibarr.beontheweb.be/api/index.php/invoices?sortfield=t.rowid&sortorder=ASC&limit=100';
  
        // Headers
        $headers = [
            'Accept' => 'application/json',
            'DOLAPIKEY' => '848vf87PgPK7kawI812TTKjfZNgdzEB8'
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;

    }

    public function getRelationById($id){
        // URL
        $apiURL = 'https://dorian-dolibarr.beontheweb.be/api/index.php/thirdparties/'.$id;
  
        // Headers
        $headers = [
            'Accept' => 'application/json',
            'DOLAPIKEY' => '848vf87PgPK7kawI812TTKjfZNgdzEB8'
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;

    }

    public function getProductByRef($ref){
        // URL
        $apiURL = 'https://dorian-dolibarr.beontheweb.be/api/index.php/products/ref/'.$ref;
  
        // Headers
        $headers = [
            'Accept' => 'application/json',
            'DOLAPIKEY' => '848vf87PgPK7kawI812TTKjfZNgdzEB8'
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;

    }
}
