<?php

namespace Stopsopa\LiteSerializer\Dumpers;

use Stopsopa\LiteSerializer\Dumper;

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
            'name' => $this->helperMode('name', Dumper::MODE_COLLECTION, 'defaultname')
        ));
    }

}
