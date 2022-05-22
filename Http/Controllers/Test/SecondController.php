<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SecondController extends Controller
{
    public static function justForTestRefreance($arrayGet){
        return $arrayGet;
    }
}
