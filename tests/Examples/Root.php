<?php

namespace Stopsopa\LiteSerializer\Examples;

class Root {
    protected $prot     = 'protval';

    protected function getProtected_() {
        return 'test get';
    }
    protected function isProtected_() {
        return 'test is';
    }
    protected function hasHasProtected_() {
        return 'test has';
    }
}