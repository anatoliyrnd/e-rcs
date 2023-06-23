<?php
namespace database;
final class SingletonLib {

    /**
     * @var array<class-string, object>
     */
    private static array $singleton_array = [];

    /**
     * @param string $classname
     *
     * @return object
     *
     * @template       TInit as object
     * @phpstan-param  class-string<TInit> $classname
     * @phpstan-return TInit
     */
    public static function init($classname) {
        if (!isset(SingletonLib::$singleton_array[$classname])) {
            SingletonLib::$singleton_array[$classname] = new $classname();
        }

        /* @phpstan-ignore-next-line | static magic */
        return SingletonLib::$singleton_array[$classname];
    }

    /**
     * @param string $classname
     *
     * @return object
     *
     * @template       TInit as object
     * @phpstan-param  class-string<TInit> $classname
     * @phpstan-return TInit|null
     */
    public static function get($classname) {
        if (!isset(SingletonLib::$singleton_array[$classname])) {
            return null;
        }

        /* @phpstan-ignore-next-line | static magic */
        return SingletonLib::$singleton_array[$classname];
    }

    /**
     * @return array<class-string, object>
     */
    public static function getAll() {
        return SingletonLib::$singleton_array;
    }
}