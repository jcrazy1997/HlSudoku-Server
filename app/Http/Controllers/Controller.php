<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;



    public function responseSuccess($message='success',$statusCode=200,array $data=null){
        return response()->json([
            'message'		=>$message,
            'statusCode'	=>$statusCode,
            'data'			=>$data
        ]);
    }

    public function responseError($message='error',$statusCode=500,$httpCode=500){
        return response()->json([
            'message'		=>$message,
            'statusCode'	=>$statusCode,
        ],$httpCode);
    }

    public function responseFailed($message='failed',$statusCode=201){
        return response()->json([
            'message'       =>$message,
            'statusCode'    =>$statusCode,
        ]);
    }
}
