<?php

namespace Stopsopa\LiteSerializer\Examples;

class ExampleClass extends Root {
    public $pub         = 'pubval';
    public $pub_is      = 'pubval_is';
    public $pub_get     = 'pubval_get';
    public $pub_has     = 'pubval_has';
    private $something;
    private $priv = 'priv';

    public function isPubIs()
    {
        return $this->pub_is . ' - method is';
    }
    public function getPubGet()
    {
        return $this->pub_get . ' - method get';
    }
    public function hasPubHas()
    {
        return $this->pub_has . ' - method has';
    }
    public function getSomething()
    {
        return $this->something;
    }
    public function setSomething($something)
    {
        $this->something = $something;
        return $this;
    }
    protected function getProtected() {
        return 'protected';
    }

}