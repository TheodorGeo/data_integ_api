<?php

namespace Theodor\Mapping\Requests;

use Theodor\Repositories\Jira as JiraRepository;
use Theodor\Mapping\Responses\IntegratedSchema;
use Theodor\Mapping\IntegratedSchema\Task;
use Theodor\Mapping\IntegratedSchema\Info;
use Theodor\Mapping\IntegratedSchema\User;


class Jira
{
    private $project;

    private $schema;

    public function __construct($data, IntegratedSchema $schema)
    {
        $this->schema = $schema;
        $this->project = JiraRepository::get($data)->handle()->show($data['projectKey'], $data['fields']);
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
            "source" => 'JIRA',
            "project" => $this->project['project'][0]->key
        ]));
    }

    private function setTasks()
    {
        foreach ($this->project['issues'][0][0]->issues as $issue){
            try{
                $completed = ($issue->fields->resolution->name == 'Done') ? true : false;
            }
            catch (\Exception $e){
                $completed = false;
            }
            $this->schema->addTasks(new Task([
                "title" => $issue->fields->summary,
                "description" => $issue->fields->description,
                "complete" => $completed
            ]));
        }
    }

    private function setUsers()
    {
        foreach ($this->project['users'] as $user){
            $this->schema->addUser(new User([
                "fullName" => $user->displayName,
                "email" => $user->emailAddress
            ]));
        }
    }
}