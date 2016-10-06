<?php

namespace Stopsopa\LiteSerializer\Dumpers;

use Stopsopa\LiteSerializer\Dumper;

class DumperWrongKey extends Dumper
{
    public function dumpStopsopaLiteSerializerEntities_User($entity) {

        $dump = array(
            'groups'    => 'groups',
            'comments'  => 'comments'
        );

        if ($this->scope !== 'noname') {
            $dump['name'] = 'name';
        }

        return $this->toArray($entity, $dump);
    }
    public function dumpStopsopaLiteSerializerEntities_Group($entity) {

        $dump = array(
            'id'    => 'wrongKey'
        );

        if ($this->scope !== 'noname') {
            $dump['name'] = 'name';
        }

        return $this->toArray($entity, $dump);
    }
}
