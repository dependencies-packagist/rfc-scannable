<?php

namespace Rfc\Scannable;

use Attribute;
use RecursiveDirectoryIterator;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ScanPackageNamespace extends ScanPath
{
    public function __construct(
        protected array|string $packageNamespace = []
    )
    {
        parent::__construct(
            path: $this->setPackageNamespace($packageNamespace)->getPackageNamespacePath(),
        );
    }

    /**
     * @return array<string,RecursiveDirectoryIterator[]>
     */
    public function getPackageNamespacePath(): array
    {
        $psr4 = $this->getClassLoader()->getPrefixesPsr4();
        return array_reduce($this->getPackageNamespace(), function (array $packageNamespaces, string $packageNamespace) use ($psr4) {
            foreach ($psr4[$packageNamespace] ?? [] as $path) {
                if (!is_dir($path)) {
                    continue;
                }
                if (!array_key_exists($packageNamespace, $packageNamespaces)) {
                    $packageNamespaces[$packageNamespace] = [];
                }
                $packageNamespaces[$packageNamespace][] = new RecursiveDirectoryIterator($path);
            }
            return $packageNamespaces;
        }, []);
    }

    /**
     * @return array
     */
    public function getPackageNamespace(): array
    {
        return $this->packageNamespace;
    }

    /**
     * @param array|string $packageNamespace
     * @return static
     */
    public function setPackageNamespace(array|string $packageNamespace): static
    {
        $this->packageNamespace = array_map(function (string $packageNamespace) {
            return trim($packageNamespace, '\\') . '\\';
        }, is_array($packageNamespace) ? $packageNamespace : [$packageNamespace]);
        return $this;
    }

}
