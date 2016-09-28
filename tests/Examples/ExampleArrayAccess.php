<?php

namespace Stopsopa\LiteSerializer\Examples;
use ArrayAccess;

class ExampleArrayAccess implements ArrayAccess {
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}