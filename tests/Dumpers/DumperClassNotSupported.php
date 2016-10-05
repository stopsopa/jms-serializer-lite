<?php

namespace Stopsopa\LiteSerializer\Dumpers;
use ArrayAccess;
use Stopsopa\LiteSerializer\Dumper;
use Stopsopa\LiteSerializer\Entities\Group;
use Stopsopa\LiteSerializer\Entities\User;
use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

class DumperClassNotSupported extends Dumper
{
    public function dumpStopsopaLiteSerializerEntities_User($entity) {
        return $this->toArray($entity, array(
            'groups'    => 'groups'
        ));
    }
}
