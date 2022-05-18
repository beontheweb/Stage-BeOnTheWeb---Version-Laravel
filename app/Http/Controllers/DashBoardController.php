<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\GeneralParam;
use App\Models\Octopus;
use App\Models\Zoho;
use App\Models\Dolibarr;

class DashBoardController extends Controller
{

    public $octopusController;
    public $databaseController;
    public $zohoController;
    public $dolibarrController;

    /**
     * Appelé en premier pour la route /home, récupère le code renvoyé pour Auth Zoho en requête 
     *
     * @param Request $request
     * @return void
     */
    public function indexSetup(Request $request) {

        if($request->code){
            //Si jamais l'addresse du site est changée, la partie redirect_uri de l'href du bouton Auth Zoho de la vue dashboard.index doit être changée aussi

            //Récupère l'access token de Zoho
            $this->zohoController = new ZohoController();
            $this->zohoController->zoho = Zoho::get()->first();
            $tokens = $this->zohoController->getToken($request->code);
            $this->zohoController->zoho->accessToken = $tokens["access_token"];
            $this->zohoController->zoho->save();

            //Enregistre le timestamp qui sera utilisé pour vérifier la validité du token
            $generalParams = GeneralParam::get()->first();
            $generalParams->lastZohoAuth = now();
            $generalParams->save();
        }

        return $this->index([]);
    }

    /**
     * Renvoie vers la vue index du dashboard et accepte un tableau associatif pour envoyer des données à la vue
     */
    public function index($addVariables) {

        $this->octopusController = new OctopusController();
        $this->octopusController->octopus = Octopus::where("action", "receive")->get()->first();
        $journalKeys = $this->octopusController->getJournalKeys();

        $generalParams = GeneralParam::get()->first();
        $lastZohoAuth = time() - strtotime($generalParams->lastZohoAuth) < 3601;

        //Données devant toujours être envoyées à la vue dashboard
        $array = [
            "modifiedBookings" => null,
            "lastUpdated" => $generalParams->lastUpdated,
            "lastZohoAuth" => $lastZohoAuth,
            "journals" => $journalKeys
        ];

        //Ajoute les données supplémentaires au tableau de données générales
        $array = array_merge($array, $addVariables);

        return View::make('dashboard.index', $array);
    }

    /**
     * Réinitialise les tableaux bookings, bookingLines et relations de la base donnée
     */
    public function resetDatabase() {
        $this->databaseController = new DatabaseController();
        $this->databaseController->resetDatabase();

        $addVariables = ["reset" => "Réinitialisation exécutée"];

        return $this->index($addVariables);
    }

    /**
     * Récupère les données de l'octopus associé dans les paramètres (où "action" = "receive") et les place dans la base de donneé du hub
     */
    public function updateDB(Request $request) {
        //Log montrant les bookings récupérés
        $completeBookings = [];

        //Récupère les paramètres du formulaire
        $journalKeys = $request->get("journals");
        $timestamp = $request->timestamp . " 00:00:00.000";

        $this->octopusController = new OctopusController();
        $this->octopusController->octopus = Octopus::where("action", "receive")->get()->first();
        $this->octopusController->token = $this->octopusController->getToken();
        $this->octopusController->dossierToken = $this->octopusController->getDossierToken();

        //Récupération des params généraux pour pouvoir mettre à jour lastUpdated
        $this->databaseController = new DatabaseController();
        $params = GeneralParam::get()->first();

        //Pour chaque clé de journal, appelle l'api d'octopus pour récupérer les bookings correspondant et mets à jour la base de donnée
        if ($journalKeys) {
            foreach ($journalKeys as $journalKey) {
                //Retire les espaces
                $journalKey = trim($journalKey);
                
                $bookings = $this->octopusController->getBookings($journalKey, $timestamp);
                if(!isset($bookings["technicalInfo"])){
                    $this->databaseController->fillDB($bookings, $this->octopusController);
                }
                //Rajoute le booking au log
                array_push($completeBookings, $bookings);
            }

            //Met à jour la date utilisée pour le timestamp
            $params->lastUpdated = date('Y-m-d H:i:s');
            $params->save();
        }

        $addVariables = ["modifiedBookings" => $completeBookings];

        return $this->index($addVariables);
    }
    
    /**
     * Transfert les données du hub vers le Zoho Creator associé et les supprime du hub
     */
    public function sendDataZoho() {

        $this->zohoController = new ZohoController();
        $this->zohoController->zoho = Zoho::get()->first();

        //Pour chaques relations de la base de donnée, vérifie si elle existe déjà dans Zoho et sinon la crée
        $relations = \App\Models\Relation::all();
        $zohoRelations = $this->zohoController->getRelations();
        foreach ($relations as $relation) {
            $hasRelation = $this->zohoController->hasRelation($zohoRelations, trim($relation->name));
            if(!$hasRelation){
                $this->zohoController->createRelation($relation)["data"]["ID"];
            }
        }

        $bookingController = new BookingController();
        //Récupère la liste des bookings du hub
        $bookings = \App\Models\Booking::with("relation")->get();
        $bookingLines = \App\Models\BookingLine::all();
        $bookings = $bookingController->calculateTVA($bookings, $bookingLines);
        //Log contenant la liste des bookings créés ou mis à jour dans Zoho
        $zohoBookingLog = [];
        //Récupère les bookings déjà présents dans Zoho 
        $zohoBookings = $this->zohoController->getBookings();
        $zohoRelations = $this->zohoController->getRelations();
        //Pour chaque bookings du hub, crée ou met à jour le booking dans Zoho
        foreach ($bookings as $booking) {
            $hasRelation = $this->zohoController->hasRelation($zohoRelations, trim($booking->relation->name));
            $relationId = $hasRelation['ID'];

            $hasBooking = $this->zohoController->hasBooking($zohoBookings, $booking->alphaNumericalNumber);
            if(!$hasBooking){
                $result = $this->zohoController->createBooking($booking, $relationId);
                if(isset($result["error"])){
                    $zohoBookingLog[$booking->alphaNumericalNumber] = $result["error"];
                }
                else{
                    $zohoBookingLog[$booking->alphaNumericalNumber] = "Ajouté";
                }
                
            }
            else{
                $result = $this->zohoController->updateBooking($hasBooking['ID'], $booking, $relationId);
                if(isset($result["error"])){
                    $zohoBookingLog[$booking->alphaNumericalNumber] = $result["error"];
                }
                else{
                    $zohoBookingLog[$booking->alphaNumericalNumber] = "Mise à jour";
                }
            }

            //Supprime le booking et ses bookingLines
            foreach ($bookingLines as $line) {
                if($line->booking_id == $booking->id){
                    $line->delete();
                }
            }
            $booking->delete();
        }

        $addVariables = ["zohoBookingLog" => $zohoBookingLog];

        return $this->index($addVariables);
    }

    /**
     * Récupère les factures de ventes du Dolibarr associé et les transfert vers l'Octopus associé (où "action" = "send")
     */
    public function transferDoliOcto(Request $request) {

        $this->octopusController = new OctopusController();
        $this->octopusController->octopus = Octopus::where("action", "send")->get()->first();
        $this->octopusController->token = $this->octopusController->getToken();
        $this->octopusController->dossierToken = $this->octopusController->getDossierToken();
        $validBookings = [];
        $bookings = [];
        //Log contenant la liste des factures de vente créées dans Octopus
        $octoBookingLog = [];

        $this->dolibarrController = new DolibarrController();
        $this->dolibarrController->dolibarr = Dolibarr::get()->first();
        //Récupère les factures de ventes de Dolibarr
        $dolibarrBookings = $this->dolibarrController->getBookings($request->timestamp);

        if(isset($dolibarrBookings["error"])) {
            //Renvoie l'erreur vers la vue si il y en a eu une lors de la récupération des factures
            $octoBookingLog["Code"] = $dolibarrBookings["error"]["code"];
            $octoBookingLog["Message"] = $dolibarrBookings["error"]["message"];
        }
        else {
            //Boucle sur les factures pour retirer celle ayant un statut "brouillon"
            foreach ($dolibarrBookings as $dolibarrBooking) {
                if($dolibarrBooking["brouillon"] == null){
                    array_push($validBookings, $dolibarrBooking);
                }
            }
            //Récupère les factures de ventes dans octopus
            $octopusBookings = $this->octopusController->getBookings("V1", "1980-01-01 00:00:00.000");
            //Ne garde que les factures de dolibarr n'existant pas déjà dans octopus
            foreach ($validBookings as $key => $validBooking) {
                $bool = true;
                foreach ($octopusBookings as $key => $octopusBooking) {
                    if(isset($octopusBooking["reference"])){
                        if($octopusBooking["reference"] == $validBooking["ref"]){
                            $bool = false;
                        }
                    }
                }
                if($bool){
                    array_push($bookings, $validBooking);
                }   
            }
            //Trie les factures par date de facture croissante puis par référence croissante
            if(count($bookings) > 0){
                $sort = array();
                foreach ($bookings as $key => $booking)
                {
                    $sort["ref"][$key] = substr($booking['ref'], -9);
                    $sort["date"][$key] = $booking['date'];
                }
                array_multisort($sort["date"], SORT_ASC, $sort["ref"], SORT_ASC, $bookings);
            }
            
            //Pour chaque factures, crée la relation dans octopus si elle n'existe pas déjà puis crée la facture
            foreach ($bookings as $booking) {
                $relation = $this->dolibarrController->getRelationById($booking["socid"]);
                if(isset($this->octopusController->getRelationByName($relation["name"])["errorCode"])){
                    $this->octopusController->createRelation($relation);
                }
                $relationId = $this->octopusController->getRelationByName($relation["name"])[0]["relationIdentificationServiceData"]["relationKey"]["id"];
                $externalRealtionId = $this->octopusController->getRelationByName($relation["name"])[0]["relationIdentificationServiceData"]["externalRelationId"];
    
                $result = $this->octopusController->createBooking($booking, $relationId, $externalRealtionId);
                if(isset($result["errorCode"])) {
                    $octoBookingLog[$booking["ref"]] = "Erreur ".$result["errorCode"]." : ".$result["technicalInfo"];
                }
                elseif(isset($result["technicalInfo"])){
                    $octoBookingLog[$booking["ref"]] = $result["technicalInfo"];
                }
                else {
                    $octoBookingLog[$booking["ref"]] = "Ajouté";
                }
            }
        }

        $addVariables = ["octoBookingLog" => $octoBookingLog];

        return $this->index($addVariables);
    }
}
