<?php

use Fyuze\File\Cabinet;
use PHPUnit\Framework\TestCase;

class FileCabinetTest extends TestCase
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var Fyuze\File\Cabinet
     */
    protected $cabinet;

    public function setUp()
    {
        $this->path = __DIR__ . '/mock';
    }

    public function testAllFilesAreFoundFromAppMocks()
    {
        $cabinet = (new Cabinet())
            ->in($this->path);

        $filesToAssert = ['bar.php', 'baz.bat', 'foo'];

        $idx = 0;
        foreach ($cabinet as $source) {
            $this->assertEquals($filesToAssert[$idx], $source->getBaseName());
            $idx++;
        }
    }

    public function testOnlyFilesAreFoundWhenSpecified()
    {
        $cabinet = (new Cabinet())
            ->only('files')
            ->in($this->path);

        $filesToAssert = ['bar.php', 'baz.bat'];

        $idx = 0;
        foreach ($cabinet as $source) {
            $this->assertEquals($filesToAssert[$idx], $source->getBaseName());
            $idx++;
        }
    }

    public function testOnlyFoldersAreFoundWhenSpecified()
    {
        $cabinet = (new Cabinet())
            ->only('folders')
            ->in($this->path);

	foreach ($cabinet as $source) {

	    $this->assertEquals('foo', $source->getBaseName());
        }

    }

    public function testSearchFiltersApplyAsExpected()
    {
        $cabinet = (new Cabinet())
            ->search('.php')
            ->only('files')
            ->in($this->path);

        $filesToAssert = ['bar.php', 'baz.bat'];

        $idx = 0;
        foreach ($cabinet as $source) {
            $this->assertEquals($filesToAssert[$idx], $source->getBaseName());
            $idx++;
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFilterThrowsExceptionOnInvalidRules()
    {
        $cabinet = (new Cabinet())
            ->only('foobar')
            ->in($this->path);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testExceptionIsThrownOnInvalidDirectory()
    {
        $cabinet = (new Cabinet())
            ->in('foobar');
    }
}
