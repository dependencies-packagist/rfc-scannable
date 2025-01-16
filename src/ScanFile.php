<?php

namespace Rfc\Scannable;

use Attribute;
use SplFileInfo;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ScanFile extends ScanNamespace
{
    public function __construct(
        protected SplFileInfo|array|string $file = []
    )
    {
        parent::__construct(
            namespace: $this->setFile($file)->getFileNamespace(),
        );
    }

    /**
     * @return array<string,string>
     */
    public function getFileNamespace(): array
    {
        $classMap = $this->getClassLoader()->getClassMap();
        $classMap = array_map(function ($className) {
            return realpath($className);
        }, $classMap);
        return array_reduce($this->getFile(), function (array $namespaces, SplFileInfo $file) use ($classMap) {
            if ($namespace = array_search($file->getRealPath(), $classMap)) {
                $namespaces[$file->getRealPath()] = $namespace;
            }
            return $namespaces;
        }, []);
    }

    /**
     * @return SplFileInfo[]
     */
    public function getFile(): array
    {
        return $this->file;
    }

    /**
     * @param SplFileInfo|array|string $file
     * @return static
     */
    public function setFile(SplFileInfo|array|string $file): static
    {
        $this->file = array_reduce(is_array($file) ? $file : [$file], function (array $files, mixed $file) {
            if ($file instanceof SplFileInfo) {
                $file = $file->getRealPath();
            }
            if (is_file($file)) {
                $files[] = new SplFileInfo($file);
            }
            return $files;
        }, []);
        return $this;
    }

}
