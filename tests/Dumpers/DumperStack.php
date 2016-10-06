<?php

namespace Stopsopa\LiteSerializer\Dumpers;
use ArrayAccess;
use Stopsopa\LiteSerializer\Dumper;
use Stopsopa\LiteSerializer\Entities\Group;
use Stopsopa\LiteSerializer\Entities\User;
use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

class DumperStack extends Dumper
{
    public function dumpStopsopaLiteSerializerEntities_User($entity) {

        $dump = array(
            'groups'    => 'groups'
        );

        $data = $this->toArray($entity, $dump);

        $data['s'] = $this->stack;

        return $data;
    }
    public function dumpStopsopaLiteSerializerEntities_Group($entity) {

        return array(
            's' => $this->stack
        );
    }
}
