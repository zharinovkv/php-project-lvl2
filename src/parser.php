<?php
namespace Differ\parser;

use Symfony\Component\Yaml\Yaml;

use const Differ\settings\TYPES;
use const Differ\settings\PROPS;
use const Differ\settings\KEYS;

function getContent($paths)
{
    $json = function ($path) {
        return json_decode(file_get_contents($path), false);
    };

    $yaml = function ($path) {
        return Yaml::parseFile($path, Yaml::PARSE_OBJECT_FOR_MAP);
    };

    $ext = strtolower(pathinfo($paths[KEYS['path_before']], PATHINFO_EXTENSION));

    return array_map(${$ext}, $paths);
}

function splitOnBeforeAndAfter($content)
{
    return [$content[KEYS['path_before']], $content[KEYS['path_after']]];
}

function createItem($type, $key, $beforeValue, $afterValue, $depth, $children)
{
    $item = [
        PROPS['type'] => $type,
        PROPS['name'] => $key,
        PROPS['beforeValue'] => $beforeValue,
        PROPS['afterValue'] => $afterValue,
        PROPS['depth'] => $depth,
        PROPS['children'] => $children
    ];
    return $item;
}

function getAst($before, $after, $depth)
{
    $keys = array_keys(array_merge(get_object_vars($before), get_object_vars($after)));

    $mapper = function ($key) use ($before, $after, $depth) {
        if (property_exists($before, $key) && property_exists($after, $key)) {
            if (is_object($before->$key) && is_object($after->$key)) {
                $item = createItem(TYPES['nested'], $key, null, null, $depth, getAst($before->$key, $after->$key, $depth + 1));
                return $item;
            } else {
                if ($before->$key === $after->$key) {
                    return createItem(TYPES['unchanged'], $key, $before->$key, $after->$key, $depth, null);
                } elseif ($before->$key !== $after->$key) {
                    return createItem(TYPES['changed'], $key, $before->$key, $after->$key, $depth, null);
                }
            }
        } elseif (!property_exists($after, $key)) {
            return createItem(TYPES['removed'], $key, $before->$key, null, $depth, null);
        } elseif (!property_exists($before, $key)) {
            return createItem(TYPES['added'], $key, null, $after->$key, $depth, null);
        }
    };

    $mapped = array_map($mapper, $keys);
    return $mapped;
}
