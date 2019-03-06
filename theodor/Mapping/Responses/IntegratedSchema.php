<?php


namespace Theodor\Mapping\Responses;

use Theodor\Mapping\IntegratedSchema\Task;
use Theodor\Mapping\IntegratedSchema\User;
use Theodor\Mapping\IntegratedSchema\Info;


class IntegratedSchema
{
    static $infos=[];
    static $tasks=[];
    static $users=[];

    public static function setDate()
    {
        self::$infos['date'] = date('Y-m-d');
    }


    public static function addInfos(Info $info)
    {
        self::$infos['sources'][] = $info->source;
        self::$infos['project'][] = $info->project;
    }


    public static function addTasks(Task $task)
    {
        self::$tasks[] = $task;
    }


    public static function addUser(User $user)
    {
        self::$tasks[] = $user;
    }
}