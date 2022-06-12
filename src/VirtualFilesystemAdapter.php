<?php

declare(strict_types=1);

namespace HalloDanny\Filesystem;

use InvalidArgumentException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use SplFileInfo;

class VirtualFilesystemAdapter extends LocalFilesystemAdapter
{
    protected string $dirname;

    protected vfsStreamDirectory $vfsStreamDirectory;

    public function __construct(array $config = [])
    {
        if (!array_key_exists('dir_name', $config)) {
            throw new InvalidArgumentException('Missing \'dirname\' key in $config');
        }

        $this->dirname = $config['dir_name'];
        $this->vfsStreamDirectory = vfsStream::setup($this->dirname, 0755, []);
        parent::__construct($this->vfsStreamDirectory->url(), null, LOCK_SH);
    }

    protected function ensureDirectoryExists(string $dirname, int $visibility): void
    {
        if ($dirname === $this->dirname) {
            return;
        }
        parent::ensureDirectoryExists($dirname, $visibility);
    }

    protected function deleteFileInfoObject(SplFileInfo $file): bool
    {
        if ($file->getType() === 'dir') {
            return @rmdir($file->getPathname());
        }
        return @unlink($file->getPathname());
    }
}
