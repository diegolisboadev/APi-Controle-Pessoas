<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Eloquent\Model;

# Importante para as usar as funções do Eloquent preparadas para o MongoDB
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use MongoDB\Operation\FindOneAndUpdate;

class ControlePessoas extends Eloquent
{
    use HasFactory;

    protected $connection = 'mongodb';

    //
    protected $collection = 'controle_pessoas';
    protected $primaryKey = "id";
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nome', 'sexo', 'peso', 'altura', 'imc'];

    /**
     * The Cast Datas
     */
    protected $dates = ['created_at', 'updated_at'];
}
