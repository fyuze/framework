<?php
namespace Fyuze\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Stream implements StreamInterface
{
    /**
     * @var resource
     */
    protected $stream;

    /**
     * {@inheritdoc}
     *
     * @var  array
     * @link http://php.net/manual/function.fopen.php
     */
    protected static $modes = [
        'readable' => ['r', 'r+', 'w+', 'a+', 'x+', 'c+'],
        'writable' => ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'],
    ];

    /**
     * @param $stream
     * @param $mode
     */
    public function __construct($stream, $mode = 'r')
    {
        $resource = is_string($stream) ? fopen($stream, $mode) : $stream;

        $this->stream = $this->attach($resource);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function close()
    {
        if (false === is_resource($this->stream)) {
            return;
        }

        fclose($this->detach());
    }

    /**
     * @param $resource
     * @return mixed
     */
    public function attach($resource)
    {
        if (false === is_resource($resource)) {
            throw new InvalidArgumentException(
                sprintf('You can only attach a resource or stream string, %s provided', gettype($resource))
            );
        }

        return $this->stream = $resource;
    }

    /**
     * {@inheritdoc}
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $stream = $this->stream;
        $this->stream = null;
        return $stream;
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if (null === $this->stream) {
            return null;
        }

        return fstat($this->stream)['size'];
    }

    /**
     * {@inheritdoc}
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        if (!$this->stream) {
            throw new RuntimeException('No stream available, cannot get current position.');
        }

        return ftell($this->stream);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function eof()
    {
        return is_resource($this->stream) ? feof($this->stream) : true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isSeekable()
    {
        return is_resource($this->stream) ? $this->getMetadata('seekable') : false;
    }

    /**
     * {@inheritdoc}
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @return bool
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->isSeekable()) {
            throw new RuntimeException('The current stream is not seekable.');
        }

        fseek($this->stream, $offset, $whence);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        return $this->seek(0);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isWritable()
    {
        foreach (static::$modes['writable'] as $mode) {
            if (strpos($this->getMetadata('mode'), $mode) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if (false === $this->isWritable()) {
            throw new RuntimeException('The current stream is not writable.');
        }

        // @todo find out how to make fwrite fail
        if (false === $bytes = fwrite($this->stream, $string)) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('Error triggered when attempting to write to stream');
            // @codeCoverageIgnoreEnd
        }

        return $bytes;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isReadable()
    {
        foreach (static::$modes['readable'] as $mode) {
            if (strpos($this->getMetadata('mode'), $mode) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        if (false === $this->isReadable()) {
            throw new RuntimeException('The current stream is not readable.');
        }

        // @todo find out how to make fread fail
        if (false === $content = fread($this->stream, $length)) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('An error occurred while reading stream.');
            // @codeCoverageIgnoreEnd
        }

        return $content;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        if (false === $this->isReadable()) {
            throw new RuntimeException('The current stream is not readable.');
        }

        if (false === $contents = stream_get_contents($this->stream)) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('An error occurred while getting stream contents.');
            // @codeCoverageIgnoreEnd
        }
        return $contents;
    }

    /**
     * {@inheritdoc}
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        $meta = stream_get_meta_data($this->stream);

        if ($key === null) {
            return $meta;
        }

        if (array_key_exists($key, $meta)) {
            return $meta[$key];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (RuntimeException $e) {
            return '';
        }
    }
}
