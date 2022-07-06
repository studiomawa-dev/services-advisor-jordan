<?php

namespace App\Libraries\Clustering;

class Point
{
    protected $id;
    protected $sid;
    protected $x;
    protected $y;

    protected static $last_number = 0;

    /**
     * @var Cluster
     */
    protected $parent_cluster;

    /**
     * @return Cluster
     */
    public function getParentCluster()
    {
        return $this->parent_cluster;
    }

    /**
     * @param Cluster $parent_cluster
     */
    public function setParentCluster(Cluster $parent_cluster)
    {
        $this->parent_cluster = $parent_cluster;
        return $this;
    }

    const SHOULD_BE_INTEGER = 'The input variables should be integer';

    /**
     * Point constructor.
     * @param $x
     * @param $y
     * @throws \Exception
     */
    public function __construct($x, $y, $sid)
    {
        if (!is_numeric($x) || !is_numeric($y) || !is_numeric($sid)) {
            throw new \Exception(static::SHOULD_BE_INTEGER);
        }

        $this->x = $x;
        $this->y = $y;
        $this->sid = $sid;

        $this->id = self::$last_number++;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($name == 'x' || $name == 'y') {
            return $this->$name;
        }
    }

    public function getSid()
    {
        return $this->sid;
    }
}
