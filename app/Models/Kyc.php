<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{
    use HasFactory;

    protected $table = 'kycs';

    protected $fillable = [
        'user_id',
        'id_card_front',
        'id_card_back',
        'photo',
        'proof_of_address',
        'tax_id_number',
        'ssn',
        'dob',
        'business_license',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'tax_id_number',
        'ssn',
        'dob',
        'business_license',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dob' => 'date',
    ];
}
