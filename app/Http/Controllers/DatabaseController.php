<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\BookingLine;
use App\Models\Relation;


class DatabaseController extends Controller
{
    public $octopusController;

    //Vide les tables de donnée.
    public function resetDatabase(){
        DB::table('bookings')->truncate();
        DB::table('booking_lines')->truncate();
        DB::statement("SET foreign_key_checks = 0");
        DB::table('relations')->truncate();
        DB::statement("SET foreign_key_checks = 1");
    }

    public function fillDB($array, $octopusController){

        foreach ($array as $value) {
            $this->octopusController = $octopusController;
            $relationName = $this->octopusController->getRelation($value["relationIdentificationServiceData"]["relationKey"]["id"])[0]["name"];
            $this->createBooking($value, $relationName);
        }
    }

    //Crée ou update un booking si alphaNumericalNumber existe déjà
    public function createBooking($value, $relationName){

            $alphaNumericalNumber = substr($value["bookyearPeriodeNr"], 0, 4)."-".$value["journalKey"]."-".sprintf("%03d", $value["documentSequenceNr"]);

            if (Relation::where('name', $relationName)->exists()) {
                $relationId = Relation::where('name', $relationName)->pluck('id')[0];
             }
             else{
                $relationId = $this->createRelation($value["relationIdentificationServiceData"]["relationKey"]["id"]);
             }

            $booking = Booking::updateOrCreate(
                ['alphaNumericalNumber' => $alphaNumericalNumber],
                [
                    'documentNumber' => $value["documentSequenceNr"],
                    'amount' => $value["amount"],
                    'bookYearId' => $value["bookyearKey"]["id"],
                    'bookYearNumber' => $value["bookyearPeriodeNr"],
                    'comment' => $value["comment"] ?? "",
                    'currency' => $value["currencyCode"],
                    'bookingDate' => $value["documentDate"],
                    'expiryDate' => $value["expiryDate"],
                    'echangeRate' => $value["exchangeRate"],
                    'journalKey' => $value["journalKey"],
                    'paymentMethod' => $value["paymentMethod"],
                    'reference' => $value["reference"],
                    'relation_id' => $relationId
                ]
            );

            foreach ($value["bookingLines"] as $key => $line) {
                $this->createBookingLine($line, $booking->id, $key+1, $alphaNumericalNumber);
            }

            return $booking->id;
    }

    //Crée ou update une booking line si alphaNumericalNumber existe déjà
    public function createBookingLine($value, $id, $lineID, $alphaNumericalNumber){

        $alphaNumericalNumber = $alphaNumericalNumber."-".sprintf("%02d", $lineID);

        BookingLine::updateOrCreate(
            ['alphaNumericalNumber' => $alphaNumericalNumber],
            [
                'accountKey' => $value["accountKey"],
                'baseAmount' => $value["baseAmount"],
                'vatAmount' => $value["vatAmount"],
                'vatCodeKey' => $value["vatCodeKey"],
                'vatPercentage' => array_key_exists("vatRecupPercentage", $value) ? $value["vatRecupPercentage"] : 100,
                'vatBasePercentage' => $this->octopusController->getVatBasePercentage($value["vatCodeKey"]),
                'comment' => $value["comment"],
                'booking_id' => $id,
            ]
        );

    }

    //Crée une nouvelle relation
    public function createRelation($id){

            $value = $this->octopusController->getRelation($id);
            //Renvoie un tableau dans un tableau, il faut sélectionner le premier élément
            $value = $value[0];

            $relation = new Relation();
 
            $relation->externalID = $id;
            $relation->name = $value["name"];
            $relation->firstName = $value["firstName"] ?? "";
            $relation->contactPerson = $value["contactPerson"] ?? "";
            $relation->telephone = $value["telephone"] ?? "";
            $relation->mobile = $value["mobile"] ?? "";
            $relation->fax = $value["fax"] ?? "";
            $relation->email = $value["email"] ?? "";
            $relation->active = $value["active"];
            $relation->bankAccount = $value["bankAccountNr"] ?? "";
            $relation->bicCode = $value["bicCode"] ?? "";
            $relation->TVAType = $value["vatType"] ?? "";
            $relation->TVANumber = $value["vatNr"];
            $relation->ibanAccount = $value["ibanAccountNr"] ?? "";
            $relation->currency = $value["currencyCode"];
            $relation->corporationType = $value["corporationType"];
            $relation->financialDiscount = $value["financialDiscount"];
            $relation->defaultSupplier = 0;
            $relation->defaultClient = 0;
            $relation->hasClient = $value["client"];
            $relation->hasSupplier = $value["supplier"];
            $relation->expirationDays = $value["expirationDays"];
            $relation->expirationType = $value["expirationType"];
            $relation->country = $value["country"] ?? "";
            $relation->city = $value["city"] ?? "";
            $relation->postalCode = $value["postalCode"] ?? "";
            $relation->street = $value["streetAndNr"] ?? "";
            $relation->deliveryCountry = $value["deliveryCountry"] ?? "";
            $relation->deliveryCity = $value["deliveryCity"] ?? "";
            $relation->deliveryPostalCode = $value["deliveryPostalCode"] ?? "";
            $relation->deliveryStreet = $value["deliveryStreetAndNr"] ?? "";
            $relation->remarks = $value["remarks"] ?? "";
            $relation->sddActive = $value["sddActive"];
            $relation->sddMandateType = $value["sddMandateType"];
            $relation->sddSeqtype = $value["sddSeqtype"];
            $relation->searchField1 = $value["searchField1"] ?? "";
            $relation->searchField2 = $value["searchField2"] ?? "";
 
            $relation->save();
            return $relation->id;
    }
}
