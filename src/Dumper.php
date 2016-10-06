<?php

namespace Stopsopa\LiteSerializer;

use Exception;
use Stopsopa\LiteSerializer\Exceptions\AbstractEntityException;
use Stopsopa\LiteSerializer\Libs\AbstractEntity;
use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

abstract class Dumper extends AbstractEntity {

    const MODE_AUTO         = 1;
    const MODE_COLLECTION   = 2;
    const MODE_ENTITY       = 3;

    protected $level        = 0;
    protected $scope        = null;
    protected $stack    = array();

    public function __construct() {
        $this->level        = 0;
        $this->stack    = array();
    }

    /**
     * @return Dumper
     */
    public static function getInstance() {
        $cls = get_called_class();
        return new $cls;
    }
    public function dumpMode($entity, $mode) {
        return $this->dump($entity, $mode, $scope = null);
    }
    public function dumpScope($entity, $scope) {
        return $this->dump($entity, $mode = null, $scope);
    }
    public function dump($entity, $mode = null, $scope = null) {

        $this->scope = $scope;

        $this->level = 0;

        $this->stack = array();

        $data = $this->innerDump($entity, $mode);

        if (method_exists($this, 'transform')) {
            return $this->transform($data);
        }

        return $data;
    }

    /**
     * @param $object
     * @param array $options
     *    scope
     *    mode - (collection|entity|auto) auto = isforeachable ? collection : entity
     * @throws Exception
     */
    protected function innerDump($entity, $mode = null)
    {
        $class = static::getClass($entity);

        $this->stack[] = $class;

        if (!is_array($entity) && !is_object($entity)) {

            $this->level += 1;

            $return = $this->dumpPrimitives($entity);

            $this->level -= 1;

            array_pop($this->stack);

            return $return;
        }

        if (!$mode) {
            $mode = static::MODE_AUTO;
        }

        $isfo = false;

        if ($mode === static::MODE_AUTO) {

            if ($entity instanceof DumpToArrayInterface) {

                $this->level += 1;

                $return = $entity->dumpToArray($this->scope, $this->level);

                $this->level -= 1;

                array_pop($this->stack);

                return $return;
            }

            $isfo = static::isForeachable($entity);

            $mode = $isfo ? static::MODE_COLLECTION : static::MODE_ENTITY;
        }

        if ($mode === static::MODE_COLLECTION) {

            if (!$isfo) {

                throw new AbstractEntityException("Entity '$class' is not foreachable", AbstractEntityException::CLASS_NOT_FOREACHABLE);
            }

            $tmp = array();

            $this->level += 1;

            foreach ($entity as $key => &$e) {

                $this->stack[] = $key;

                try {
                    $tmp[] = $this->innerDump($e);
                }
                catch (DumperContinueException $e) {
                    array_pop($this->stack);
                }

                array_pop($this->stack);
            }

            $this->level -= 1;

            array_pop($this->stack);

            return $tmp;
        }

        $method = static::getMethodName($entity);

        if (!method_exists($this, $method)) {

            $tclass = static::getClass($this);

            array_pop($this->stack);

            throw new AbstractEntityException(
                sprintf(
                    "Dumping entity of class '%s' is not handled by dumper '%s', this entity should implement interface '%s' or add method '%s(\$entity)' to dumper",
                    $class,
                    $tclass,
                    'Stopsopa\\LiteSerializer\\DumpToArrayInterface',
                    $tclass.'->'.$method
                ),
                AbstractEntityException::METHOD_NOT_IMPLEMENTED
            );
        }

        // it would be good to test accessability of this method but i don't do that
        // because of ReflectionMethod bad performance, i assume that because of
        // purpose of this methods they always will be public


        $data = $this->{$method}($entity);

        array_pop($this->stack);

        return $data;
    }
    /**
     * Helper method
     * @param object $entity
     * @param array $fields
     *
     * @return array
     */
    public function toArray($entity, $fields)
    {
        $tmp = array();

        foreach ($fields as $target => $key) {

            $this->stack[] = $target;

            $isdefault = false;

            $mode = static::MODE_AUTO;

            if (is_array($key)) {

                $isdefault = true;

                $default    = $key[1];

                if (array_key_exists(2, $key)) {
                    $mode = $key[2];
                }

                $key = $key[0];
            }

            if ($isdefault) {
                $tmp2 = AbstractEntity::get($entity, $key, $default);
            }
            else {
                $tmp2 = AbstractEntity::get($entity, $key);
            }

            $tmp[$target] = $this->innerDump($tmp2, $mode);

            array_pop($this->stack);
        }

        return $tmp;
    }
    protected function helperDefault($key, $default) {
        return array($key, $default);
    }
    protected function helperMode($key, $mode, $default = null) {
        return array($key, $default, $mode);
    }
    protected function dump_DateTime($date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    /**
     * @param mixed $data
     *
     * @return mixed
     */
    protected function dumpPrimitives($data)
    {
        return $data;
    }
}
