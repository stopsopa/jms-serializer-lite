<?php

namespace Stopsopa\LiteSerializer\Dumpers;

use Stopsopa\LiteSerializer\Dumper;
use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

class DumperTry2 extends Dumper
{
    public function dumpStopsopaLiteSerializerEntities_User($entity) {

        $dump = array(
            'groups'    => 'groups',
        );

        if ($this->scope !== 'noname') {
            $dump['name'] = 'name';
        }

        return $this->toArray($entity, $dump);
    }
    public function dumpStopsopaLiteSerializerEntities_Group($entity) {

        if ($entity->getId() === 2) {
            throw new DumperContinueException();
        }

        $dump = array(
            'id' => 'id'
        );

        if ($this->scope !== 'noname') {
            $dump['name'] = 'name';
        }

        return $this->toArray($entity, $dump);
    }

}
