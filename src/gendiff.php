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
    $content = \Differ\differ\genDiff($result->args["<firstFile>"], $result->args["<secondFile>"]);
    $diff = \Differ\differ\parser($content);
    echo $diff;
}
