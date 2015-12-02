<?php
namespace Fyuze\File;

use Fyuze\File\Iterators\ExtensionIterator;
use GlobIterator;
use AppendIterator;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use IteratorAggregate;
use InvalidArgumentException;
use RuntimeException;

/**
 * Directory handler for fyuze
 *
 * @package Fyuze\File
 */
class Cabinet implements IteratorAggregate
{
    /**
     * @var
     */
    protected $search = [];

    /**
     * @var
     */
    protected $index;

    /**
     * @var
     */
    protected $flag;

    /**
     * @var array
     */
    public $directory = [];

    /**
     * @param $pattern
     * @return $this
     */
    public function search($pattern)
    {
        $this->search[] = $pattern;

        return $this;
    }

    /**
     * @param $flag
     * @return $this
     * @throws InvalidArgumentException
     */
    public function only($flag)
    {
        if ($flag !== 'files' && $flag !== 'folders') {
            throw new InvalidArgumentException(
                sprintf('The flag \'%s\' is not supported.', $flag)
            );
        }

        $this->flag = $flag;

        return $this;
    }

    /**
     * @param $directory
     * @return $this
     * @throws RuntimeException
     */
    public function in($directory)
    {
        if (file_exists($directory) === false || is_dir($directory) === false) {
            throw new RuntimeException(
                sprintf('The file %s either does not exist or is not a directory', $directory)
            );
        }

        $this->directory = new RecursiveDirectoryIterator(
            $directory,
            FilesystemIterator::SKIP_DOTS
        );

        return $this;
    }

    /**
     * @return AppendIterator
     */
    public function getIterator()
    {
        $iterator = new AppendIterator();

        $directoryIterator = $this->createFilter($this->directory);

        foreach ($this->search as $query) {

            $iterator->append(
                new ExtensionIterator(
                    $this->directory,
                    $query
                )
            );
        }

        $iterator->append($directoryIterator);

        return $iterator;
    }

    /**
     * @param $iterator
     * @return mixed
     */
    protected function createFilter($iterator)
    {
        if ($this->flag) {

            /** @var \FilterIterator $class */
            $class = sprintf('Fyuze\File\Iterators\%sIterator',
                ucfirst(str_replace('s', '', $this->flag)));

            return new $class($iterator);
        }

        return $iterator;
    }
}
