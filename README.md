[![Build Status](https://travis-ci.org/stopsopa/jms-serializer-lite.svg?branch=master)](https://travis-ci.org/stopsopa/jms-serializer-lite)
[![Coverage Status](https://coveralls.io/repos/github/stopsopa/jms-serializer-lite/badge.svg)](https://coveralls.io/github/stopsopa/jms-serializer-lite)


[![Latest Stable Version](https://poser.pugx.org/stopsopa/jms-serializer-lite/v/stable)](https://packagist.org/packages/stopsopa/jms-serializer-lite)

Why it was built?
===

Usually first choice of library to dump data from db to provide any RESTful feeds is [jms/serializer](http://jmsyst.com/libs/serializer). This tool is designed to serialize and unserialize data to xml, json, or yml and back to initial data structures. But usually there is need to just provide data into one direction - to json feeds. Additionally usually there is need to serialize the same object in different ways. In jms/serializer and similar complex tools usually you can use "groups", unfortunately this solution is not flexible enough to deal with real life situations.
 
So what does this library do? 

This library gives you ability to serialize any nested data structures (usually ORM objects) to any array structure, ready to json_encode in simplest possible way, without loosing flexibility and without loosing inheritance to provide new serialization format by changing old format. This library is also framework agnostic.

