<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ConnectException;
use \GuzzleHttp\Exception\ClientException;

class TrelloController extends Controller
{
    private $client ;
    private $token ;
    private $infos = [
      'errorBoolean' => false
    ];
    public $app_key = '0043dc605ec22c8b475f7e945cc1f067';

    function __construct()
    {
      $this->token = request('token');
      $this->client = new Client(['base_uri' => 'https://api.trello.com/1/']);

    }

    public function index()
    {
      $url = "members/me/boards";
      $data = $this->request($url,['fields' => 'name,shortLink']);

      if (!$this->infos['errorBoolean']) {
        $boards = json_decode($data);
        return $boards;
      }else{
        return $this->infos;
      }

    }

    public function shortLink($shortLink)
    {
      $url = "members/me/boards";
      $boards = $this->request($url,['fields' => 'name,shortLink']);
      dd($boards);


    }



    //--------------------------------------------------------------------------
    //Use this method to send safetely request to an external server and handle
    //Any callaback error (400,401 500 etc)
    //Return data only if there are not any errors
    //It add any found error to the static array $infos
    //--------------------------------------------------------------------------
    private function request($url, $data = null)
    {

      $query = [
        'query'=>[
          'key' => $this->app_key,
          'token' => $this->token,
        ]
      ];

      if($data !== null){
        $query['query'][key($data)] = $data[key($data)];
      }

      try {
        $res = $this->client->request('GET', $url, $query);
        return $res->getBody();
      } catch (ConnectException $e) {
        $this->infos['error'] = "Could not connect to the Trello API's server";
        $this->infos['errorBoolean'] = true ;
      } catch(ClientException $e){
        $this->infos['status'] = $e->getResponse()->getStatusCode();
        $this->infos['message'] = $e->getResponse()->getReasonPhrase();
        $this->infos['errorBoolean'] = true ;
      }

    }


}
