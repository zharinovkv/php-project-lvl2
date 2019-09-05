<?php

namespace php_project_lvl2\gendiff;

use Docopt;

function run()
{
    $doc = <<<'DOCOPT'
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

  Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: pretty]
  
DOCOPT;

    $result = Docopt::handle($doc, array('version' => '1.0.0rc2'));

    foreach ($result as $key => $value) {
        echo $key . ': ' . json_encode($value) . PHP_EOL;
    }
}
