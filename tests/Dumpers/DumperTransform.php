<?php

namespace Stopsopa\LiteSerializer\Dumpers;
use ArrayAccess;
use Stopsopa\LiteSerializer\Dumper;
use Stopsopa\LiteSerializer\Entities\Group;
use Stopsopa\LiteSerializer\Entities\User;
use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

class DumperTransform extends Dumper
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
            'id'    => 'id'
        );

        if ($this->scope !== 'noname') {
            $dump['name'] = 'name';
        }

        return $this->toArray($entity, $dump);
    }
    public function transform($data) {

        $data['groups'][0]['name'] = 'this should be name not date';

        return $data;
    }
}