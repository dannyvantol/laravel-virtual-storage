<?php

declare(strict_types=1);

namespace HalloDanny\Filesystem;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Illuminate\Support\ServiceProvider;


class VirtualFilesystemServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('vfs', static function ($app, $config) {
            $client = new VirtualFilesystemAdapter($config);
            return new Filesystem($client);
        });
    }
}
