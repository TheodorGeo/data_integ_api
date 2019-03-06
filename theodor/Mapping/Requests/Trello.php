<?php

namespace Theodor\Mapping\Requests;

use Theodor\Repositories\Trello as TrelloRepository;
use Theodor\Mapping\Responses\IntegratedSchema;
use Theodor\Mapping\IntegratedSchema\Task;
use Theodor\Mapping\IntegratedSchema\Info;
use Theodor\Mapping\IntegratedSchema\User;


class Trello
{
    private $board;

    private $schema;

    public function __construct($data, IntegratedSchema $schema)
    {
        $this->schema = $schema;
        $this->board = TrelloRepository::get($data)->handle()->boardByShortLink($data['shortLink']);
        $this->build();
    }

    private function build()
    {
        $this->setInfos();
        $this->setTasks();
        $this->setUsers();
    }

    private function setInfos()
    {
        $this->schema->addInfos(new Info([
            "source" => 'Trello',
            "project" => $this->board['board']['name']
        ]));
    }

    private function setTasks()
    {
        foreach ($this->board['cards'] as $card){
            $this->schema->addTasks(new Task([
                "title" => $card->name,
                "description" => $card->desc,
                "complete" => $card->closed
            ]));
        }
    }

    private function setUsers()
    {
        foreach ($this->board['members'] as $member){
            $this->schema->addUser(new User([
                "fullName" => $member->fullName,
                "email" => "Not Available"
            ]));
        }
    }
}