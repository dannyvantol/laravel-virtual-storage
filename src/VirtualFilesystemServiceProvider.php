<?php

declare(strict_types=1);

namespace HalloDanny\Filesystem;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class VirtualFilesystemServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('vfs', static function ($app, $config) {
            $adapter = new VirtualFilesystemAdapter($config);
            $config['root'] = $adapter->getBasePath();

            $driver = new Filesystem($adapter, $config);
            return new FilesystemAdapter($driver, $adapter, $config);
        });
    }
}
