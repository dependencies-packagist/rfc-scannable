<?php

namespace Rfc\Scannable;

use Attribute;
use Illuminate\Support\Str;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ScanNamespace extends Scan
{
    public function __construct(
        protected array|string $namespace = []
    )
    {
        parent::__construct(
            reflectionClass: $this->setNamespace($namespace)->getNamespace(),
        );
    }

    /**
     * @return array
     */
    public function getNamespace(): array
    {
        return $this->namespace;
    }

    /**
     * @param array|string $namespace
     * @return static
     */
    public function setNamespace(array|string $namespace): static
    {
        $this->namespace = array_reduce(is_array($namespace) ? $namespace : [$namespace], function (array $namespaces, $namespace) {
            return collect($this->getClassLoader()->getClassMap())
                ->filter(function ($value, $key) use ($namespace) {
                    return Str::contains($namespace, '*') ? Str::is($namespace, $key) : $key === $namespace;
                })
                ->keys()
                ->merge($namespaces)
                ->all();
        }, []);
        return $this;
    }

}
