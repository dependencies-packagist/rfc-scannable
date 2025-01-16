<?php

namespace Rfc\Scannable;

use Attribute;
use BadMethodCallException;
use Composer\Autoload\ClassLoader;
use Illuminate\Support\Traits\Macroable;
use ReflectionClass;
use Reflective\Reflection\ReflectionParentClass;
use Rfc\Scannable\Contracts\Scannable;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Scan implements Scannable
{
    use Macroable {
        __call as macroCall;
    }

    protected ClassLoader $classLoader;

    public function __construct(
        protected ReflectionClass|array|string $reflectionClass = []
    )
    {
        $this->setReflectionClass($reflectionClass);
    }

    /**
     * @param string|null $name
     * @param int $flags
     * @return array
     * @return array<string,array<string,ReflectionClass>>
     */
    public function getParentsClass(?string $name = null, int $flags = 0): array
    {
        return array_map(function (ReflectionClass $class) use ($name, $flags) {
            return (new ReflectionParentClass($class->getName()))->getParentsClass($name, $flags);
        }, $this->getReflectionClass());
    }

    /**
     * @param string|null $name
     * @param int $flags
     * @return array<string,array<string,ReflectionClass>>
     */
    public function getAttributesClass(?string $name = null, int $flags = 0): array
    {
        return array_map(function (ReflectionClass $class) use ($name, $flags) {
            return $class->getAttributes($name, $flags);
        }, $this->getReflectionClass());
    }

    /**
     * @return array<string,array<string,ReflectionClass>>
     */
    public function getInterfacesClass(): array
    {
        return array_map(function (ReflectionClass $class) {
            return $class->getInterfaces();
        }, $this->getReflectionClass());
    }

    /**
     * @return array<string,ReflectionClass>
     */
    public function getReflectionClass(): array
    {
        return $this->reflectionClass;
    }

    /**
     * @param ReflectionClass|array|string $reflectionClass
     * @return static
     */
    public function setReflectionClass(ReflectionClass|array|string $reflectionClass): static
    {
        $this->reflectionClass = array_reduce(is_array($reflectionClass) ? $reflectionClass : [$reflectionClass], function (array $reflectionClasses, $reflectionClass) {
            if (is_string($reflectionClass) && class_exists($reflectionClass)) {
                $reflectionClass = new ReflectionClass($reflectionClass);
            }
            if ($reflectionClass instanceof ReflectionClass) {
                $reflectionClasses[$reflectionClass->getName()] = $reflectionClass;
            }
            return $reflectionClasses;
        }, []);
        return $this;
    }

    public function getClassLoader(): ClassLoader
    {
        foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $registeredLoader) {
            if (str_starts_with(__DIR__, $vendorDir)) {
                return $this->classLoader = $registeredLoader;
            }
        }
        return new ClassLoader();
    }

    /**
     * Dynamically handle calls the instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }

}
