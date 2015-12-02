<?php
/**
 * Created by PhpStorm.
 * User: matthew
 * Date: 12/1/15
 * Time: 11:40 AM
 */

namespace Fyuze\File\Iterators;


class FolderIterator extends \FilterIterator
{
    public function accept()
    {
        return $this->getInnerIterator()->current()->isDir();
    }
}
