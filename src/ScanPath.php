<?php

namespace Rfc\Scannable;

use Attribute;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ScanPath extends ScanFile
{
    public function __construct(
        protected RecursiveDirectoryIterator|array|string $path = []
    )
    {
        parent::__construct(
            file: $this->setPath($path)->getPathFiles(),
        );
    }

    /**
     * @return SplFileInfo[]
     */
    public function getPathFiles(): array
    {
        return array_reduce($this->getPath(), function (array $files, RecursiveDirectoryIterator $path) {
            foreach (new RecursiveIteratorIterator($path) as $file) {
                if ($file->isDir()) {
                    continue;
                }
                $files[] = $file;
            }
            return $files;
        }, []);
    }

    /**
     * @return RecursiveDirectoryIterator[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * @param array|string|RecursiveDirectoryIterator $path
     * @return static
     */
    public function setPath(array|string|RecursiveDirectoryIterator $path): static
    {
        $this->path = array_reduce(is_array($path) ? $path : [$path], function (array $paths, mixed $path) {
            foreach (is_array($path) ? $path : [$path] as $directory) {
                if ($directory instanceof RecursiveDirectoryIterator) {
                    $directory = $directory->getRealPath();
                }
                if (is_dir($directory)) {
                    $paths[] = new RecursiveDirectoryIterator($directory);
                }
            }
            return $paths;
        }, []);
        return $this;
    }

}
