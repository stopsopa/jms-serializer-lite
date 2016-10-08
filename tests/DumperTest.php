<?php

namespace Stopsopa\LiteSerializer;

use PHPUnit_Framework_TestCase;
use Stopsopa\LiteSerializer\Dumpers\DumperClassNotSupported;
use Stopsopa\LiteSerializer\Dumpers\DumperContinueNested;
use Stopsopa\LiteSerializer\Dumpers\DumperInterface;
use Stopsopa\LiteSerializer\Dumpers\DumperNotForeachable;
use Stopsopa\LiteSerializer\Dumpers\DumperEx;
use Stopsopa\LiteSerializer\Dumpers\DumperNullInsteadOfException;
use Stopsopa\LiteSerializer\Dumpers\DumperSaveKeys;
use Stopsopa\LiteSerializer\Dumpers\DumperStack;
use Stopsopa\LiteSerializer\Dumpers\DumperTransform;
use Stopsopa\LiteSerializer\Dumpers\DumperTry1;
use Stopsopa\LiteSerializer\Dumpers\DumperTry2;
use Stopsopa\LiteSerializer\Dumpers\DumperWrongKey;
use Stopsopa\LiteSerializer\Entities\Comments;
use Stopsopa\LiteSerializer\Entities\Group;
use Stopsopa\LiteSerializer\Entities\User;
use Stopsopa\LiteSerializer\Exceptions\AbstractEntityException;
use Stopsopa\LiteSerializer\Libs\AbstractEntity;
use Proxies\__CG__\LiteSerializer\Entities\Group as CgGroup;

class DumperTest extends PHPUnit_Framework_TestCase {
    public function testUser() {

        $u = DataProvider::getSet1();

        $dumper = DumperTry1::getInstance();

        $dump = $dumper->dump($u);

        $this->assertSame('{"id":1,"group1":"group2","group2":"missing","scope":null}', json_encode($dump));
    }
    public function testNested() {

        $u = DataProvider::getSet1();

        $dumper = DumperTry2::getInstance();

        $dump = $dumper->dump($u);

        $this->assertSame('{"groups":[{"id":1,"name":"group1"},{"id":3,"name":"ignoreme"}],"name":"user1"}', json_encode($dump));
    }
    public function testDate() {

        $u = DataProvider::getSetDate();

        $dumper = DumperTry2::getInstance();

        $dump = $dumper->dump($u);

        $this->assertSame('{"groups":[{"id":1,"name":"2016-07-07 09:04:03"},{"id":3,"name":"ignoreme"}],"name":"user1"}', json_encode($dump));
    }
    public function testScope() {

        $u = DataProvider::getSetDate();

        $dumper = DumperTry2::getInstance();

        $dump = $dumper->dumpScope($u, 'noname');

        $this->assertSame('{"groups":[{"id":1},{"id":3}]}', json_encode($dump));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 3
     * @expectedExceptionMessage Entity 'DateTime' is not foreachable
     */
    public function testNotForeachable() {
        DumperNotForeachable::getInstance()->dump(DataProvider::getSetDate());
    }
    public function testInterface() {

        $u = DataProvider::getSetDate();

        $u->setComments(new Comments());

        $dumper = DumperInterface::getInstance();

        $dump = $dumper->dumpScope($u, 'noname');

        $this->assertSame('{"groups":[{"id":1},{"id":3},{"id":2}],"comments":["first comment","second comment","third comment","noname"]}', json_encode($dump));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 2
     * @expectedExceptionMessage Dumping entity of class 'Stopsopa\LiteSerializer\Entities\Group' is not handled by dumper 'Stopsopa\LiteSerializer\Dumpers\DumperClassNotSupported', this entity should implement interface 'Stopsopa\LiteSerializer\DumpToArrayInterface' or add method 'Stopsopa\LiteSerializer\Dumpers\DumperClassNotSupported->dumpStopsopaLiteSerializerEntities_Group($entity)' to dumper
     */
    public function testClassNotSupported() {

        $dumper = DumperClassNotSupported::getInstance();

        $dumper->dump(DataProvider::getSetDate());
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 3
     * @expectedExceptionMessage Entity 'Stopsopa\LiteSerializer\Entities\User' is not foreachable
     */
    public function testDumpMode() {

        $dumper = DumperInterface::getInstance();

        $dumper->dumpMode(DataProvider::getSetDate(), Dumper::MODE_COLLECTION);
    }

    public function testWrongKey() {

        $dumper = DumperWrongKey::getInstance();

        $data = DataProvider::getSetDate();

        $isExc = false;
        try {
            $dumper->dump($data);
        }
        catch (AbstractEntityException $e) {
            $isExc = true;
            $this->assertSame(1, $e->getCode());
            $this->assertSame(
                "Stopsopa\LiteSerializer\Libs\AbstractEntity::internalValueByMethodOrAttribute ".
                "error: Property 'wrongKey' doesn't exist and methods getWrongKey(), ".
                "isWrongKey(), hasWrongKey(), wrongKey() are not accessible in 'Stopsopa\LiteSerializer\Entities\Group'",
                $e->getMessage()
            );
        }

        $this->assertTrue($isExc);

        $isExc = false;
        try {
            $group = $data->getGroups();

            $group = $group[1];

            $dumper->dump($group);
        }
        catch (AbstractEntityException $e) {
            $isExc = true;
            $this->assertSame(1, $e->getCode());
            $this->assertSame(
                "Stopsopa\LiteSerializer\Libs\AbstractEntity::internalValueByMethodOrAttribute ".
                "error: Property 'wrongKey' doesn't exist and methods getWrongKey(), ".
                "isWrongKey(), hasWrongKey(), wrongKey() are not accessible in 'Stopsopa\LiteSerializer\Entities\Group'",
                $e->getMessage()
            );
        }

        $this->assertTrue($isExc);
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 2
     * @expectedExceptionMessage Dumping entity of class 'LiteSerializer\Entities\Group' is not handled by dumper 'Stopsopa\LiteSerializer\Dumpers\DumperTransform', this entity should implement interface 'Stopsopa\LiteSerializer\DumpToArrayInterface' or add method 'Stopsopa\LiteSerializer\Dumpers\DumperTransform->dumpLiteSerializerEntities_Group($entity)' to dumper
     */
    public function testCg() {

        $dumper = DumperTransform::getInstance();

        $u = DataProvider::getSetDate();

        require_once dirname(__FILE__).'/Entities/__CG__/Group.php';

        $u->setGroups(array(new CgGroup()));

        $dump = $dumper->dump($u);

        $this->assertSame('tes', $dump);
    }
    public function testStack() {

        $dumper = DumperStack::getInstance();

        $u1 = DataProvider::getSetDate();

        $u2 = DataProvider::getSetDate();

        $u1->addGroup($u2);

        $this->assertSame(DataProvider::stackData(), json_encode($dumper->dump($u1)));
    }
    public function testContinueNested() {

        $u1 = DataProvider::getSetDate();

        $mg = $u1->getGroups();

        /* @var $mg Group */
        $mg = $mg[1];

        $user = new User();
        $user->setName('ignoreme');

        $mg->setNested($user);

        $dumper = DumperContinueNested::getInstance();

        $this->assertSame('{"groups":[{"name":"2016-07-07 09:04:03","nested":null},{"name":"group2","nested":null}],"name":"user1"}', json_encode($dumper->dump($u1)));
    }
    public function testEx() {

        $u1 = DataProvider::getSetDate();

        $g = $u1->getGroups();

        $g = $g[1];

        $g->ex = 'isval';

        $dumper = DumperEx::getInstance();

        $this->assertSame('{"name":"user1","groups":[{"name":"2016-07-07 09:04:03","nested":null,"ex":"noex"},{"name":"ignoreme","nested":null,"ex":"isval"},{"name":"group2","nested":null,"ex":"noex"}]}', json_encode($dumper->dump($u1)));
    }
    public function testNullInsteadOfException() {

        $dumper = DumperNullInsteadOfException::getInstance();

        $g = new Group();

        $g->setName('test');

        $this->assertSame(
            '{"name":"test"}',
            json_encode($dumper->dump($g))
        );

        $g->setName('ignoreme');

        $this->assertSame(
            'null',
            json_encode($dumper->dump($g))
        );
    }
    public function testSaveKeys() {

        $u1 = DataProvider::getSetDate();

        $g = $u1->getGroups();

        $tmp = array();
        foreach ($g as $key => $gg) {
            $tmp['key'.$key] = $gg;
        }

        $u1->setGroups($tmp);

        $dumper = DumperSaveKeys::getInstance();

        $this->assertSame('{"name":"user1","groups":{"key0":{"name":"2016-07-07 09:04:03"},"key2":{"name":"group2"}}}', json_encode($dumper->dump($u1)));
    }
}