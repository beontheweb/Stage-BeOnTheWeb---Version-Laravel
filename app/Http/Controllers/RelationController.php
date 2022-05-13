<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Relation;
use App\Models\Booking;
use App\Models\BookingLine;

class RelationController extends Controller
{
    /**
     * Renvoie vers la vue index des relations
     */
    public function index() {

        $relations = Relation::all();

        $bookings = Booking::all();
        $bookingLines = BookingLine::all();
        $bookingController = new BookingController();
        $bookings = $bookingController->calculateTVA($bookings, $bookingLines);

        return View::make('relations.index', 
            [
                "relations" => $relations,
                "bookings" => $bookings
            ]
        );
    }
}
