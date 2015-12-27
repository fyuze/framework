<?php
namespace Fyuze\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class Upload implements UploadedFileInterface
{
    /**
     * @var StreamInterface
     */
    protected $stream;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var int
     */
    protected $error;

    /**
     * @var string
     */
    protected $clientFilename;

    /**
     * @var string
     */
    protected $clientMediaType;

    /**
     * @var bool
     */
    protected $moved = false;

    /**
     * @param $resource
     * @param int $size
     * @param int $error
     * @param null $clientFilename
     * @param null $clientMediaType
     */
    public function __construct(
        $resource,
        $size = 0,
        $error = UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientMediaType = null
    )
    {
        if (is_resource($resource) === false
            && is_string($resource) === false
            && is_object($resource) === false
        ) {
            throw new InvalidArgumentException(
                sprintf('Aruement 1 must be resource or string. %s provided', gettype($resource))
            );
        }

        $this->stream = is_resource($resource) ? new Stream($resource) : $resource;
        $this->size = $size;
        $this->error = $error;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    /**
     * {@inheritdoc}
     *
     * @return StreamInterface Stream representation of the uploaded file.
     * @throws \RuntimeException in cases when no stream is available or can be
     *     created.
     */
    public function getStream()
    {
        if ($this->moved === true) {
            throw new \RuntimeException('File has already been moved to disk.');
        }

        if ($this->stream instanceof StreamInterface) {
            return $this->stream;
        }

        return $this->stream = new Stream($this->stream);
    }

    /**
     * {@inheritdoc}
     *
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     * @param string $targetPath Path to which to move the uploaded file.
     * @throws \InvalidArgumentException if the $path specified is invalid.
     * @throws \RuntimeException on any error during the move operation, or on
     *     the second or subsequent call to the method.
     */
    public function moveTo($targetPath)
    {
        if ($this->moved === true) {
            throw new RuntimeException('This file has already been moved.');
        }

        if (is_writable($targetPath) === false) {
            throw new InvalidArgumentException(
                'Unable to write to target path'
            );
        }

        if (strpos(PHP_SAPI, 'cli') === 0) {

            $stream = new Stream($targetPath, 'wb+');
            $this->getStream()->rewind();
            $stream->write($this->stream->getContents());

        } else {
            // @codeCoverageIgnoreStart
            if (move_uploaded_file($this->stream, $targetPath) === false) {
                throw new RuntimeException('There was a problem moving your uploaded file.');
            }
            // @codeCoverageIgnoreEnd
        }

        $this->moved = true;
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null The filename sent by the client or null if none
     *     was provided.
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null The media type sent by the client or null if none
     *     was provided.
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }
}
