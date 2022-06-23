<?php

declare(strict_types=1);

namespace HalloDanny\Filesystem;

use Illuminate\Support\Str;
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
        $this->dirname = Str::random(8);
        $this->vfsStreamDirectory = vfsStream::setup($this->dirname, 0755, []);
        parent::__construct($this->vfsStreamDirectory->url(), null, LOCK_SH);
    }

    final public function getBasePath(): string
    {
        return $this->vfsStreamDirectory->url();
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
