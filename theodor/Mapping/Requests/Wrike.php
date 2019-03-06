<?php

namespace Theodor\Mapping\Requests;


use Theodor\Mapping\Responses\IntegratedSchema;
use Theodor\Repositories\Wrike as WrikeRepository;

use Theodor\Mapping\IntegratedSchema\Info;
use Theodor\Mapping\IntegratedSchema\Task;
use Theodor\Mapping\IntegratedSchema\User;

class Wrike
{
    private $folder;

    private $schema;

    public function __construct($data, IntegratedSchema $schema)
    {
        $this->schema = $schema;
        $this->folder = WrikeRepository::get($data)->handle()->showByName()[0];
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
            "source" => 'Wrike',
            "project" => $this->folder['folderId']
        ]));
    }

    private function setTasks()
    {
        foreach ($this->folder['tasks'] as $task){
            $this->schema->addTasks(new Task([
                "title" => $task->title,
                "description" => $task->description,
                "complete" => ($task->status == 'Active') ? false : true
            ]));
        }
    }

    private function setUsers()
    {
        foreach ($this->folder['users'] as $user){
            $this->schema->addUser(new User([
                "fullName" => $user[0]->firstName." ". $user[0]->lastName,
                "email" => $user[0]->profiles[0]->email
            ]));
        }
    }
}
