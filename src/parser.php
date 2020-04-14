<?php

namespace Differ\parser;

use Symfony\Component\Yaml\Yaml;

function parser($path_before, $path_after)
{
    $extention = pathinfo($path_before, PATHINFO_EXTENSION);
    $content = [];

    if ($extention == 'json') {
        $content['before'] = \Differ\differ\fileGetContents($path_before);
        $content['after'] = \Differ\differ\fileGetContents($path_after);
    } elseif ($extention == 'yaml') {
        $content['before'] = Yaml::parseFile($path_before);
        $content['after'] = Yaml::parseFile($path_after);
    } elseif ($extention == 'ini') {
        // parse = ini.parse;
    }
    return $content;
}