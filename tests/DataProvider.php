<?php

namespace Stopsopa\LiteSerializer;

use Stopsopa\LiteSerializer\Entities\User;
use Stopsopa\LiteSerializer\Entities\Group;
use DateTime;

class DataProvider {
    public static function getUser($user) {

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
    public static function getSet1() {
        return static::getUser(array(
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
                    'name' => 'ignoreme'
                )
            )
        ));
    }
    public static function getSetDate() {
        return static::getUser(array(
            'id' => 1,
            'name' => 'user1',
            'surname' => 'surname1',
            'groups' => array(
                array(
                    'id' => 1,
                    'name' => new DateTime('2016-07-07 09:04:03')
                ),
                array(
                    'id' => 3,
                    'name' => 'ignoreme'
                ),
                array(
                    'id' => 2,
                    'name' => 'group2'
                )
            )
        ));
    }
    public static function stackData() {
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