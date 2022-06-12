<?php

declare(strict_types=1);

namespace Tests\HalloDanny\Filesystem;

use HalloDanny\Filesystem\VirtualFilesystemAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use PHPUnit\Framework\TestCase;

class VirtualFilesystemAdapterTest extends TestCase
{
    protected Filesystem $filesystem;

    public function setUp(): void
    {
        $adapter = new VirtualFilesystemAdapter(['dir_name' => 'root']);
        $this->filesystem = new Filesystem($adapter);
    }

    /**
     * @throws FilesystemException
     */
    public function testWriteFileExpectFileExists(): void
    {
        $this->createDefaultFile();
        $this->assertTrue($this->filesystem->fileExists('foo.txt'));
    }

    /**
     * @throws FilesystemException
     */
    public function testFileContentExpectBar(): void
    {
        $this->createDefaultFile();
        $contents = $this->filesystem->read('foo.txt');

        $this->assertEquals('bar', $contents);
    }

    /**
     * @throws FilesystemException
     */
    public function testFileReadStreamExpectResource(): void
    {
        $this->createDefaultFile();
        $this->assertIsResource($this->filesystem->readStream('foo.txt'));
    }

    /**
     * @throws FilesystemException
     */
    public function testFileWriteStreamExpectContentBar(): void
    {
        $resource = tmpfile();
        fwrite($resource, 'bar');
        $this->filesystem->writeStream('foo.txt', $resource);

        $this->assertEquals('bar', $this->filesystem->read('foo.txt'));
    }

    /**
     * @throws FilesystemException
     */
    public function testFileCanBeCopied(): void
    {
        $this->createDefaultFile();
        $this->filesystem->copy('foo.txt', 'foo/bar.txt');

        $this->assertTrue($this->filesystem->has('foo/bar.txt'));
        $this->assertEquals('bar', $this->filesystem->read('foo/bar.txt'));
    }

    /**
     * @throws FilesystemException
     */
    public function testFileLastModified(): void
    {
        $this->createDefaultFile();

        $this->assertNotNull($this->filesystem->lastModified('foo.txt'));
    }

    /**
     * @throws FilesystemException
     */
    public function testFileMimeType(): void
    {
        $this->createDefaultFile();

        $this->assertEquals('text/plain', $this->filesystem->mimeType('foo.txt'));
    }

    /**
     * @throws FilesystemException
     */
    public function testFileVisibility(): void
    {
        $this->createDefaultFile();

        $this->assertEquals('public', $this->filesystem->visibility('foo.txt'));
    }

    /**
     * @throws FilesystemException
     */
    public function testFileSize(): void
    {
        $this->createDefaultFile();

        $this->assertGreaterThan(0, $this->filesystem->fileSize('foo.txt'));
    }

    /**
     * @throws FilesystemException
     */
    public function testFileCanBeDeleted(): void
    {
        $this->createDefaultFile();
        $this->filesystem->delete('foo.txt');

        $this->assertFalse($this->filesystem->has('file.txt'));
    }

    /**
     * @throws FilesystemException
     */
    public function testDirectoryWithChildrenCanBeDeleted(): void
    {
        $this->filesystem->createDirectory('foo/bar');
        $this->filesystem->deleteDirectory('foo');

        $this->assertFalse($this->filesystem->directoryExists('foo'));
    }

    /**
     * @throws FilesystemException
     */
    public function testListContents(): void
    {
        $this->createDefaultFile();

        $expected = [
            "type" => "file",
            "path" => "foo.txt",
            "fileSize" => 3,
        ];
        $actual = $this->filesystem->listContents('')->toArray()[0];

        collect($expected)->each(
            function ($value, $key) use ($actual) {
                $this->assertEquals($value, $actual[$key]);
            }
        );
    }

    /**
     * @throws FilesystemException
     */
    private function createDefaultFile(): void
    {
        $this->filesystem->write('foo.txt', 'bar');
    }
}
