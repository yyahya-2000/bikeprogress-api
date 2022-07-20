<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_name',
        'firstname',
        'lastname',
        'patronymic',
        'phone_number',
        'extra_phone_number',
        'email',
        'extra_email',
        'loyalty',
        'note',
    ];

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
