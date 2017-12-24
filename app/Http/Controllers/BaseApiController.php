<?php

namespace App\Http\Controllers;

use App\Http\Models\User;
use App\Http\Models\MenuSystem;
use App\Http\Models\UserSetting;
use App\Http\Models\CarrierSetting;
use App\Http\Models\UserCarrierSetting;
use App\Http\Models\RoleMenu;

use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\FunctionLib;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
class BaseApiController extends Controller{

	public function __construct(){}

    public function returnResultSuccess($dataOutPut, $message = 'Success'){
        return Response::json(
            array(
                'intIsOK'=> 1,
                'data' => $dataOutPut,
                'message' => $message,
                'code'=>  200
            )
        );
    }

    public function returnResultError($dataOutPut, $message = 'Error Exception'){
        $dataLog['data'] = $dataOutPut;
        $dataLog['message'] = $message;
        return Response::json(
            array(
                'intIsOK'=> -1,
                'data' => $dataOutPut,
                'message' => $message,
                'code'=>  202
            )
        );
    }
}  