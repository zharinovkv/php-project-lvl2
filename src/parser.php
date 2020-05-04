<?php

namespace Differ\parser;

use Symfony\Component\Yaml\Yaml;

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

function createItem($type, $key, $beforeValue = null, $afterValue = null, $children = null)
{
    $item = [
        PROPS['type'] => $type,
        PROPS['name'] => $key,
        PROPS['beforeValue'] => $beforeValue,
        PROPS['afterValue'] => $afterValue,
        PROPS['children'] => $children,
    ];
    return $item;
}

function getAst($before, $after)
{
    $keys = array_keys(array_merge(get_object_vars($before), get_object_vars($after)));

    $mapper = function ($key) use ($before, $after) {

        if (property_exists($before, $key) && property_exists($after, $key)) {
            if (is_object($before->$key) && is_object($after->$key)) {
                return createItem(TYPES['nested'], $key, $children = getAst($before->$key, $after->$key));
            } else {
                if ($before->$key === $after->$key) {
                    return createItem(TYPES['unchanged'], $key, $beforeValue = $before->$key, $afterValue = $after->$key);
                } elseif ($before->$key !== $after->$key) {
                    return createItem(TYPES['changed'], $key, $beforeValue = $before->$key, $afterValue = $after->$key);
                }
            }
        } elseif (!property_exists($after, $key)) {
            return createItem(TYPES['removed'], $key, $beforeValue = $before->$key);
        } elseif (!property_exists($before, $key)) {
            return createItem(TYPES['added'], $key, $beforeValue = null, $afterValue = $after->$key);
        }
    };

    $mapped = array_map($mapper, $keys);
    return $mapped;
}

function toString($ast)
{
    $types = [
        TYPES['unchanged'] => function ($item) {
            $value = gettype(true) == "boolean" ? json_encode($item[PROPS['beforeValue']]) : $item[PROPS['beforeValue']];
            return "  {$item[PROPS['name']]}: {$value}";
        },
        TYPES['changed'] => function ($item) {
            $before = gettype(true) == "boolean" ? json_encode($item[PROPS['beforeValue']]) : $item[PROPS['beforeValue']];
            $after = gettype(true) == "boolean" ? json_encode($item[PROPS['afterValue']]) : $item[PROPS['afterValue']];
            return  "+ {$item[PROPS['name']]}: {$after}\n- {$item[PROPS['name']]}: {$before}";
        },
        TYPES['removed'] => function ($item) {
            $value = gettype(true) == "boolean" ? json_encode($item[PROPS['beforeValue']]) : $item[PROPS['beforeValue']];
            return "- {$item[PROPS['name']]}: {$value}";
        },
        TYPES['added'] => function ($item) {
            $value = gettype(true) == "boolean" ? json_encode($item[PROPS['afterValue']]) : $item[PROPS['afterValue']];
            return "+ {$item[PROPS['name']]}: {$value}";
        },
        TYPES['nested'] => function ($item) {
            return [$item[PROPS['name']] => toString($item[PROPS['children']])];
        },
    ];

    $mapper = function ($child) use ($types) {
        return $types[$child[PROPS['type']]]($child);
    };
    $result = array_map($mapper, $ast);
    return $result;
}


function toString2($ast)
{
    $types = [
        TYPES['unchanged'] => function ($item) {
            $value = gettype(true) == "boolean" ? json_encode($item[PROPS['beforeValue']]) : $item[PROPS['beforeValue']];
            return "  {$item[PROPS['name']]}: {$value}";
        },
        TYPES['changed'] => function ($item) {

            $before = gettype(true) == "boolean" ? json_encode($item[PROPS['beforeValue']]) : $item[PROPS['beforeValue']];
            $after = gettype(true) == "boolean" ? json_encode($item[PROPS['afterValue']]) : $item[PROPS['afterValue']];

            return  "+ {$item[PROPS['name']]}: {$after}\n- {$item[PROPS['name']]}: {$before}";
        },
        TYPES['removed'] => function ($item) {
            $value = gettype(true) == "boolean" ? json_encode($item[PROPS['beforeValue']]) : $item[PROPS['beforeValue']];
            return "- {$item[PROPS['name']]}: {$value}";
        },
        TYPES['added'] => function ($item) {
            $value = gettype(true) == "boolean" ? json_encode($item[PROPS['afterValue']]) : $item[PROPS['afterValue']];
            return "+ {$item[PROPS['name']]}: {$value}";
        },
        TYPES['nested'] => function ($item) {
            return [$item[PROPS['name']] => toString($item[PROPS['children']])];
        },
    ];

    $mapper = function ($acc, $child) use ($types, &$mapper) {

        $item = $types[$child[PROPS['type']]]($child);
        return "{$acc}{$item}\n";
    };

    $result = array_reduce($ast, $mapper, "{\n");

    return $result . "}\n";
}

function render($arr)
{
    $keys = array_keys($arr);

    $mapper = function ($acc, $child) use (&$mapper) {
        $item = str_replace("\"", "", $child);
        return "{$acc}{$item}\n";
    };

    $result = array_reduce($arr, $mapper, "{\n");

    return $result . "}\n";
}
