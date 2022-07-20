<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'service_name',
        'price',
        'status',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
