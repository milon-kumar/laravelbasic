<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Callable_;
use function PHPUnit\Framework\callback;

class MyController extends Controller
{
    public function index(Request $request){
        return  $request->server();
    }

    public function getThisTestFunction(){
        $arrayGet[] =[
          'name' => "jugol",
          "age" => 20,
        ];
        $secObject = new SecondController();
        return SecondController::justForTestRefreance($arrayGet);
    }

    public function testRequest(Request $request){

    }





}
