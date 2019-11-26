<?php

namespace Differ\parser;

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

use Symfony\Component\Yaml\Yaml;

function parser()
{
    $value = Yaml::parseFile('assets/before.yaml');
    var_dump($value);
}

// https://github.com/Hexlet/patterns/tree/master/content/factory
