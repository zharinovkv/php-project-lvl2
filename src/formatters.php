<?php

namespace Differ\formatters;

function formatters($format)
{
    $allow = in_array($format, ['pretty', 'plain', 'json']);

    switch ($allow) {
        case true:
            return "\Differ\Formatters\\{$format}\\format";
        default:
            throw new \Exception("Formatter \"{$format}\" not supported.");
    }
}
