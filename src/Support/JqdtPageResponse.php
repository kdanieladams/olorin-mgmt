<?php

namespace Olorin\Support;

class JqdtPageResponse
{
    public $draw = 1;
    public $recordsTotal = 0;
    public $recordsFiltered = 0;
    public $data = [];

    public function __construct($draw, $recordsTotal, $recordsFiltered, $data = [])
    {
        $this->draw = $draw;
        $this->recordsTotal = $recordsTotal;
        $this->recordsFiltered = $recordsFiltered;
        $this->data = $data;
    }
}