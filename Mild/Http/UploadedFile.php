<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Http;

use RuntimeException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    /**
     * @var string
     */
    protected $tmp;
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
    protected $extension;
    /**
     * @var bool
     */
    protected $moved = false;
    /**
     * @var string
     */
    protected $clientFileName;
    /**
     * @var string
     */
    protected $clientMediaType;

    /**
     * UploadedFile constructor.
     * @param $tmp
     * @param null $clientFileName
     * @param null $clientMediaType
     * @param int $size
     * @param int $error
     */
    public function __construct($tmp, $clientFileName = null, $clientMediaType = null, $size = 0, $error = 0)
    {
        $this->tmp = $tmp;
        $this->size = $size;
        $this->error = $error;
        $this->clientFileName = $clientFileName;
        $this->clientMediaType = $clientMediaType;
        $this->extension = substr($clientFileName, strpos($clientFileName, '.') + 1);
    }

    /**
     * @return StreamInterface
     */
    public function getStream()
    {
        if ($this->error !== 0) {
            throw new RuntimeException('The file '.$this->tmp.' is not valid to uploaded.');
        }
        if ($this->moved) {
            throw new RuntimeException('Uploaded file already moved');
        }
        return new Stream(fopen($this->tmp, 'w'));
    }

    /**
     * @return bool
     */
    public function isMoved()
    {
        return $this->moved;
    }

    /**
     * @param string $targetPath
     */
    public function moveTo($targetPath)
    {
        if ($this->error !== 0) {
            throw new RuntimeException('The file '.$this->tmp.' is not valid to uploaded.');
        }
        if ($this->moved) {
            throw new RuntimeException('Uploaded file already moved');
        }
        $targetIsStream = strpos($targetPath, '://') !== false;
        if (!$targetIsStream && !is_writable(dirname($targetPath))) {
            throw new RuntimeException('The target path is not writable');
        }
        if ($targetIsStream) {
            $this->moved = copy($this->tmp, $targetPath);
            if (! unlink($this->tmp)) {
                throw new RuntimeException('The file could not be removing.');
            }
        } else {
            $this->moved = php_sapi_name() === 'cli' ? rename($this->tmp, $targetPath) : move_uploaded_file($this->tmp, $targetPath);
        }
        if ($this->moved === false) {
            throw new RuntimeException('could not moved to file to '.$targetPath.'');
        }
    }

    /**
     * @return int|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string|null
     */
    public function getClientFilename()
    {
        return $this->clientFileName;
    }

    /**
     * @return string|null
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }
}