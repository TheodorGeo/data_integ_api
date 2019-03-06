<?php


namespace Theodor\Mapping\Responses;

use Theodor\Mapping\IntegratedSchema\Task;
use Theodor\Mapping\IntegratedSchema\User;
use Theodor\Mapping\IntegratedSchema\Info;


class IntegratedSchema
{
    public $infos=[];
    public $tasks=[];
    public $users=[];
    public $errors=[];


    public function __construct()
    {
        $this->setDate();
    }

    public function addInfos(Info $info)
    {
        $this->infos['sources'][] = $info->source;
        $this->infos['project'][] = $info->project;
    }


    public function addTasks(Task $task)
    {
        $this->tasks[] = $task;
    }


    public function addUser(User $user)
    {
        $this->users[] = $user;
    }

    private function setDate()
    {
        $this->infos['date'] = date('Y-m-d');
    }
}