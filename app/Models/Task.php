<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Task extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tasks';

    protected $fillable = [
        'field_id', 'task_name', 'description', 'status', 'due_date'
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
