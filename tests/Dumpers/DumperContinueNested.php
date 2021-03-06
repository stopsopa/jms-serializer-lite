<?php

namespace Stopsopa\LiteSerializer\Dumpers;

use Stopsopa\LiteSerializer\Dumper;
use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

class DumperContinueNested extends Dumper
{
    public function dumpStopsopaLiteSerializerEntities_User($entity) {

        if ($entity->getName() === 'ignoreme') {
            throw new DumperContinueException();
        }

        return $this->toArray($entity, array(
            'groups'    => 'groups',
            'name'      => 'name'
        ));
    }
    public function dumpStopsopaLiteSerializerEntities_Group($entity) {

        if ($entity->getName() === 'ignoreme') {
            throw new DumperContinueException();
        }

        return $this->toArray($entity, array(
            'name'      => 'name',
            'nested'    => 'nested'
        ));
    }
    public function dump_DateTime($date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
