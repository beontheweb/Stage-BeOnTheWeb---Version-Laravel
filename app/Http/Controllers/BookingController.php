<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Booking;
use App\Models\BookingLine;

class BookingController extends Controller
{
    public function index() {

        $bookings = Booking::with("relation")->get();

        $bookingLines = BookingLine::all();

        $bookings = $this->calculateTVA($bookings, $bookingLines);




        return View::make('bookings.index', 
            [
                "bookings" => $bookings,
                "bookingLines" => $bookingLines
            ]
        );
    }

    //Calcule la somme des montants HTVA et TVA des Booking Lines
    public function calculateTVA($bookings, $bookingLines){
        foreach ($bookings as $booking) {
            $booking->HTVA = 0;
            $booking->TVA = 0;
        }

        foreach ($bookingLines as $line) {
            foreach ($bookings as $booking) {
                if($line->booking_id == $booking->id){
                    $booking->HTVA += $line->baseAmount;
                    $booking->TVA += $line->vatAmount;
                }
            }
        }

        return $bookings;
    }
}
