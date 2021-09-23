<?php

namespace App\Http\Controllers;

use App\Models\User;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //
    public function usuariosQuant() {
        return response()->json(['status' => 200, 'retorno' => User::count()]);
    }
}
