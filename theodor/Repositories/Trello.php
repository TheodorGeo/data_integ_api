<?php


namespace Theodor\Repositories;

use App\Http\Controllers\TrelloController;
use GuzzleHttp;

class Trello
{
    public $token;
    public $shortLink;
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
        return new TrelloController([
            "token" => $this->token,
            "shortLink" => $this->shortLink,
            "fields" => $this->fields
        ]);
    }
}