<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit41409f72151aabafeb286c8ff9067297
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'Psr\\Http\\Message\\' => 17,
        ),
        'I' => 
        array (
            'IgnicoWordPress\\Api\\' => 20,
        ),
        'F' => 
        array (
            'Fig\\Http\\Message\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'IgnicoWordPress\\Api\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc/api',
        ),
        'Fig\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/fig/http-message-util/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit41409f72151aabafeb286c8ff9067297::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit41409f72151aabafeb286c8ff9067297::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
