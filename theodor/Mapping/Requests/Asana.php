<?php

namespace Theodor\Mapping\Requests;

use Theodor\Repositories\Asana as AsanaRepository;
use Theodor\Mapping\Responses\IntegratedSchema;
use Theodor\Mapping\IntegratedSchema\Task;
use Theodor\Mapping\IntegratedSchema\Info;
use Theodor\Mapping\IntegratedSchema\User;


class Asana
{
    private $project;

    private $schema;

    public function __construct($data, IntegratedSchema $schema)
    {
        $this->schema = $schema;
        $this->project = AsanaRepository::get($data)->handle()->show($data['projectId'], $data['fields']);
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
            "source" => 'Asana',
            "project" => $this->project[0]['project']->name
        ]));
    }

    private function setTasks()
    {
        foreach ($this->project[1]['tasks'] as $task){
            $this->schema->addTasks(new Task([
                "title" => $task->name,
                "description" => $task->notes,
                "complete" => false
            ]));
        }
    }

    private function setUsers()
    {
        foreach ($this->project[2]['users'] as $user){
            $this->schema->addUser(new User([
                "fullName" => $user->name,
                "email" => $user->email
            ]));
        }
    }
}