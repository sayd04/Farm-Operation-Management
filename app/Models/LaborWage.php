<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class LaborWage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'labor_wages';

    protected $fillable = [
        'laborer_id', 'amount', 'payment_date', 'status'
    ];

    public function laborer()
    {
        return $this->belongsTo(Laborer::class);
    }
}
