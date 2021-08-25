<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf2c382b77db9ce3c897460ef772c5b7a
{
    public static $classMap = array (
        'Ps_Checkpayment' => __DIR__ . '/../..' . '/ps_checkpayment.php',
        'Ps_CheckpaymentPaymentModuleFrontController' => __DIR__ . '/../..' . '/controllers/front/payment.php',
        'Ps_CheckpaymentValidationModuleFrontController' => __DIR__ . '/../..' . '/controllers/front/validation.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitf2c382b77db9ce3c897460ef772c5b7a::$classMap;

        }, null, ClassLoader::class);
    }
}
