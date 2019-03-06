<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Theodor\Mapping\Requests\Trello;

use Theodor\Mapping\Responses\IntegratedSchema;

class IntegrationController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->post();

        $integratedSchema = new IntegratedSchema();

        new Trello($data['trello'], $integratedSchema);

        return Response::json($integratedSchema);
    }
}
