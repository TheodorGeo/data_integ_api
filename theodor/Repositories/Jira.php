<?php


namespace Theodor\Repositories;

use App\Http\Controllers\JiraController;

class Jira
{
    public $token;
    public $email;
    public $domain;
    public $projectKey;
    public $fields;

    public function __construct($data)
    {
        foreach ($data as $k=>$v){
            $this->{$k} = $v;
        }
    }

    public static function get($data)
    {
        return new self($data);
    }

    public function handle()
    {
        return new JiraController([
            "token" => $this->token,
            "email" => $this->email,
            "domain" => $this->domain
        ]);
    }
}