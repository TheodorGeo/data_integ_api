<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ConnectException;
use \GuzzleHttp\Exception\ClientException;

class JiraController extends Controller
{
    private $token ;
    private $domain;
    private $email;
    private $project;
    private $base_uri;
    private $infos = [
      'errorBoolean' => false
    ];

    function __construct()
    {
      $this->token = request('token');
      $this->domain = request('domain');
      $this->email = request('email');
      $this->base_uri = "https://".$this->domain.'/rest/api/2/';

      $this->client = new Client([
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.base64_encode($this->email.':'.$this->token)
        ]
      ]);
    }


    public function index()
    {
      $projects = $this->getAllProjects();
      if ($this->infos['errorBoolean']) {
        return $this->infos;
      }
      return $projects;
    }


    public function project($projectKey)
    {
      $this->project = $projectKey;
      $project = $this->getProject();
      if ($this->infos['errorBoolean']) {
        return $this->infos;
      }
      return $project;

    }


    public function show($projectKey)
    {
      $response = [];
      $this->project = $projectKey;
      $users_bool = false;
      $issues_bool = false;
      $project = $this->getProject();
      $total_issues = $this->totalIssues();
      if ($this->infos['errorBoolean']) {
        echo 'asdfasdfasdf';
        return $this->infos;
      }
      $fields = $this->fields();
      if ($fields['users']) {
        $users = $this->getUsers();
        $users_bool = true;
      }
      if ($fields['issues']) {
        $issues = $this->getIssues();
        $issues_bool = true;
      }
      if ($this->infos['errorBoolean']) {
        return $this->infos;
      }
      $response['project'] = array(json_decode($project));
      if ($issues_bool) {
        $response['issues'] = $issues;
      }
      if ($users_bool) {
        $response['users'] = $users;
      }
      return $response;

    }


    //--------------------------------------------------------------------------
    //Use this method to send safetely request to an external server and handle
    //Any callaback error (400,401 500 etc)
    //Return data only if there are not any errors
    //It add any found error to the static array $infos
    //--------------------------------------------------------------------------

    private function totalIssues()
    {
      $url = $this->base_uri.'search?jql=project='.$this->project.'&startAt=0&maxResults=0';
      $issues = $this->request($url);
      if (!$this->infos['errorBoolean']) {
        return json_decode($issues)->total;
      }
    }

    //--------------------------------------------------------------------------
    //Use this method to get all the issues of a projects
    // IMPORTANT !!!: JIRA has a limit to access only 100 issues per requested
    // This methods returns all the issues with multiple requests
    // IMPORTANT !!! NOT TESTED VERY WELL
    //--------------------------------------------------------------------------

    private function getIssues()
    {

      $issues = [];
      $total_issues = $this->totalIssues();
      if ($this->infos['errorBoolean']) {
        return 0;
      }
      if ($total_issues % 100 !== 0 ) {
        $total_int = intdiv($total_issues, 100) +1;
      }else {
        $total_int = $total_issues/100;
      }
      for ($i=0; $i < $total_int ; $i++) {
        $start = 100*$i;
        $max = 100*($i+1);
        $url = $this->base_uri.'search?jql=project='.$this->project.'&startAt='.$start.'&maxResults='.$max;
        $temp= $this->request($url);
        $issues[] = array(json_decode($temp));
      }

      return $issues;

    }

    //--------------------------------------------------------------------------
    //Use this method to get the assignable users of a project
    //--------------------------------------------------------------------------


    private function getUsers()
    {
      $url = $this->base_uri.'user/assignable/multiProjectSearch?projectKeys='.$this->project;
      $users = $this->request($url);
      if (!$this->infos['errorBoolean']) {
        return json_decode($users);
      }
    }

    //--------------------------------------------------------------------------
    //Use this method to get the infos of a project. This infos are included
    //when issues or users are requested !
    //--------------------------------------------------------------------------

    private function getProject()
    {
      $url = $this->base_uri."project/".$this->project;
      $project = $this->request($url);
      return $project;
    }


    //--------------------------------------------------------------------------
    //Use this method to get all the projcts based on user's token domain and
    //email
    //--------------------------------------------------------------------------


    private function getAllProjects()
    {
      $url = $this->base_uri.'project?expand=description,lead,issueTypes,url,projectKeys';
      $projects = $this->request($url);
      return $projects;
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
        $this->infos['error'] = "Could not connect to the Jira's API's server";
        $this->infos['errorBoolean'] = true ;
      } catch(ClientException $e){
        $this->infos['status'] = $e->getResponse()->getStatusCode();
        $this->infos['messages'][] = $e->getResponse()->getReasonPhrase();
        $this->infos['errorBoolean'] = true ;
      }
    }



    //--------------------------------------------------------------------------
    //Use this method to make sure that the fields issues or users are
    //existed during the request /jira/project/{projectKey
    //Return data only if there are not any errors
    //It add any found error to the static array $infos
    //--------------------------------------------------------------------------


    private function fields()
    {
      $fields = [
        'issues' => false,
        'users' => false
      ];
      $fieldBoolean = false;
      if (request('fields') !== null) {
        $query = explode(',', request('fields'));
        foreach ($query as $f) {
          if (($f === 'issues') || ($f === 'users')) {
            $fields[$f] = true;
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
