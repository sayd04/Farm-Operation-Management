<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Laborer extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'laborers';

    protected $fillable = [
        'user_id', 'name', 'contact_info', 'position'
    ];

    public function wages()
    {
        return $this->hasMany(LaborWage::class, 'laborer_id');
    }
}
