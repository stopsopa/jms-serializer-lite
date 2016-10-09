
# AbstractEntity    

    
 &nbsp; &nbsp; AbstractEntity is another standalone library inspired by [Twig for Template Designers](http://twig.sensiolabs.org/doc/templates.html). 
    
 
    
 &nbsp; &nbsp; Main goal of AbstractEntity is to simplify access to data in nested structure of entities using simple path where each level is separated by dot. 
 
 Simple example:
 

```php
    use Stopsopa\LiteSerializer\Libs\AbstractEntity;
    
    $c = new stdClass();
    $c->four = array(
        'five' => 'six',
        'seven' => array(
            'eight', 'nine'
        )
    );
    
    $k = array(
        'one' => 'two',
        'three' => $c
    );
    

    var_dump(AbstractEntity::get($k, 'three.four.seven.1') === 'nine');
    # return 'true'
    
    var_dump(json_encode(AbstractEntity::get($k, 'three.four.seven')));
    # return string '["eight","nine"]' (length=16)  
``` 

## Private properties

access to private/protected properties of object ...


```php
    class Foo {
        protected $bar = 'barval';
        protected $noaccess = 'naval';
        public function getBar() {
            return $this->bar . '-getBar';
        }
    }
    
    $k = array(
        'one' => 'two',
        'three' => new Foo()
    );
    
    var_dump(AbstractEntity::get($k, 'three.bar') === 'barval-getBar');
    # return 'true'

    var_dump(AbstractEntity::get($k, 'three.noaccess'));
    # return string 'naval' (length=5)
    
    AbstractEntity::get($k, 'three.barw');
    # throw exception like below  
```


![barwF](https://cloud.githubusercontent.com/assets/3743506/19213371/5c17d904-8d6b-11e6-880d-3f7714e332a7.jpg)

## Default value    
 

 &nbsp; &nbsp; By default method 'get' return value (whatever it is), but if path is wrong it throws AbstractEntityException. It is possible to disable throwing exception by specifying third argument in 'get' method. From now on if 'get' method will be not able to get data pointed by path it will return value from third argument instead of throwing exception. Last example with default value now will return ...
 
```php
    var_dump(AbstractEntity::get($k, 'three.barw', "can't find"));
    # return string 'can't find' (length=10) 
```
 
 
 
 
 
 
 