<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingLine extends Model
{
    use HasFactory;

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public $timestamps = false;
    protected $fillable = ['alphaNumericalNumber', 'accountKey', 'baseAmount', 'vatAmount', 'vatCodeKey', 'vatPercentage', 'vatBasePercentage', 'comment', 'booking_id'];

}
