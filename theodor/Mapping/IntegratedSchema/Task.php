<?php


namespace Theodor\Mapping\IntegratedSchema;

class Task
{
    public $title;
    public $description;
    public $sanitizedDescription;
    public $complete = false;

    public function __construct($data)
    {
        foreach ($data as $k=>$v){
            $this->{$k} = $v;
        }
        $this->sanitizedDescription = filter_var($this->description, FILTER_SANITIZE_STRING);
    }
}