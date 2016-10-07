<?php

namespace Stopsopa\LiteSerializer\Entities;

use Stopsopa\LiteSerializer\DumpToArrayInterface;

class Comments implements DumpToArrayInterface  {
    protected $comments;
    public function __construct()
    {
        $this->comments = array(
            'first comment',
            'second comment',
            'third comment'
        );
    }

    public function dumpToArray($scope)
    {
        $data = $this->comments;

        $data[] = $scope;

        return $data;
    }
}