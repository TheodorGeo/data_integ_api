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
    private $integration;

    function __construct($integration = null)
    {
        $this->integration = $integration;
        $this->token = isset($integration['token']) ? $integration['token'] : request('token');
        $this->client = new Client(['base_uri' => 'https://api.trello.com/1/']);
        $this->app_key = env('TRELLO_APP_KEY', '0043dc605ec22c8b475f7e945cc1f067');

    }


    public function index()
    {
      $data = $this->getAllBoards();

      if (!$this->infos['errorBoolean']) {
        $boards = json_decode($data);
        return $boards;
      }else{
        return $this->infos;
      }

    }


    public function shortLink($shortLink)
    {
      $board =  $this->getBoardByShortLink($shortLink);
      if ($this->infos['errorBoolean']) {
        return $this->infos;
      }else {
        return $board;
      }

    }


    public function boardByShortLink($shortLink)
    {
      $board = $this->getBoardByShortLink($shortLink);
      $fields = isset($this->integration['fields']) ? $this->integration['fields'] :$this->fields();
      if ($this->infos['errorBoolean']) {
        return $this->infos;
      }
      $id = $board['id'];
      $body['board'] = $board;
      foreach ($fields as $field) {
        $data = $this->getBoardField($id, $field);
        if ($this->infos['errorBoolean']) {
          return $this->infos;
        }else {
          $body[$field] = json_decode($data);
        }
      }

      return $body;

    }


    public function boardById($id)
    {
      $board = $this->getBoardById($id);
      $fields = $this->fields();
      if ($this->infos['errorBoolean']) {
        return $this->infos;
      }
      $id = $board['id'];
      $body['board'] = $board;
      foreach ($fields as $field) {
        $data = $this->getBoardField($id, $field);
        if ($this->infos['errorBoolean']) {
          return $this->infos;
        }else {
          $body[$field] = json_decode($data);
        }
      }

      return $body;
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
        $this->infos['messages'][] = "Could not connect to the Trello API's server";
        $this->infos['errorBoolean'] = true ;
      } catch(ClientException $e){
        $this->infos['status'] = $e->getResponse()->getStatusCode();
        $this->infos['messages'][] = $e->getResponse()->getReasonPhrase();
        $this->infos['errorBoolean'] = true ;
      }

    }




    //--------------------------------------------------------------------------
    //Use this method to get all the boards via user's token
    //Not required parameters
    //--------------------------------------------------------------------------
    private function getAllBoards()
    {
      $url = "members/me/boards";
      return $this->request($url,['fields' => 'name,shortLink']);
    }



    //--------------------------------------------------------------------------
    //Use this method to get all the boards via user's token
    //Not required parameters
    //--------------------------------------------------------------------------
    private function getBoardField($id, $field)
    {
      $url = 'boards/'.$id.'/'.$field;
      return $this->request($url);
    }

    //--------------------------------------------------------------------------
    //Use this method to get a board's infos via its shortLink
    //Required param: $shortLink
    //--------------------------------------------------------------------------
    private function getBoardByShortLink($shortLink)
    {
      $data = $this->getAllBoards();

      if ($this->infos['errorBoolean']) {
        return $this->infos;
      }
      $boards = json_decode($data);
      $fBoard = null;
      foreach ($boards as $board) {
        if ($board->shortLink === $shortLink) {
          $fBoard['name'] = $board->name;
          $fBoard['id'] = $board->id;
          $fBoard['shortLink'] = $board->shortLink;
          break;
        }
      }

      if ($fBoard !== null) {
        return $fBoard;
      }else {
        $this->infos['messages'][] = 'Please enter the correct shortLink of the board';
        $this->infos['errorBoolean'] = true ;
      }


    }


    //--------------------------------------------------------------------------
    //Use this method to get a board's infos via its id
    //Required param: $id
    //--------------------------------------------------------------------------
    private function getBoardById($id)
    {
      $data = $this->getAllBoards();

      if ($this->infos['errorBoolean']) {
        return $this->infos;
      }
      $boards = json_decode($data);
      $fBoard = null;
      foreach ($boards as $board) {
        if ($board->id === $id) {
          $fBoard['name'] = $board->name;
          $fBoard['id'] = $board->id;
          $fBoard['shortLink'] = $board->shortLink;
          break;
        }
      }

      if ($fBoard !== null) {
        return $fBoard;
      }else {
        $this->infos['messages'][] = 'Please enter the correct shortLink of the board';
        $this->infos['errorBoolean'] = true ;
      }


    }




    //--------------------------------------------------------------------------
    //Use this method to et the required fields of the board
    //possible params fields=cards,lists,checklists,members
    //--------------------------------------------------------------------------

    private function fields()
    {
      $fields = [];
      $fieldBoolean = false;
      if (request('fields') !== null) {
        $query = explode(',', request('fields'));
        foreach ($query as $f) {
          if (($f === 'cards') || ($f === 'lists') || ($f === 'checklists') || ($f === 'members') ) {
            $fields[] = $f;
            $fieldBoolean = true;
          }
        }
        if ($fieldBoolean) {
          return $fields;
        }else {
          $this->infos['messages'][] = 'Please enter the correct fields options';
          $this->infos['errorBoolean'] = true ;
        }
      }else {
        $this->infos['messages'][] = 'Please enter the correct fields options';
        $this->infos['errorBoolean'] = true ;
      }
    }

}
