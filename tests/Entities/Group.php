<?php

namespace Stopsopa\LiteSerializer\Entities;

class Group {
    protected $id;
    protected $name;
    protected $nested;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Group
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Group
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNested()
    {
        return $this->nested;
    }

    /**
     * @param mixed $nested
     * @return Group
     */
    public function setNested($nested)
    {
        $this->nested = $nested;
        return $this;
    }
}