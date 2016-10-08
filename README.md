[![Build Status](https://travis-ci.org/stopsopa/jms-serializer-lite.svg?branch=master)](https://travis-ci.org/stopsopa/jms-serializer-lite)
[![Coverage Status](https://coveralls.io/repos/github/stopsopa/jms-serializer-lite/badge.svg)](https://coveralls.io/github/stopsopa/jms-serializer-lite)
[![Latest Stable Version](https://poser.pugx.org/stopsopa/jms-serializer-lite/v/stable)](https://packagist.org/packages/stopsopa/jms-serializer-lite)


# Why it was built?

Usually first choice of library to dump data from db to provide any RESTful feeds is [jms/serializer](http://jmsyst.com/libs/serializer). This tool is designed to serialize and unserialize data to xml, json, or yml and back to initial data structures. But usually there is need to just provide data into one direction - to json feeds. Additionally usually there is need to serialize the same object in different ways. In jms/serializer and similar complex tools usually you can use "groups", unfortunately this solution is not flexible enough to deal with real life situations.
 
So what does this library special do? 

This library gives you ability to serialize any nested data structures (usually ORM objects) to any array structure, ready to json_encode in simplest possible way, without loosing flexibility and without loosing inheritance to provide new serialization format by changing old format. This library is also framework agnostic.


# Installation

    composer require stopsopa/jms-serializer-lite
    

# Documentation


When You have ORM entities like ...

    Article:
        id
        title
        content
        comments # <one-to-many with Comment entity>
        
    Comment
        id
        article # <many-to-one with Article entity>
        user # <many-to-one with User entity>
        content
        
    User
        id
        login
        name
        surname
        comments # <one-to-many with Comment entity>
        
... and there is need to serialize **Article** to RESTful feed:

```json        
{
    "id": 1,
    "name": "First article",
    "body": "Content of first article"
}
```    
    
The simplest way to start do that using this library is to create simple class (e.g.) **NewDumper** that extends class **Stopsopa\LiteSerializer\Dumper** ...
    
```php    
<?php

namespace MyProject;

use Stopsopa\LiteSerializer\Dumper;

class NewDumper extends Dumper
{
}
```    
    
... and use it to get array ready to json_encode like ...
    
```php   
$article = $man->find(...);

$array = NewDumper::getInstance()->dump($article);

echo json_encode($array, JSON_PRETTY_PRINT);
```   
    
After executing this you will end up with error ...


![Article exception screen](https://cloud.githubusercontent.com/assets/3743506/19212175/83eb5ac4-8d4c-11e6-8994-fcad60b4fa06.jpg)

... that means that You need to implement method **dumpMyProject_Article** to "explain" new class how to transform entity to flat array ...


```php   
namespace MyProject;

class NewDumper extends Dumper
{
    public function dumpMyProject_Article($entity) {
        return array(
            'id'    => $entity->getId(),
            'name'  => $entity->getTitle(),
            'body'  => $entity->getContent()
        );
    }
}
```   


... now when You run this code again You will have what You need.


## Nested entities


To serialize Article with all it's comments like ...
        
```json  
{
    "id": 1,
    "name": "First article",
    "body": "Content of first article",
    "comments": [
        {
            "id": 2,
            "body": "Content of comment 1"
        },
        {
            "id": 1,
            "body": "Content of comment 2"
        }
    ]
}
```   
    
... simply change method **dumpMyProject_Article** to ...    
        


```php   
namespace MyProject;

class NewDumper extends Dumper
{
    public function dumpMyProject_Article($entity) {
        $data = array(
            'id'        => $entity->getId(),
            'name'      => $entity->getTitle(),
            'body'      => $entity->getContent(),
        );

        $data['comments'] = $this->innerDump($entity->getComments());

        return $data;
    }
}
```    
       
... now when You try to execute it, You will see that **NewDump** require method **dumpMyProject_Comment** ...

![Comment exception screen](https://cloud.githubusercontent.com/assets/3743506/19212164/554c2644-8d4c-11e6-8a1b-3d2f202cd0a0.jpg)



```php   
namespace MyProject;

class NewDumper extends Dumper
{
    ...
    public function dumpMyProject_Comment($entity) {
        return array(
            'id'        => $entity->getId(),
            'body'      => $entity->getContent()
        );
    }
}
```    

... and that's it.

## Serialize from different angles

Worth to mention is fact that class prepared above is ready to serialize classes Article and Comment for different use cases ...


```php 
$dumper = NewDumper::getInstance();

# to serialize single Article entity
$array = $dumper->dump($article); 
# result: {"id":1, ... , "comments":[...]}

# to serialize array/collection of Article entities
$array = $dumper->dump(array($article1, $article2, ...));  
# result: [ {"id":1, ... , "comments":[...] }, {"id":2, ... , "comments":[...] } ]

# to serialize single Comment entity
$array = $dumper->dump($comment); 
# result: {"id":1, ...}

# to serialize array/collection of Comment entities
$array = $dumper->dump(array($comment1, $comment2, ...));  
# result: [ {"id":1, ... }, {"id":2, ... } ]        
    
```   
 
 
## Shorter syntax and helpers


All logic explained above can be much more condensed using helpers:


```php
class NewDumper extends Dumper
{
    public function dumpMyProject_Article($entity) {
        return $this->toArray($entity, array(
            'id'        => 'id',
            'name'      => 'title',
            'body'      => 'content',
            'comments'  => 'comments'
        ));
    }
    public function dumpMyProject_Comment($entity) {
        return $this->toArray($entity, array(
            'id'        => 'id',
            'body'      => 'content'
        ));
    }
}   
``` 
    
... let's hold for a minute at this code, and try to understand what's going on here.    

 &nbsp; &nbsp; So "key side" of array (the left side with '**id**', '**name**', '**body**', '**comments**') is side where we declare keys for result array - that's obvious. 
 
  &nbsp; &nbsp; Right side (the value side of array with '**id**', '**title**', '**content**', '**comments**') is little more sophisticated. This values are passed to [AbstractEntity->get()](https://github.com/stopsopa/jms-serializer-lite/blob/master/src/Libs/AbstractEntity.php#L24) method, these are "paths" describing how to get values for these keys. See more about [AbstractEntity](https://github.com/stopsopa/jms-serializer-lite/blob/master/doc/AbstractEntity.md) in another page.

&nbsp;
 
 
>**Note**: 
>
>It is good idea to read chapter about AbstractEntity before continue reading this documentation.

&nbsp;
 
 
#### Default values
 
 You can also specify default value to prevent throwing AbstractEntityException if path is wrong (wrong i mean if leads to nowhere). But to do that on the right hand side you need to use extended version of options ...
  
```php

public function dumpMyProject_Article($entity) {
    return $this->toArray($entity, array(
        'id'        => 'id',
        'name'      => array(
            'path'      => 'title',
            'default'   => 'defaultname'            
        ),
    ));
}  
```  

... so from now on if in object Article field 'title' will be **missing** you won't see Exception but method will return (like nothing happened) value 'defaultname'. 

When i say "**missing**" it means:

 - **if** $article is an array **OR** if object implements interface ArrayAccess
 
    - **if** 'title' is valid key return value
    
 - **if** $article is an object
 
    - **if** path have postfix '()' look for public method 'title()' explicitely and try to execute it and return value        
    
    - **else** throw AbstractEntityException
        
    - **if** there is public method 'getTitle' execute it and return value
    
    - **else if** there is public method 'isTitle' execute it and return value
    
    - **else if** there is public method 'hasTitle' execute it and return value
    
    - **else if** object has (public or private) property 'title' then return value of this prop.
     
 - throw AbstractEntityException because path is wrong, leads to nowhere...
    
... if all above can't reach value then "**path is wrong**" and value is **missing** because there is no value under this path. 

&nbsp;

>**Note:** 
>
>Empty string or null value are still valid values it doesn't mean that path is wrong.

&nbsp;

If there is need to replace [false](http://php.net/manual/en/language.types.boolean.php#language.types.boolean.casting) value by something else it should be done like this:


```php
class NewDumper extends Dumper
{
    public function dumpMyProject_Article($entity) {
        $data = $this->toArray($entity, array(
            'id'        => 'id',
            'name'      => array('title', null), 
                # if path 'title' is wrong then 
                # return null instead of throw AbstractEntityException
            'body'      => 'content',
            'comments'  => 'comments'
        ));

        if (!$data['name']) {
            $data['name'] = 'default value if here is something false';
        }

        return $data;
    }
}
```

#### Excluding/Omitting entities

Sometimes we don't want to have some particular entities in feed. To skip them just throw DumperContinueException: 

```php

use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

class NewDumper extends Dumper
{
    public function dumpMyProject_Comment($entity) {
    
        if (!$entity->isModerated()) {
            throw new DumperContinueException();
        }
        
        return $this->toArray($entity, array(
            'id'        => 'id',
            'body'      => 'content'
        ));
    }
}   
```

#### Save keys

By default dumper don't maintains key association during iteration:

```php

namespace MyProject;

use Stopsopa\LiteSerializer\Dumper;
use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

class Group {
    protected $id;
    protected $name;
    public static function getInstance() { return new self(); }
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; return $this; }
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; return $this; }
}
# extend just to make this example shorter
# the case is that we have one-to-many relation
class User extends Group {
    protected $groups = array();
    public function getGroups() { return $this->groups; }
    public function setGroups($groups) { $this->groups = $groups; return $this; }
}

$user = new User();
$user->setGroups(array(
    'group-1' => Group::getInstance()->setId(10)->setName('gr 1'),
    'group-2' => Group::getInstance()->setId(11)->setName('gr 2'),
    'group-3' => Group::getInstance()->setId(12)->setName('gr 3'),
));

class NewDumper extends Dumper
{
    public function dumpMyProject_User($entity) {
        return $this->toArray($entity, array(
            'id'        => 'id',
            'name'      => 'name',
            'groups'    => 'groups'
        ));
    }
    public function dumpMyProject_Group($entity) {
        if ($entity->getName() === 'gr 2') {
            throw new DumperContinueException();
        }
        return $this->toArray($entity, array(
            'id'        => 'id',
            'name'      => 'name'
        ));
    }
}

echo json_encode(NewDumper::getInstance()->dump($user), JSON_PRETTY_PRINT);
```

... return ...

```json
{
    "id": 50,
    "name": "user",
    "groups": [
        {
            "id": 10,
            "name": "gr 1"
        },
        {
            "id": 12,
            "name": "gr 3"
        }
    ]
}
```

but when you change ...

```php
    ...
    public function dumpMyProject_User($entity) {
        return $this->toArray($entity, array(
            'id'        => 'id',
            'name'      => 'name',
            'groups'    => array(
                'path' => 'groups',
                'savekeys' => true
            )
        ));
    }
    ...
```
 
... keys will be saved ...
 
```json
{
    "id": 50,
    "name": "user",
    "groups": {
        "group-1": {
            "id": 10,
            "name": "gr 1"
        },
        "group-3": {
            "id": 12,
            "name": "gr 3"
        }
    }
}
```

#### DumperInterface

There is special interface Stopsopa\LiteSerializer\DumpToArrayInterface. Implement this interface to declare how to dump entity right in entity itself.
 
#### Default types serialization (integer, string, float, etc.)
 
 Anything else that is not array and is not object is serialized by method dumpPrimitives. You can easily change way of serializing even such values by overriding this method.
 
#### Force mode
 
As you probably noticed everything what is 'foreachable' will be iterated and each element of "collection" will be serialized individually. But there is one situation when this behaviour "can" (don't must) be wrong. When entity implements interface Traversable and in json feed you need fields that are not accessible through 'foreach' on this class. In such situation would be good to have mechanizm to decide manually if serialize such class in normal mode or let dumper to iterate through. You can achieve this like:

```php

    public function dumpMyProject_User($entity) {
        return $this->toArray($entity, array(
            'id'        => 'id',
            'name'      => 'name',
            'order'    => array(
                'path'     => 'order',
                'mode'     => Dumper::MODE_ENTITY # or Dumper::MODE_COLLECTION
                # default is Dumper::MODE_AUTO
            )
        ));
    }
```

#### Scope and stack

As we saw higher in documentation ([link](https://github.com/stopsopa/jms-serializer-lite#serialize-from-different-angles)), it is possible to use one dumper class to dump bunch of entities from different perspective. Doing this though sometimes You might want to change way of working individual dumping methods.
For example if You want to dump article it is good in feed provide also information about user that created this article, but You don't need in this use case all informations about user. But if You will dump User and his all articles it would be good to provide all information about user and less from article. To distinguish these use cases in method itself You can use two private properties available in all methods - scope and stack.
 
Example of using 'stack':
 
 ```php
 ...
    public function dumpMyProject_User($entity) {

        $map = array(
            'id'        => 'id',
            'name'      => 'name'
        );

        if (count($this->stack) < 1) {
            # dump groups of user only if this is feed where
            # user is on higher level of hierarchy
            $map['groups'] = 'groups';
        }

        return $this->toArray($entity, $map);
    }
 ...
 ```
 
 Example of using 'scope':
 
 ```php 
class NewDumper extends Dumper
{
    ...
    public function dumpMyProject_User($entity) {

        $map = array(
            'id'        => 'id',
            'name'      => 'name'
        );

        if ($this->scope === 'dumpalsogroups') {
            $map['groups'] = 'groups';
        }

        return $this->toArray($entity, $map);
    }
}

NewDumper::getInstance()->dumpScope($user, 'dumpalsogroups');
 ```


 
 


 



       
       
