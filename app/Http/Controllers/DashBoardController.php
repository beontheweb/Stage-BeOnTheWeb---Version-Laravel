<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\GeneralParam;
use App\Models\Octopus;
use App\Models\Zoho;
use App\Models\Relation;

class DashBoardController extends Controller
{

    public $octopusController;
    public $databaseController;
    public $zohoController;

    public function index(Request $request) {

        if($request->code){
            $this->zohoController = new ZohoController();
            $this->zohoController->zoho = Zoho::get()->first();
            $tokens = $this->zohoController->getToken($request->code);
            //$this->zohoController->zoho->refreshToken = $tokens->refresh_token;
            $this->zohoController->zoho->accessToken = $tokens["access_token"];
            $this->zohoController->zoho->save();
        }

        $this->octopusController = new OctopusController();
        $this->octopusController->octopus = Octopus::get()->first();

        $journalKeys = $this->octopusController->getJournalKeys();

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

        //Récupère les paramètres du formulaire
        $journalKeys = $request->get("journals");
        $timestamp = $request->timestamp . " 00:00:00.000";

        $this->octopusController = new OctopusController();
        $this->octopusController->octopus = Octopus::get()->first();
        $this->octopusController->token = $this->octopusController->getToken();
        $this->octopusController->dossierToken = $this->octopusController->getDossierToken();

        $this->databaseController = new DatabaseController();
        $params = GeneralParam::get()->first();

        //Pour chaque clé de journal, appelle l'api octopus pour récupérer les bookings correspondant et mets à jour la base de donnée
        if ($journalKeys) {
            foreach ($journalKeys as $journalKey) {
                //Retire les espaces
                $journalKey = trim($journalKey);
                
                $bookings = $this->octopusController->getBookings($journalKey, $timestamp);
                if(!isset($bookings["technicalInfo"])){
                    $this->databaseController->fillDB($bookings, $this->octopusController);
                }
                //Juste pour le débuggage
                array_push($completeBookings, $bookings);
            }

            $params->lastUpdated = date('Y-m-d H:i:s');
            $params->save();
        }

        $journalKeys = $this->octopusController->getJournalKeys();

        return View::make('dashboard.index', 
            [
             "modifiedBookings" => $completeBookings,
             "lastUpdated" => $params->lastUpdated,
             "journals" => $journalKeys
            ]
        );
    }

    public function refreshZohoToken() {

        $this->octopusController = new OctopusController();
        $this->octopusController->octopus = Octopus::get()->first();
        $journalKeys = $this->octopusController->getJournalKeys();

        $this->zohoController = new ZohoController();
        $this->zohoController->zoho = Zoho::get()->first();
        $this->zohoController->zoho->accessToken = $this->zohoController->refreshToken();


        return View::make('dashboard.index', 
            [
             "zohoToken" => $this->zohoController->zoho->accessToken,
             "modifiedBookings" => null,
             "lastUpdated" => GeneralParam::get()->first()->lastUpdated,
             "journals" => $journalKeys
            ]
        );
    }
    
    public function sendDataZoho() {

        $this->octopusController = new OctopusController();
        $this->octopusController->octopus = Octopus::get()->first();
        $journalKeys = $this->octopusController->getJournalKeys();

        $this->zohoController = new ZohoController();
        $this->zohoController->zoho = Zoho::get()->first();
        $bookingController = new BookingController();
        $bookings = \App\Models\Booking::with("relation")->get();
        $bookingLines = \App\Models\BookingLine::all();
        $bookings = $bookingController->calculateTVA($bookings, $bookingLines);
        foreach ($bookings as $booking) {
            $hasRelation = $this->zohoController->getRelationByName($booking->relation->name);
            if($hasRelation["code"] == 3100){
                $relationId = $this->zohoController->createRelation(Relation::where('name', $booking->relation->name)->first())["data"]["ID"];
            }
            else{
                $relationId = $hasRelation['data'][0]['ID'];
            }
            $hasBooking = $this->zohoController->getBookingByNumber($booking);
            if($hasBooking["code"] == 3100){
                $this->zohoController->createBooking($booking, $relationId);
            }
            else{
                $this->zohoController->updateBooking($hasBooking['data'][0]['ID'], $booking, $relationId);
            }
            foreach ($bookingLines as $line) {
                if($line->booking_id == $booking->id){
                    $line->delete();
                }
            }
            $booking->delete();
        }


        return View::make('dashboard.index', 
            [
             "modifiedBookings" => null,
             "lastUpdated" => GeneralParam::get()->first()->lastUpdated,
             "journals" => $journalKeys
            ]
        );
    }
}
