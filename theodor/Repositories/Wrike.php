<?php


namespace Theodor\Repositories;

use App\Http\Controllers\WrikeController;

class Wrike
{
    public $token;
    public $folderName;
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
        return new WrikeController([
            "token" => $this->token,
            "folderName" => $this->folderName,
            "fields" => $this->fields
        ]);
    }
}