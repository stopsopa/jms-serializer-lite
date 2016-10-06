<?php

namespace Stopsopa\LiteSerializer\Libs;

use PHPUnit_Framework_TestCase;
use Stopsopa\LiteSerializer\Examples\ExampleArrayAccess;
use Stopsopa\LiteSerializer\Examples\ExampleClass;
use stdClass;

class AbstractEntityTest extends PHPUnit_Framework_TestCase {
    public function testPublic() {

        $c = new ExampleClass();

        $this->assertEquals('pubval', AbstractEntity::get($c, 'pub'));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 4
     * @expectedExceptionMessage Parameter 'attr' is not a string, it is: integer
     */
    public function testWrongAttr() {

        $c = new ExampleClass();

        AbstractEntity::get($c, 5);
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 5
     * @expectedExceptionMessage Parameter 'attr' is empty string
     */
    public function testEmptyAttrObject() {

        $c = new ExampleClass();

        $c->test = 'test';

        $this->assertEquals('test', AbstractEntity::get($c, ''));
    }
    public function testEmptyAttrArray() {

        $c = array(''=>'test');

        $this->assertEquals('test', AbstractEntity::get($c, ''));
    }
    public function testDot() {

        $cls = new stdClass();
        $cls->three = array(
            'four' => 'six'
        );

        $c = array(
            'one' => array(
                'two' => $cls
            )
        );

        $this->assertEquals('six', AbstractEntity::get($c, 'one.two.three.four'));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Stopsopa\LiteSerializer\Libs\AbstractEntity::internalValueByMethodOrAttribute error: Property 'seven' doesn't exist and methods getSeven(), isSeven(), hasSeven(), seven() are not accessible in 'stdClass'
     */
    public function testWrongDot() {

        $cls = new stdClass();
        $cls->three = array(
            'four' => 'six'
        );

        $c = array(
            'one' => array(
                'two' => $cls
            )
        );

        $this->assertEquals('six', AbstractEntity::get($c, 'one.two.seven.four'));
    }
    public function testWrongDotDefault() {

        $cls = new stdClass();
        $cls->three = array(
            'four' => 'six'
        );

        $c = array(
            'one' => array(
                'two' => $cls
            )
        );

        $this->assertEquals('default', AbstractEntity::get($c, 'one.two.seven.four', 'default'));

        $this->assertEquals('default', AbstractEntity::get($c, 'one.two.three.seven', 'default'));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Stopsopa\LiteSerializer\Libs\AbstractEntity::internalValueByMethodOrAttribute error: Property 'two' doesn't exist and methods getTwo(), isTwo(), hasTwo(), two() are not accessible in 'array'
     */
    public function testWrongDotRecursion() {

        $cls = array(
            'one' => 'two'
        );

        AbstractEntity::get($cls, 'two');
    }
    public function testArrayAccess() {

        $c = new ExampleArrayAccess(array(
            'one' => 'two'
        ));

        $this->assertEquals('two', AbstractEntity::get($c, 'one'));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Stopsopa\LiteSerializer\Libs\AbstractEntity::internalValueByMethodOrAttribute error: Property 'three' doesn't exist and methods getThree(), isThree(), hasThree(), three() are not accessible in 'Stopsopa\LiteSerializer\Examples\ExampleArrayAccess'
     */
    public function testArrayAccessWrong() {

        $c = new ExampleArrayAccess(array(
            'one' => 'two'
        ));

        AbstractEntity::get($c, 'three');
    }
    public function testMethod() {

        $a = new ExampleClass();

        $b = new ExampleClass();

        $b->setSomething('test');

        $a->setSomething($b);

        $this->assertEquals('test', AbstractEntity::get($a, 'getSomething().getSomething()'));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 6
     * @expectedExceptionMessage Method getMethodDoesntExist() doesn't exist in class Stopsopa\LiteSerializer\Examples\ExampleClass
     */
    public function testNoMethod() {

        $a = new ExampleClass();

        $b = new ExampleClass();

        $b->setSomething('test');

        $a->setSomething($b);

        $this->assertEquals('test', AbstractEntity::get($a, 'getSomething().getMethodDoesntExist()'));
    }
    public function testDefaultOnObject() {

        $b = new ExampleClass();

        $b->setSomething('test');

        $this->assertEquals('default', AbstractEntity::get($b, 'wrongCall', 'default'));
    }
    public function testDefaultOnObjectWithDot() {

        $a = new ExampleClass();

        $b = new ExampleClass();

        $b->setSomething('test');

        $a->setSomething($b);

        $this->assertEquals('default', AbstractEntity::get($a, 'getSomething().wrongCall', 'default'));
    }
    public function testSlashedDot() {
        $test = array(
            'reach.me' => 'gotya'
        );

        $this->assertEquals('gotya', AbstractEntity::get($test, 'reach\.me'));
    }
    public function testGet() {

        $a = new ExampleClass();

        $this->assertEquals('pubval_get - method get', AbstractEntity::get($a, 'pubGet'));
    }
    public function testIs() {

        $a = new ExampleClass();

        $this->assertEquals('pubval_is - method is', AbstractEntity::get($a, 'pubIs'));
    }
    public function testHas() {

        $a = new ExampleClass();

        $this->assertEquals('pubval_has - method has', AbstractEntity::get($a, 'pubHas'));
    }
    public function testProtected() {

        $a = new ExampleClass();

        $b = new ExampleClass();

        $b->setSomething('test');

        $a->setSomething($b);

        $this->assertEquals('protval', AbstractEntity::get($a, 'something.prot'));
    }
    public function testPrivate() {

        $a = new ExampleClass();

        $b = new ExampleClass();

        $b->setSomething('test');

        $a->setSomething($b);

        $this->assertEquals('priv', AbstractEntity::get($a, 'something.priv'));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Stopsopa\LiteSerializer\Libs\AbstractEntity::internalValueByMethodOrAttribute error: Property 'nonexist' doesn't exist and methods getNonexist(), isNonexist(), hasNonexist(), nonexist() are not accessible in 'Stopsopa\LiteSerializer\Examples\ExampleClass'
     */
    public function testNonExisting() {

        $a = new ExampleClass();

        $b = new ExampleClass();

        $b->setSomething('test');

        $a->setSomething($b);

        $this->assertEquals('priv', AbstractEntity::get($a, 'something.nonexist'));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Stopsopa\LiteSerializer\Libs\AbstractEntity::internalValueByMethodOrAttribute error: Property 'protected' doesn't exist and methods getProtected(), isProtected(), hasProtected(), protected() are not accessible in 'Stopsopa\LiteSerializer\Examples\ExampleClass'
     */
    public function testProtMethod() {

        $a = new ExampleClass();

        $this->assertEquals('', AbstractEntity::get($a, 'protected'));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 6
     * @expectedExceptionMessage Method getProtected_() is not public in class Stopsopa\LiteSerializer\Examples\ExampleClass
     */
    public function testGetProtected_() {

        $a = new ExampleClass();

        $this->assertEquals('', AbstractEntity::get($a, 'getProtected_()'));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Stopsopa\LiteSerializer\Libs\AbstractEntity::internalValueByMethodOrAttribute error: Property 'protected_' doesn't exist and methods getProtected_(), isProtected_(), hasProtected_(), protected_() are not accessible in 'Stopsopa\LiteSerializer\Examples\ExampleClass'
     */
    public function testIsProtected_() {

        $a = new ExampleClass();

        $this->assertEquals('', AbstractEntity::get($a, 'protected_'));
    }
    /**
     * @expectedException Stopsopa\LiteSerializer\Exceptions\AbstractEntityException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Stopsopa\LiteSerializer\Libs\AbstractEntity::internalValueByMethodOrAttribute error: Property 'hasProtected_' doesn't exist and methods getHasProtected_(), isHasProtected_(), hasHasProtected_(), hasProtected_() are not accessible in 'Stopsopa\LiteSerializer\Examples\ExampleClass'
     */
    public function testHasProtected_() {

        $a = new ExampleClass();

        $this->assertEquals('', AbstractEntity::get($a, 'hasProtected_'));
    }
}