<?php

namespace Stopsopa\LiteSerializer\Exceptions;

use \Exception;

class AbstractEntityException extends Exception {
    const METHOD_NOT_IMPLEMENTED                = 1;
    const CLASS_NOT_FOREACHABLE                 = 2;
    const ATTR_NOT_STRING                       = 3;
    const ATTR_IS_EMPTY_STRING                  = 4;
    const DIRECTLY_CALLED_METHOD_DONT_EXIST     = 5;
}