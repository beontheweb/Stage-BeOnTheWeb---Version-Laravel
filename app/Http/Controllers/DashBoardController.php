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
 
        return View::make('welcome', ["dossierToken" => $dossierToken]);
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
}
