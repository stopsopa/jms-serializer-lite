<?php

namespace Stopsopa\LiteSerializer\Dumpers;

use Stopsopa\LiteSerializer\Dumper;

class DumperTry1 extends Dumper
{
    public function dumpStopsopaLiteSerializerEntities_User($entity) {
        $data = $this->toArray($entity, array(
            'id'        => 'id',
            'group1'    => 'groups.1.name',
            'group2'    => array(
                'path'      => 'groups.10.name',
                'default'   => 'missing'
            )
        ));

        $data['scope'] = $this->scope;

        return $data;
    }

}
