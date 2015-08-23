<?php
namespace Fyuze\Kernel;

abstract class Service
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return mixed
     */
    abstract public function services();
}
