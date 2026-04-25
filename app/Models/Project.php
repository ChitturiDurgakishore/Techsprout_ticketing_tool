<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
