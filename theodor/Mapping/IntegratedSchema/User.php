<?php


namespace Theodor\Mapping\IntegratedSchema;

class User
{
    public $fullName;
    public $email;

    public function __construct($data)
    {
        foreach ($data as $k=>$v){
            $this->{$k} = $v;
        }
    }
}