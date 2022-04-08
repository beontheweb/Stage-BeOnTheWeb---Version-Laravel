<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public function booking_lines()
    {
        return $this->hasMany(BookingLine::class);
    }

    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }

    protected $fillable = ['alphaNumericalNumber', 'documentNumber', 'amount', 'bookYearId', 'bookYearNumber', 'comment', 'currency', 'bookingDate', 'expiryDate', 'echangeRate', 'journalKey', 'paymentMethod', 'reference', 'relation_id'];

    protected $casts = [
        'bookingDate' => 'datetime',
    ];

}
