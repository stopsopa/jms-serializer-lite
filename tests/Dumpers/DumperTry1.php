<?php

namespace Stopsopa\LiteSerializer\Dumpers;
use ArrayAccess;
use Stopsopa\LiteSerializer\Dumper;
use Stopsopa\LiteSerializer\Entities\Group;
use Stopsopa\LiteSerializer\Entities\User;

class DumperTry1 extends Dumper
{
    public function dumpStopsopaLiteSerializerEntities_User($entity) {
        $data = $this->toArray($entity, array(
            'id'        => 'id',
            'group1'    => 'groups.1.name',
            'group2'    => array('groups.10.name', 'missing')
        ));

        $data['level'] = $this->level;
        $data['scope'] = $this->scope;

        return $data;
    }

}
