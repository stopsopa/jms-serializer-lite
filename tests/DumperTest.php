<?php

namespace Stopsopa\LiteSerializer;
use PHPUnit_Framework_TestCase;
use Stopsopa\LiteSerializer\Dumpers\DumperTry1;
use Stopsopa\LiteSerializer\Dumpers\DumperTry2;
use Stopsopa\LiteSerializer\Examples\ExampleArrayAccess;
use Stopsopa\LiteSerializer\Examples\ExampleClass;
use stdClass;
use Stopsopa\LiteSerializer\Entities\User;
use Stopsopa\LiteSerializer\Entities\Group;

class DumperTest extends PHPUnit_Framework_TestCase {
    /**
     * @param $user
     * @param $groups
     * @return User
     */
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
    public function testUser() {

        $u = $this->getSet1();

        $dump = DumperTry1::getInstance()->dump($u);

        $this->assertSame('{"id":1,"group1":"group2","group2":"missing","level":0,"scope":null}', json_encode($dump));
    }

    /**
     * @group test
     */
    public function testNested() {

        $u = $this->getSet1();

        $dump = DumperTry2::getInstance()->dump($u);

        $this->assertSame('{"id":1,"group1":"group2","group2":"missing","level":0,"scope":null}', json_encode($dump));
    }
}