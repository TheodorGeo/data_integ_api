<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Theodor\Repositories\Trello;

class IntegrationController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->post();
        //dd($data['trello']);
        var_dump(Trello::get($data['trello'])->handle()->boardByShortLink($data['trello']['shortLink']));
    }
}
