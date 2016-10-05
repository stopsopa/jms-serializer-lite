<?php

namespace Stopsopa\LiteSerializer\Libs;

use Stopsopa\LiteSerializer\Exceptions\AbstractEntityException;
use ArrayAccess;
use ReflectionException;
use ReflectionProperty;
use ReflectionMethod;
use Traversable;

abstract class AbstractEntity {

    const MARKER = '__CG__';

    /**
     * @param object $entity
     * @param string|array $attr - Key like 'author.id'
     * @param $default - If you provide this parameter that is optional then this method don't throw
     *                   an exception but return this value
     * @return mixed
     * @throws AbstractEntityException
     */
    public static function get($entity, $attr) {

        if (!is_string($attr)) {
            throw new AbstractEntityException("Parameter 'attr' is not a string, it is: ".  gettype($attr), AbstractEntityException::ATTR_IS_NOT_STRING);
        }

        $args = func_get_args();

        if (strpos($attr, '.') !== false) {

            $attr = trim($attr);

            $exp = static::cascadeExplode($attr);

            if ( count($exp) > 1 ) {

                foreach ($exp as $k) {

                    $args[0] = $entity;
                    $args[1] = $k;

                    $entity = call_user_func_array(
                        array(__CLASS__, 'internalValueByMethodOrAttribute'),
                        $args
                    );
                }

                return $entity;
            }

            $args[1] = $exp[0];
        }

        return call_user_func_array(array(__CLASS__, 'internalValueByMethodOrAttribute'), $args);
    }
    protected static function internalValueByMethodOrAttribute($entity, $attr) {

        $args = func_get_args();

        $isdefault = (count($args) > 2);

        $short = ucfirst($attr);

        if (is_array($entity) && array_key_exists($attr, $entity)) {
            return $entity[$attr];
        }

        if (is_object($entity)) {

            if ($entity instanceof ArrayAccess) {
                if ($entity->offsetExists($attr)) {
                    return $entity[$attr];
                }
            }

            if ($attr === '') {
                throw new AbstractEntityException("Parameter 'attr' is empty string: ".json_encode($attr), AbstractEntityException::ATTR_IS_EMPTY_STRING);
            }

            if (strpos($attr, '()') !== false) {

                $method = rtrim($attr, '()');

                $access = false;

                if (method_exists($entity, $method)) {

                    $access = true;

                    $reflection = new ReflectionMethod($entity, $method);

                    if ($reflection->isPublic()) {
                        return $entity->$method();
                    }
                }

                $class = self::getClass($entity);

                throw new AbstractEntityException(
                    sprintf("Method %s() %s in class %s",
                        $method,
                        $access ? "is not public" : "doesn't exist",
                        $class
                    ),
                    AbstractEntityException::DIRECTLY_CALLED_METHOD_DOESNT_EXIST
                );
            }
            else {

                $short = ucfirst($short);

                $method = "get$short";
                if (method_exists($entity, $method)) {

                    $reflection = new ReflectionMethod($entity, $method);

                    if ($reflection->isPublic()) {
                        return $entity->$method();
                    }
                }

                $method = "is$short";
                if (method_exists($entity, $method)) {

                    $reflection = new ReflectionMethod($entity, $method);

                    if ($reflection->isPublic()) {
                        return $entity->$method();
                    }
                }

                $method = "has$short";
                if (method_exists($entity, $method)) {

                    $reflection = new ReflectionMethod($entity, $method);

                    if ($reflection->isPublic()) {
                        return $entity->$method();
                    }
                }

                if (isset($entity->$attr)) {
                    return $entity->$attr;
                }

                try {
                    $rp = new ReflectionProperty($entity, $attr);
                    $rp->setAccessible(true);

                    return $rp->getValue($entity);
                }
                catch (ReflectionException $e) {
                }
            }
        }

        if ($isdefault) {
            return $args[2];
        }

        $class = self::getClass($entity);

        throw new AbstractEntityException(
            __METHOD__." error: Property '$attr' doesn't exist and methods get$short(), is$short(), has$short(), $attr() are not accessible in '$class'",
            AbstractEntityException::WRONG_KEY
        );
    }
    public static function cascadeExplode($key)
    {
        $key = preg_split("#(?<!\\\\)\.#", $key);

        foreach ($key as $k => &$d) {
            $d = str_replace('\\.', '.', $d);
        }

        return $key;
    }
    public static function getClass($obj) {

        if (is_object($obj)) {
            return static::cleanClassNamespace(get_class($obj));
        }

        return gettype($obj);
    }
    /**
     * Unfortunately Doctrine generates different namespaces for proxy classes, like:
     *      Proxies\__CG__\Site\CMS\ArticleBundle\Entity\Article
     */
    public static function cleanClassNamespace($classNamespace) {

        if ( strpos( $classNamespace, "\\" . preg_quote(static::MARKER) . "\\" ) ) {
            $classNamespace = preg_replace('#^[^\\\\]+\\\\[^\\\\]+\\\\(.*)$#', '$1', $classNamespace);
        }

        return $classNamespace;
    }
    protected function getMethodName($object)
    {
        $parts = explode('\\', static::getClass($object));

        $last = array_pop($parts);

        $parts = implode('', $parts);

        return 'dump'.ucfirst("{$parts}_$last");
    }
    public static function isForeachable($object) {
        return is_array($object) || ( is_object($object) && $object instanceof Traversable);
    }
}