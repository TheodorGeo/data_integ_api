<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ConnectException;
use \GuzzleHttp\Exception\ClientException;

class AsanaController extends Controller
{
  private $infos = [
    'errorBoolean' => false
  ];
  private $token;
  private $fields = [];

  function __construct()
  {
    $this->token = request('token');
    $this->client = new Client([
      'base_uri' => 'https://app.asana.com/api/1.0/',
      'headers' => [
          'Authorization' => 'Bearer '.$this->token
      ]
    ]);
  }


  public function index()
  {
    $projects = $this->request('projects?opt_fields=notes,name,id,workspace');
    if($this->infos['errorBoolean']){
      return $this->infos;
    }
    return $projects;
  }


  public function show($id)
  {
    if (!(request('fields') === 'users' || request('fields') === 'tasks' || request('fields') === 'users,tasks' || request('fields') === 'tasks,users')) {
      $this->infos['errorBoolean'] = true;
      $this->infos['messages'] = 'At least 1 field is required (users or tasks)';
      return $this->infos;
    }else {
      $this->fields = explode(',', request('fields'));
      $data = [];
    }
    foreach ($this->fields as $field) {
      if ($field === 'tasks') {
        $data[] = $this->getTasks($id);
      }
      if ($field === 'users') {
        $data[] = $this->getUsers($id);
      }
    }
    if ($this->infos['errorBoolean']) {
      return $this->infos;
    }else {
      array_unshift($data,$this->project);
      return $data;
    }
  }


  public function tasks($id)
  {
    $tasks = $this->getTasks($id);
    if ($this->infos['errorBoolean']) {
      return $this->infos;
    }
    return $tasks;
  }


  public function users($id)
  {
    $users = $this->getUsers($id);
    if ($this->infos['errorBoolean']) {
      return $this->infos;
    }
    return $users;
  }


  //--------------------------------------------------------------------------
  //Use this method to get all tasks inside a project
  //--------------------------------------------------------------------------


  private function getTasks($id)
  {
    $tasks = $this->request('projects/'.$id.'/tasks?opt_fields=name,id,owner,current_status,due_date,created_at,members,followers,notes,team,workspace,color,archived,modified_at,public');
    if (!$this->infos['errorBoolean']) {
      return ['tasks'=> json_decode($tasks)->data];
    }
  }


  //--------------------------------------------------------------------------
  //Use this method to get all users inside a workspace / project
  //--------------------------------------------------------------------------


  private function getUsers($id)
  {
    $workspace = $this->projectToWorkspace($id);
    if ($workspace) {
      $users = $this->request('workspaces/'.$workspace.'/users?opt_fields=name,email,id');
      if (!$this->infos['errorBoolean']) {
        return ['users'=> json_decode($users)->data];
      }
    }
  }


  //--------------------------------------------------------------------------
  //This method generate the id of the project's workspace via a project's id
  //--------------------------------------------------------------------------


  private function projectToWorkspace($id)
  {
    $project = $this->getProject($id);
    if (!$this->infos['errorBoolean']) {
      $data = json_decode($project)->data;
      return $data->workspace->id;
    }else {
      return false;
    }
  }


  //--------------------------------------------------------------------------
  //Use this method to get a projects infos via it's id
  //When it grabs data successfully it's define a new static variable with
  //projects infos ( $this->project )
  //--------------------------------------------------------------------------


  private function getProject($id)
  {
    $project = $this->request('projects/'.$id.'?opt_fields=name,id,workspace');
    if (!$this->infos['errorBoolean']) {
      $this->project = ['project' => json_decode($project)->data];
      return $project;
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
