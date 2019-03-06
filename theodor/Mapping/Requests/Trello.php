<?php

namespace Theodor\Mapping\Requests;

use Theodor\Repositories\Trello as TrelloRepository;


class Trello
{
    private $trelloRepository;

    public function __construct(TrelloRepository $trelloRepository)
    {
        $this->trelloRepository = $trelloRepository;
    }

    public function cards()
    {

    }
}