<?php

namespace Stopsopa\LiteSerializer;

use PHPUnit_Framework_TestCase;
use Stopsopa\LiteSerializer\Dumpers\DumperClassNotSupported;
use Stopsopa\LiteSerializer\Dumpers\DumperInterface;
use Stopsopa\LiteSerializer\Dumpers\DumperNotForeachable;
use Stopsopa\LiteSerializer\Dumpers\DumperTry1;
use Stopsopa\LiteSerializer\Dumpers\DumperTry2;
use Stopsopa\LiteSerializer\Entities\Comments;
use Stopsopa\LiteSerializer\Entities\User;
use Stopsopa\LiteSerializer\Entities\Group;
use DateTime;

class DumperTest extends PHPUnit_Framework_TestCase {
    public function getUser($user) {

        $u = new User();

        foreach ($user as $prop => $data) {
            if (!is_array($data)) {
                $u->{'set'.ucfirst($prop)}($data);
            }
        }

        if (!empty($user['groups'])) {
            foreach ($user['groups'] as $g) {

                $group = new Group();

                foreach ($g as $prop => $data) {
                    $group->{'set'.ucfirst($prop)}($data);
                }

                $u->addGroup($group);
            }
        }

        return $u;
    }
    protected function getSet1() {
        return $this->getUser(array(
            'id' => 1,
            'name' => 'user1',
            'surname' => 'surname1',
            'groups' => array(
                array(
                    'id' => 1,
                    'name' => 'group1'
                ),
                array(
                    'id' => 2,
                    'name' => 'group2'
                ),
                array(
                    'id' => 3,
                    'name' => 'group3'
                )
            )
        ));
    }
    protected function getSetDate() {
        return $this->getUser(array(
            'id' => 1,
            'name' => 'user1',
            'surname' => 'surname1',
            'groups' => array(
                array(
                    'id' => 1,
                    'name' => new DateTime('2016-07-07 09:04:03')
                ),
                array(
                    'id' => 2,
                    'name' => 'group2'
                ),
                array(
                    'id' => 3,
                    'name' => 'group3'
                )
            )
        ));
    }
    public function testUser() {

        $u = $this->getSet1();

        $dump = DumperTry1::getInstance()->dump($u);

        $this->assertSame('{"id":1,"group1":"group2","group2":"missing","level":0,"scope":null}', json_encode($dump));
    }
    public function testNested() {

        $u = $this->getSet1();

        $dump = DumperTry2::getInstance()->dump($u);

        $this->assertSame('{"groups":[{"id":1,"name":"group1"},{"id":3,"name":"group3"}],"name":"user1"}', json_encode($dump));
    }
    public function testDate() {

        $u = $this->getSetDate();

        $dump = DumperTry2::getInstance()->dump($u);

        $this->assertSame('{"groups":[{"id":1,"name":"2016-07-07 09:04:03"},{"id":3,"name":"group3"}],"name":"user1"}', json_encode($dump));
    }
    public function testScope() {

        $u = $this->getSetDate();

        $dump = DumperTry2::getInstance()->dumpScope($u, 'noname');

        $this->assertSame('{"groups":[{"id":1},{"id":3}]}', json_encode($dump));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 3
     * @expectedExceptionMessage Entity 'DateTime' is not foreachable
     */
    public function testNotForeachable() {
        DumperNotForeachable::getInstance()->dump($this->getSetDate());
    }
    public function testInterface() {

        $u = $this->getSetDate();

        $u->setComments(new Comments());

        $dump = DumperInterface::getInstance()->dumpScope($u, 'noname');

        $this->assertSame('{"groups":[{"id":1},{"id":2},{"id":3}],"comments":["first comment","second comment","third comment","noname",1]}', json_encode($dump));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 2
     * @expectedExceptionMessage Dumping entity of class 'Stopsopa\LiteSerializer\Entities\Group' is not handled by dumper 'Stopsopa\LiteSerializer\Dumpers\DumperClassNotSupported', this entity should implement interface 'Stopsopa\LiteSerializer\DumpToArrayInterface' or add method 'Stopsopa\LiteSerializer\Dumpers\DumperClassNotSupported->dumpStopsopaLiteSerializerEntities_Group($entity)' to dumper
     */
    public function testClassNotSupported() {
        DumperClassNotSupported::getInstance()->dump($this->getSetDate());
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 3
     * @expectedExceptionMessage Entity 'Stopsopa\LiteSerializer\Entities\User' is not foreachable
     */
    public function testDumpMode() {

        DumperInterface::getInstance()->dumpMode($this->getSetDate(), Dumper::MODE_COLLECTION);
    }
}