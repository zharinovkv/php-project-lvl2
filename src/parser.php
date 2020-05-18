<?php

namespace Differ\parser;

use Symfony\Component\Yaml\Yaml;

use const Differ\settings\TYPES;
use const Differ\settings\PROPS;
use const Differ\settings\KEYS;

function getPathsToFiles(...$paths)
{
    $mapper = function ($path) {
        $pathToFile = !(bool) substr_count($path, '/') ? $path = "./{$path}" : $path;

        if (!file_exists($pathToFile)) {
            throw new \Exception("По указанному пути файл {$pathToFile} не существует.");
        }
        return $pathToFile;
    };

    $pathsToFiles = array_map($mapper, $paths);
    return array_combine(KEYS, $pathsToFiles);
}

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

function createNode($type, $key, $beforeValue, $afterValue, $depth, $children)
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

function getAst($before, $after, $depth = 1)
{
    $keys = array_keys(array_merge(get_object_vars($before), get_object_vars($after)));

    $mapper = function ($key) use ($before, $after, $depth) {
        if (property_exists($before, $key) && property_exists($after, $key)) {
            if (is_object($before->$key) && is_object($after->$key)) {
                $item = createNode(
                    TYPES['nested'],
                    $key,
                    null,
                    null,
                    $depth,
                    getAst($before->$key, $after->$key, $depth + 1, $key)
                );
                return $item;
            } else {
                if ($before->$key === $after->$key) {
                    return createNode(TYPES['unchanged'], $key, $before->$key, $after->$key, $depth, null);
                } elseif ($before->$key !== $after->$key) {
                    return createNode(TYPES['changed'], $key, $before->$key, $after->$key, $depth, null);
                }
            }
        } elseif (!property_exists($after, $key)) {
            return createNode(TYPES['removed'], $key, $before->$key, null, $depth, null);
        } elseif (!property_exists($before, $key)) {
            return createNode(TYPES['added'], $key, null, $after->$key, $depth, null);
        }
    };

    $mapped = array_map($mapper, $keys);
    return $mapped;
}
