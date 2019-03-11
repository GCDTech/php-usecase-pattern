<?php

namespace Gcd\UseCases;

use Psr\Container\ContainerInterface;

abstract class UseCase
{
    public function __construct()
    {
    }

    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * Set the DI Container to allow `create` to use DI
     *
     * @see create()
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }

    private static $mocks = [];

    /**
     * Receives a Stub class to return when a use case needs to be created.
     *
     * Note this should be the result of a Codeception Stub::make() call.
     *
     * @param $useCase
     */
    final public static function setMockUseCase($useCase)
    {
        self::$mocks[$useCase->__mocked] = $useCase;
    }

    /**
     * Remove any attached mocks
     */
    final public static function clearMockUseCases()
    {
        self::$mocks = [];
    }

    /**
     * @param array $arguments
     * @return static|UseCase
     * @throws \ReflectionException
     */
    final public static function create(...$arguments)
    {
        // If we have an attached mock use case - return it instead of making a new use case.
        if (isset(self::$mocks[static::class])) {
            return self::$mocks[static::class];
        }

        $reflection = new \ReflectionMethod(static::class, '__construct');
        $params = $reflection->getParameters();

        $paramArgs = [];

        foreach ($params as $param) {
            $dependencyClass = $param->getClass();
            if ($dependencyClass == null) {
                // End of the type hinted arguments
                break;
            }

            $dependencyClassName = $dependencyClass->getName();

            if (count($arguments) > 0 && is_object($arguments[0]) && $arguments[0] instanceof $dependencyClassName) {
                $dependency = $arguments[0];
                array_splice($arguments, 0, 1);
            } else {
                if (self::$container){
                    $container = self::$container;
                    $dependency = $container->get($dependencyClassName);
                } else {
                    $dependency = new $dependencyClassName();
                }
            }

            $paramArgs[] = $dependency;
        }

        $paramArgs = array_merge($paramArgs, $arguments);

        return new static(...$paramArgs);
    }
}