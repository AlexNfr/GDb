<?php

/**
 * Class Autoloader - автозагрузчик классов
 *
 * @version 0.1.2016.10.19  - первоначальная версия (включает обработку namespace)
 * @author  AlexNfr
 */

class Autoloader
{
    /**
     * @var array
     */
    protected static $paths = [
        'models/',
        'view/',
        'controllers/',
        'libs/',
    ];

    /**
     * @var bool
     */
    protected static $caseSensitivity = true;


    public static function register($loader)
    {
        spl_autoload_register($loader);
    }

    /**
     * @param $path
     */
    public static function addPath($path)
    {
        self::$paths[] = $path;
    }

    /**
     * @param boolean   $caseSensitivity
     */
    public static function setCaseSensitivity($caseSensitivity)
    {
        self::$caseSensitivity = $caseSensitivity;
    }

    /**
     * @param $className
     */
    public function loadClass($className){
        if (!self::$caseSensitivity) {
            $className = strtolower($className);
        }
        // преобразуем namespaces (если есть) в path
        $className = str_replace('\\', '/', $className, $slashCnt);
        if ($slashCnt) {
            // если в имени класса указан namespace
            require_once("../{$className}.php");
        } else {            //
            // если в имя класса не содержит namespace
            foreach (self::$paths as $dir) {
                $filename = "../{$dir}{$className}.php";
                if (file_exists($filename)) {
                    require_once($filename);
                    return;
                }
            }
        }
    }

}