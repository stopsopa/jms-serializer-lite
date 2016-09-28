<?php

namespace Stopsopa\LiteSerializer;

use Exception;
use Stopsopa\LiteSerializer\Exceptions\AbstractEntityException;
use Stopsopa\LiteSerializer\Libs\AbstractEntity;
use Stopsopa\LiteSerializer\Exceptions\DumperContinueException;

/**
 * - Tidy up Exceptions codes
 *  - implement cache form generated methods
 */
abstract class Dumper extends AbstractEntity {

//    const MODE_AUTO         = 1;
//    const MODE_COLLECTION   = 2;
//    const MODE_ENTITY       = 3;
    const MODE_AUTO         = 'aut';
    const MODE_COLLECTION   = 'col';
    const MODE_ENTITY       = 'ent';

    protected $level = 0;
    protected $scope = null;

    public function __construct() {
        $this->level = 0;
    }

    /**
     * @return Dumper
     */
    public static function getInstance() {
        $cls = get_called_class();
        return new $cls;
    }

    /**
     * @param $object
     * @param array $options
     *    scope
     *    mode - (collection|entity|auto) auto = isforeachable ? collection : entity
     * @throws Exception
     */
    public function dump($entity, $mode = null, $scope = null)
    {
        if (!is_null($scope)) {
            $this->scope = $scope;
        }

        if (!is_array($entity) && !is_object($entity)) {

            $this->level += 1;

            $return = $this->dumpPrimitives($entity);

            $this->level -= 1;

            return $return;
        }

        $mode = $mode ? $mode : static::MODE_AUTO;

        $isfo = false;

        if ($mode === static::MODE_AUTO) {

            $isfo = static::isForeachable($entity);

            $mode = $isfo ? static::MODE_COLLECTION : static::MODE_ENTITY;
        }

        if ($mode === static::MODE_COLLECTION) {

            if (!$isfo) {

                $class = static::getClass($entity);

                throw new AbstractEntityException("Entity '$class' is not foreachable", AbstractEntityException::CLASS_NOT_FOREACHABLE);
            }

            $tmp = array();

            $this->level += 1;

            foreach ($entity as &$e) {
                try {
                    $tmp[] = $this->dump($e);
                }
                catch (DumperContinueException $e) {
                }
            }

            $this->level -= 1;

            return $tmp;
        }

        try {

            if ($entity instanceof DumpToArrayInterface) {

                return $entity->dumpToArray($this->scope, $this->level);
            }

            $method = static::getMethodName($entity);

            if (!method_exists($this, $method)) {

                // it would be good to test accessability of this method but i don't do that
                // because of ReflectionMethod bad performance, i assume that because of
                // purpose of this methods they always will be public

                $tclass = static::getClass($this);

                throw new AbstractEntityException(
                    sprintf(
                        "Dumping entity of class '%s' is not handled by dumper '%s', this entity should implement interface '%s' or add method '%s(\$entity, \$scope = null, \$level = 0)' to dumper",
                        static::getClass($entity),
                        $tclass,
//                        'Stopsopa\\LiteSerializer\\DumpToArrayInterface',
                        DumpToArrayInterface::class,
                        $tclass.'->'.$method
                    ),
                    AbstractEntityException::METHOD_NOT_IMPLEMENTED
                );
            }

            $method;
            return $this->{$method}($entity);
        }
        catch (DumperContinueException $e) {
        }
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

            $default = null;

            $mode = static::MODE_AUTO;

            if (is_array($key)) {

                $default    = $key[1];

                if (array_key_exists(2, $key)) {
                    $mode = $key[2];
                }

                $key        = $key[0];
            }

            try {

                $tmp2 = AbstractEntity::get($entity, $key);

                $tmp;

                $tmp[$target] = $this->dump($tmp2, $mode);
            } catch (AbstractEntityException $e) {
//                if () {
//
//                }
                $tmp[$target] = $default;
            }
        }

        return $tmp;
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