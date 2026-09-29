<?php
$autoload = getenv('KCAT_VENDOR_AUTOLOAD');
if (! $autoload || ! is_file($autoload)) {
    throw new RuntimeException('Set KCAT_VENDOR_AUTOLOAD to the destination Laravel vendor/autoload.php file.');
}
$loader = require $autoload;
$loader->addPsr4('Nsms\\Kcat\\', dirname(__DIR__).'/src');
$loader->addPsr4('KcatTransferTests\\', __DIR__);
