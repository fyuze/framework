<?php
/**
 * Created by PhpStorm.
 * User: matthew
 * Date: 12/1/15
 * Time: 11:40 AM
 */

namespace Fyuze\File\Iterators;


class ExtensionIterator extends \FilterIterator
{
    /**
     * @var string
     */
    protected $extension;

    /**
     * ExtensionIterator constructor.
     * @param \Iterator $iterator
     * @param $extension
     */
    public function __construct($iterator, $extension)
    {
        parent::__construct($iterator);
        $this->extension = $extension;

    }

    /**
     * @return boolean
     */
    public function accept()
    {
        return $this->getInnerIterator()->current()->getExtension() === $this->extension;
    }
}
