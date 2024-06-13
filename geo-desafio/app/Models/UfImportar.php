<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UfImportar extends Model
{
    use HasFactory;

    protected $table = 'ufs';
    protected $fillable = ['uf'];
}
