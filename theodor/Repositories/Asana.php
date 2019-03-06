<?php


namespace Theodor\Repositories;

use App\Http\Controllers\AsanaController;

class Asana
{
    public $token;
    public $projectId;
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
        return new AsanaController([
            "token" => $this->token,
            "projectId" => $this->projectId,
            "fields" => $this->fields
        ]);
    }
}