<?php

namespace Stopsopa\LiteSerializer\Dumpers;

use Stopsopa\LiteSerializer\Dumper;
use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

class DumperEx extends Dumper
{
    public function dumpStopsopaLiteSerializerEntities_User($entity) {
        return $this->toArray($entity, array(
            'name'      => 'name',
            'groups'    => 'groups',
        ));
    }
    public function dumpStopsopaLiteSerializerEntities_Group($entity) {
        return $this->toArray($entity, array(
            'name'      => 'name',
            'nested'    => 'nested',
            'ex'        => array(
                'path'      => 'ex',
                'default'   => 'noex'
            )
        ));
    }
}
