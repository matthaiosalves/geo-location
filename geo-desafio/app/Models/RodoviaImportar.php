<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RodoviaImportar extends Model
{
    use HasFactory;

    protected $table = 'rodovias';
    protected $fillable = ['uf_id', 'rodovia'];
}
