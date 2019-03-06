<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Theodor\Mapping\Requests\Trello;
use Theodor\Mapping\Requests\Wrike;
use Theodor\Mapping\Requests\Asana;


use Theodor\Mapping\Responses\IntegratedSchema;

class IntegrationController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->post();

        $integratedSchema = new IntegratedSchema();

        try{
            new Trello($data['trello'], $integratedSchema);
        }catch (\Exception $e){
            $integratedSchema->errors[] = 'Could not connect to Trello';
        }

        try{
            new Wrike($data['wrike'], $integratedSchema);
        }catch (\Exception $e){
            $integratedSchema->errors[] = 'Could not connect to Wrike';
        }

        try{
            new Asana($data['asana'], $integratedSchema);
        }catch (\Exception $e){
            $integratedSchema->errors[] = 'Could not connect to Asana';
        }

        if(empty($integratedSchema->errors)){
            unset($integratedSchema->errors);
        }

        return Response::json($integratedSchema);
    }
}
