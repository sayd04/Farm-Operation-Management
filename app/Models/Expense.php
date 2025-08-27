<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Expense extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'expenses';

    protected $fillable = [
        'user_id', 'description', 'amount', 'date'
    ];
}
