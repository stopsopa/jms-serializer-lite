<?php

namespace Stopsopa\LiteSerializer\Dumpers;
use ArrayAccess;
use Stopsopa\LiteSerializer\Dumper;
use Stopsopa\LiteSerializer\Entities\Group;
use Stopsopa\LiteSerializer\Entities\User;
use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

class DumperNotForeachable extends Dumper
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

        return $this->toArray($entity, array(
            'name' => array('name', 'defaultname', Dumper::MODE_COLLECTION)
        ));
    }

}
