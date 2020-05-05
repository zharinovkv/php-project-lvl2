<?php

namespace Differ\parser;

use Symfony\Component\Yaml\Yaml;
use function Funct\Collection\flatten;

const KEYS = ['path_before' => 'path_before', 'path_after' => 'path_after'];
const TYPES = [
    'unchanged' => 'unchanged', 'changed' => 'changed',
    'removed' => 'removed', 'added' => 'added',
    'nested' => 'nested'
];
const PROPS = [
    'type' => 'type', 'name' => 'name',
    'beforeValue' => 'before', 'afterValue' => 'after',
    'children' => 'children'
];

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

function createItem($type, $key, $beforeValue, $afterValue, $children = null)
{
    $item = [
        PROPS['type'] => $type,
        PROPS['name'] => $key,
        PROPS['beforeValue'] => $beforeValue,
        PROPS['afterValue'] => $afterValue,
    ];
    return $item;
}

function getAst($before, $after)
{
    $keys = array_keys(array_merge(get_object_vars($before), get_object_vars($after)));

    $mapper = function ($key) use ($before, $after) {
        if (property_exists($before, $key) && property_exists($after, $key)) {
            if (is_object($before->$key) && is_object($after->$key)) {
                $item = createItem(TYPES['nested'], $key, null, null);
                $item[PROPS['children']] = getAst($before->$key, $after->$key);
                return $item;
            } else {
                if ($before->$key === $after->$key) {
                    return createItem(TYPES['unchanged'], $key, $before->$key, $after->$key);
                } elseif ($before->$key !== $after->$key) {
                    return createItem(TYPES['changed'], $key, $before->$key, $after->$key);
                }
            }
        } elseif (!property_exists($after, $key)) {
            return createItem(TYPES['removed'], $key, $before->$key, null);
        } elseif (!property_exists($before, $key)) {
            return createItem(TYPES['added'], $key, null, $after->$key);
        }
    };

    $mapped = array_map($mapper, $keys);
    return $mapped;
}

function toDiff($ast)
{
    $types = [
        TYPES['unchanged'] => function ($item) {
            $value = $item[PROPS['beforeValue']];
            return "  {$item[PROPS['name']]}: {$value}";
        },
        TYPES['changed'] => function ($item) {
            $before = $item[PROPS['beforeValue']];
            $after = $item[PROPS['afterValue']];
            $item[PROPS['name']] = ["+ {$item[PROPS['name']]}: {$after}\n", "- {$item[PROPS['name']]}: {$before}\n"];
            return $item[PROPS['name']];
        },
        TYPES['removed'] => function ($item) {
            $value =  $item[PROPS['beforeValue']];
            if (is_object($value)) {
                $value = get_object_vars($value);
                $firstKey = array_key_first($value);
                $v = $value[$firstKey];
                $value = "{\n        {$firstKey}: {$v}}";
            }
            return "- {$item[PROPS['name']]}: {$value}";
        },
        TYPES['added'] => function ($item) {
            $value = $item[PROPS['afterValue']];
            if (is_object($value)) {
                $value = get_object_vars($value);
                $firstKey = array_key_first($value);
                $v = $value[$firstKey];
                $value = "{\n        {$firstKey}: {$v}}";
            }
            if(is_bool($value)) {
                $value = json_encode($value);
            }
            return "+ {$item[PROPS['name']]}: {$value}";
        },
        TYPES['nested'] => function ($item) {
            return [$item[PROPS['name']] => toDiff($item[PROPS['children']])];
        },
    ];

    $mapper = function ($acc, $child) use ($types) {
        $item = $types[$child[PROPS['type']]]($child);
        if (is_string($item)) {
            return "{$acc}{$item}\n";
        } else {
            $key = array_key_first($item);
            $flattened = flatten($item);
            $joined = join("", $flattened);
            return $key === 0 ? "{$acc}{$joined}" : "{$acc}{$key}{$joined}"; 
        }
    };
    $result = array_reduce($ast, $mapper, '');
    return "{\n{$result}}\n";
}
