<?php

namespace Differ\gendiff;

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

    $result = Docopt::handle($doc, array('version' => '0.0.1'));
    $diff = \Differ\differ\genDiff($result->args["<firstFile>"], $result->args["<secondFile>"]);
    echo $diff;
}
