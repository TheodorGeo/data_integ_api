<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ConnectException;
use \GuzzleHttp\Exception\ClientException;

class WrikeController extends Controller
{
    private $infos = [
      'errorBoolean' => false
    ];
    private $name;
    private $descendants;
    private $users;

    function __construct()
    {
      $this->token = request('token');
      $this->name = request('name');
      $this->descendants = request('descendants');
      $this->users = request('users');
      $this->client = new Client([
        'base_uri' => 'https://www.wrike.com/api/v3/',
        'headers' => [
            'Authorization' => 'Bearer '.$this->token
        ]
      ]);
    }

    public function index()
    {
      $folders = $this->request('folders');
      if ($this->infos['errorBoolean']) {
        return $this->infos;
      }
      return $folders;
    }


    public function showById($id)
    {
      return $this->getFolder($id);
    }


    public function showByName()
    {
      $ids = $this->getIdByName();
      $temp = [];
      if (!$this->infos['errorBoolean']) {
        foreach ($ids as $id) {
          $temp[] = $this->getFolder($id);
        }
      }else {
        return $this->infos;
      }
      if ($this->infos['errorBoolean']) {
        return $this->infos;
      }else {
        return $temp;
      }
    }


    public function users($id)
    {
      $this->users = 'true';
      $data = $this->getFolder($id);
      if (!$this->infos['errorBoolean']) {
        $users = $data['users'];
        $temp = ['folderId' => $id, 'users' => $users];
        return $temp;
      }else {
        return $this->infos;
      }
    }


    //--------------------------------------------------------------------------
    //Use this method to get all the tasks from a folder or a project via it's id
    //This method is used in showById & showByName actions
    //--------------------------------------------------------------------------


    private function getFolder($id)
    {
      $tasks =  ($this->request($this->foldersUri($id)));
      if ($this->infos['errorBoolean']) {
        return $this->infos;
      }
      $tasks = json_decode($tasks);
      $tasks = $tasks->data;
      $temp = ['folderId' => $id, 'descendants' =>false, 'tasks' => $tasks];
      if ($this->descendants === 'true') {
        $temp['descendants'] = true;
      }
      if ($this->users === 'true') {
        $users = [];
        foreach ($tasks as $task) {
          foreach ($task->responsibleIds as $user) {
            if (!in_array($user, $users)) {
              $users[] = $user;
            }
          }
        }
        foreach ($users as $user) {
          $temp_user = $this->request("users/".$user);
          if (!$this->infos['errorBoolean']) {
            $temp_user = json_decode($temp_user)->data;
          }else {
            return $this->infos;
          }
          $temp['users'][] = $temp_user;
        }
      }
      return $temp;
    }


    //--------------------------------------------------------------------------
    //Use this method to get the required URI for the request
    //This method requires the id and has optional paramater descendants
    //--------------------------------------------------------------------------

    private function foldersUri($id, $descendants = 'false')
    {
      if ($this->descendants === 'true') {
        $descendants = 'true';
      }
      return "folders/".$id."/tasks?descendants=".$descendants."&fields=['briefDescription','responsibleIds','description']";
    }

    //--------------------------------------------------------------------------
    //Use this method to get all the ids via a project's or folder's title / name
    //This method returns an array of the found ids
    //Any callaback error (400,401 500 etc)
    //Return data only if there are not any errors
    //It add any found error to the static array $infos
    //--------------------------------------------------------------------------

    private function getIdByName()
    {
      $name = $this->name;
      $temp_folders = [];
      $folders = $this->request('folders');
      if ($this->infos['errorBoolean']) {
        return null ;
      }
      $folders = json_decode($folders);
      $success = false;
      foreach ($folders->data as $folder) {
        if ($folder->title == $name) {
          $temp_folders[] = $folder->id;
          $success = true;
        }
      }
      if ($success) {
        return $temp_folders;
      }else{
        $this->infos['messages'][] = 'Could not find the folder or the project with the given name';
        $this->infos['errorBoolean'] = true;
      }

    }
    //--------------------------------------------------------------------------
    //Use this method to send safetely request to an external server and handle
    //Any callaback error (400,401 500 etc)
    //Return data only if there are not any errors
    //It add any found error to the static array $infos
    //--------------------------------------------------------------------------

    private function request($url)
    {
      try {
        $res = $this->client->request('GET', $url);
        return $res->getBody();
      } catch (ConnectException $e) {
        $this->infos['messages'][] = "Could not connect to the Wrike's API's server";
        $this->infos['errorBoolean'] = true ;
      } catch(ClientException $e){
        $this->infos['status'] = $e->getResponse()->getStatusCode();
        $this->infos['messages'][] = $e->getResponse()->getReasonPhrase();
        $this->infos['errorBoolean'] = true ;
      }
    }
}
