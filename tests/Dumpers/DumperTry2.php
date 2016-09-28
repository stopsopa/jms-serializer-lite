<?php

namespace Stopsopa\LiteSerializer\Dumpers;
use ArrayAccess;
use Stopsopa\LiteSerializer\Dumper;
use Stopsopa\LiteSerializer\Entities\Group;
use Stopsopa\LiteSerializer\Entities\User;

class DumperTry2 extends Dumper
{
    public function dumpStopsopaLiteSerializerEntities_User($entity) {

        $entity;
        $data = $this->toArray($entity, array(
            'groups'    => 'groups'
        ));

        return $data;
    }

}
