<?php

namespace Stopsopa\LiteSerializer\Dumpers;

use Stopsopa\LiteSerializer\Dumper;
use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

class DumperSaveKeys extends Dumper
{
    public function dumpStopsopaLiteSerializerEntities_User($entity) {

        $data = $this->toArray($entity, array(
            'name'      => 'name',
            'groups'    => array(
                'path'      => 'groups',
                'savekeys'  => true
            )
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
    public function dump_DateTime($date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
