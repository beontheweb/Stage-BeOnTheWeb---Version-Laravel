<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class DashBoardController extends Controller
{
    public function index() {
        $token = $this->getToken();
        $dossierToken = $this->getDossierToken($token);
        $buyBookings = $this->getBookings($dossierToken, true);
        $sellBookings = $this->getBookings($dossierToken, false);
 
        return View::make('welcome', 
            [
             "token" => $token, 
             "dossierToken" => $dossierToken, 
             "modifiedBuyBookings" => $buyBookings["modifiedBookings"],
             "deletedBuyBookings" => $buyBookings["deletedBookings"],
             "modifiedSellBookings" => $sellBookings["modifiedBookings"],
             "deletedSellBookings" => $sellBookings["deletedBookings"]
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

    public function getDossierToken($token, $dossierId = 45119) {
        // URL
        $apiURL = 'https://service.inaras.be/octopus-rest-api/v1/dossiers?dossierId='.$dossierId;
  
        // Headers
        $headers = [
            'Accept' => 'application/json',
            'token' => $token,
        ];
  
        $response = Http::withHeaders($headers)->post($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody["Dossiertoken"];
    }

    public function getBookings($dossierToken, $isBuy, $dossierId = 45119) {

        //Switch between Buy and Sell Bookings
        $journalTypeId = $isBuy ? 1 : 2;

        // URL
        $apiURL = 'https://service.inaras.be/octopus-rest-api/v1/dossiers/'.$dossierId.'/bookyears/1/bookings/modified?journalTypeId='.$journalTypeId.'&modifiedTimeStamp=2020-02-01%2014%3A55%3A00.000';
  
        // Headers
        $headers = [
            'dossierToken' => $dossierToken
        ];
  
        $response = Http::withHeaders($headers)->get($apiURL);
  
        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }
}
