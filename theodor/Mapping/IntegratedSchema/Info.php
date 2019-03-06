<?php


namespace Theodor\Mapping\IntegratedSchema;

class Info
{
    public $source;
    public $project;

    public function __construct($data)
    {
        foreach ($data as $k=>$v){
            $this->{$k} = $v;
        }
    }
}