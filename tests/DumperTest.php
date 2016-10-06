<?php

namespace Stopsopa\LiteSerializer;

use PHPUnit_Framework_TestCase;
use Stopsopa\LiteSerializer\Dumpers\DumperClassNotSupported;
use Stopsopa\LiteSerializer\Dumpers\DumperInterface;
use Stopsopa\LiteSerializer\Dumpers\DumperNotForeachable;
use Stopsopa\LiteSerializer\Dumpers\DumperStack;
use Stopsopa\LiteSerializer\Dumpers\DumperTransform;
use Stopsopa\LiteSerializer\Dumpers\DumperTry1;
use Stopsopa\LiteSerializer\Dumpers\DumperTry2;
use Stopsopa\LiteSerializer\Dumpers\DumperWrongKey;use Stopsopa\LiteSerializer\Entities\Comments;
use Stopsopa\LiteSerializer\Entities\User;
use Stopsopa\LiteSerializer\Entities\Group;
use DateTime;
use Stopsopa\LiteSerializer\Exceptions\AbstractEntityException;
use Stopsopa\LiteSerializer\Libs\AbstractEntity;
use Proxies\__CG__\LiteSerializer\Entities\Group as CgGroup;

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

        $dumper = DumperTry1::getInstance();

        $dump = $dumper->dump($u);

        $this->assertSame('{"id":1,"group1":"group2","group2":"missing","level":0,"scope":null}', json_encode($dump));

        $this->assertSame(0, AbstractEntity::get($dumper, 'level'));
    }
    public function testNested() {

        $u = $this->getSet1();

        $dumper = DumperTry2::getInstance();

        $dump = $dumper->dump($u);

        $this->assertSame('{"groups":[{"id":1,"name":"group1"},{"id":3,"name":"group3"}],"name":"user1"}', json_encode($dump));

        $this->assertSame(0, AbstractEntity::get($dumper, 'level'));
    }
    public function testDate() {

        $u = $this->getSetDate();

        $dumper = DumperTry2::getInstance();

        $dump = $dumper->dump($u);

        $this->assertSame('{"groups":[{"id":1,"name":"2016-07-07 09:04:03"},{"id":3,"name":"group3"}],"name":"user1"}', json_encode($dump));

        $this->assertSame(0, AbstractEntity::get($dumper, 'level'));
    }
    public function testScope() {

        $u = $this->getSetDate();

        $dumper = DumperTry2::getInstance();

        $dump = $dumper->dumpScope($u, 'noname');

        $this->assertSame('{"groups":[{"id":1},{"id":3}]}', json_encode($dump));

        $this->assertSame(0, AbstractEntity::get($dumper, 'level'));
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

        $dumper = DumperInterface::getInstance();

        $dump = $dumper->dumpScope($u, 'noname');

        $this->assertSame('{"groups":[{"id":1},{"id":2},{"id":3}],"comments":["first comment","second comment","third comment","noname",1]}', json_encode($dump));

        $this->assertSame(0, AbstractEntity::get($dumper, 'level'));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 2
     * @expectedExceptionMessage Dumping entity of class 'Stopsopa\LiteSerializer\Entities\Group' is not handled by dumper 'Stopsopa\LiteSerializer\Dumpers\DumperClassNotSupported', this entity should implement interface 'Stopsopa\LiteSerializer\DumpToArrayInterface' or add method 'Stopsopa\LiteSerializer\Dumpers\DumperClassNotSupported->dumpStopsopaLiteSerializerEntities_Group($entity)' to dumper
     */
    public function testClassNotSupported() {

        $dumper = DumperClassNotSupported::getInstance();

        $dumper->dump($this->getSetDate());

        $this->assertSame(0, AbstractEntity::get($dumper, 'level'));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 3
     * @expectedExceptionMessage Entity 'Stopsopa\LiteSerializer\Entities\User' is not foreachable
     */
    public function testDumpMode() {

        $dumper = DumperInterface::getInstance();

        $dumper->dumpMode($this->getSetDate(), Dumper::MODE_COLLECTION);

        $this->assertSame(0, AbstractEntity::get($dumper, 'level'));
    }

    public function testWrongKey() {

        $dumper = DumperWrongKey::getInstance();

        $data = $this->getSetDate();

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

        $this->assertSame(0, AbstractEntity::get($dumper, 'level'));
    }
    public function testTransform() {

        $dumper = DumperTransform::getInstance();

        $u = $this->getSetDate();

        $dump = $dumper->dump($u);

        $this->assertSame('{"groups":[{"id":1,"name":"this should be name not date"},{"id":2,"name":"group2"},{"id":3,"name":"group3"}],"comments":null,"name":"user1"}', json_encode($dump));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 2
     * @expectedExceptionMessage Dumping entity of class 'LiteSerializer\Entities\Group' is not handled by dumper 'Stopsopa\LiteSerializer\Dumpers\DumperTransform', this entity should implement interface 'Stopsopa\LiteSerializer\DumpToArrayInterface' or add method 'Stopsopa\LiteSerializer\Dumpers\DumperTransform->dumpLiteSerializerEntities_Group($entity)' to dumper
     */
    public function testCg() {

        $dumper = DumperTransform::getInstance();

        $u = $this->getSetDate();

        require_once dirname(__FILE__).'/Entities/__CG__/Group.php';

        $u->setGroups(array(new CgGroup()));

        $dump = $dumper->dump($u);

        $this->assertSame('tes', $dump);
    }
    public function testStack() {

        $dumper = DumperStack::getInstance();

        $u1 = $this->getSetDate();

        $u2 = $this->getSetDate();

        $u1->addGroup($u2);

        $this->assertSame($this->stackData(), json_encode($dumper->dump($u1)));
    }
    protected function stackData() {
        ob_start();
?>
{
    "s": [
        "Stopsopa\\LiteSerializer\\Entities\\User"
    ],
    "groups": [
        {
            "s": [
                "Stopsopa\\LiteSerializer\\Entities\\User",
                "groups",
                "array",
                0,
                "Stopsopa\\LiteSerializer\\Entities\\Group"
            ]
        },
        {
            "s": [
                "Stopsopa\\LiteSerializer\\Entities\\User",
                "groups",
                "array",
                1,
                "Stopsopa\\LiteSerializer\\Entities\\Group"
            ]
        },
        {
            "s": [
                "Stopsopa\\LiteSerializer\\Entities\\User",
                "groups",
                "array",
                2,
                "Stopsopa\\LiteSerializer\\Entities\\Group"
            ]
        },
        {
            "s": [
                "Stopsopa\\LiteSerializer\\Entities\\User",
                "groups",
                "array",
                3,
                "Stopsopa\\LiteSerializer\\Entities\\User"
            ],
            "groups": [
                {
                    "s": [
                        "Stopsopa\\LiteSerializer\\Entities\\User",
                        "groups",
                        "array",
                        3,
                        "Stopsopa\\LiteSerializer\\Entities\\User",
                        "groups",
                        "array",
                        0,
                        "Stopsopa\\LiteSerializer\\Entities\\Group"
                    ]
                },
                {
                    "s": [
                        "Stopsopa\\LiteSerializer\\Entities\\User",
                        "groups",
                        "array",
                        3,
                        "Stopsopa\\LiteSerializer\\Entities\\User",
                        "groups",
                        "array",
                        1,
                        "Stopsopa\\LiteSerializer\\Entities\\Group"
                    ]
                },
                {
                    "s": [
                        "Stopsopa\\LiteSerializer\\Entities\\User",
                        "groups",
                        "array",
                        3,
                        "Stopsopa\\LiteSerializer\\Entities\\User",
                        "groups",
                        "array",
                        2,
                        "Stopsopa\\LiteSerializer\\Entities\\Group"
                    ]
                }
            ]
        }
    ]
}
<?php
        $json = ob_get_clean();

        return json_encode(json_decode($json, true));
    }
}