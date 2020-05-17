<?php

namespace Differ\gendiff;

use Docopt;

use function Differ\differ\genDiff;

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

    $result = Docopt::handle($doc, array('version' => '0.0.1'));
    $diff = genDiff($result->args["<firstFile>"], $result->args["<secondFile>"], $result->args["--format"]);
    echo $diff;
    echo PHP_EOL;
}
