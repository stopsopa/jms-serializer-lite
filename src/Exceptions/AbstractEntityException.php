<?php

namespace Stopsopa\LiteSerializer\Exceptions;

use Exception;

class AbstractEntityException extends Exception {
    const WRONG_KEY                             = 1;
    const METHOD_NOT_IMPLEMENTED                = 2;
    const CLASS_NOT_FOREACHABLE                 = 3;
    const ATTR_IS_NOT_STRING                    = 4;
    const ATTR_IS_EMPTY_STRING                  = 5;
    const DIRECTLY_CALLED_METHOD_DOESNT_EXIST   = 6;
}