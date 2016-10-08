<?php

namespace Stopsopa\LiteSerializer\Dumpers;

use Stopsopa\LiteSerializer\Dumper;
use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

class DumperNullInsteadOfException extends Dumper
{
    public function dumpStopsopaLiteSerializerEntities_User($entity) {
        $data = $this->toArray($entity, array(
            'name'      => 'name',
            'groups'    => 'groups'
        ));

        return $data;
    }
    public function dumpStopsopaLiteSerializerEntities_Group($entity) {

        if ($entity->getName() === 'ignoreme') {
            throw new DumperContinueException();
        }

        return $this->toArray($entity, array(
            'name'      => 'name'
        ));
    }
}
