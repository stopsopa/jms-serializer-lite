<?php

namespace Stopsopa\LiteSerializer\Dumpers;

use Stopsopa\LiteSerializer\Dumper;

class DumperClassNotSupported extends Dumper
{
    public function dumpStopsopaLiteSerializerEntities_User($entity) {
        return $this->toArray($entity, array(
            'groups'    => 'groups'
        ));
    }
}
