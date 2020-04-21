<?php

namespace Differ\parser;

use Symfony\Component\Yaml\Yaml;

const KEYS = ['path_before' => 'path_before', 'path_after' => 'path_after'];
const TYPES = [
    'unchanged' => 'unchanged', 'changed' => 'changed',
    'removed' => 'removed', 'added' => 'added', 'nested' => 'nested'
];
const PROPS = ['type' => 'type', 'name' => 'name', 'beforeValue' => 'beforeValue', 'afterValue' => 'afterValue'];

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

function getInnerRepresentation($content)
{
    [$before, $after] = [ (array)$content[KEYS['path_before']], (array)$content[KEYS['path_after']]];

    $unchanged = unchanged($before, $after);
    $changed =  changed($before, $after);
    $removed =  removed($before, $after);
    $added =  added($before, $after);

    $result = array_merge_recursive($unchanged, $changed, $removed, $added);
    return $result;
}

function unchanged($before, $after)
{
    $result = array_intersect($before, $after);

    $unchanged = array_map(function ($key, $value) {
        return [PROPS['type'] => TYPES['unchanged'], PROPS['name'] => $key,
            PROPS['beforeValue'] => $value, PROPS['afterValue'] => $value];
    }, array_keys($result), $result);

    return $unchanged;
}

function changed($before, $after)
{
    $changedItems = function ($array, $filter) {
        $filtered = array_filter(
            $array,
            function ($val, $key) use ($filter) {
                return isset($filter[$key]) && ($filter[$key] === true || $filter[$key] !== $val);
            },
            ARRAY_FILTER_USE_BOTH
        );
        return $filtered;
    };

    $filtered1 = $changedItems($before, $after);
    $filtered2 = $changedItems($after, $before);
    $merged = array_merge_recursive($filtered1, $filtered2);

    $changed = array_map(function ($key, $value) {
        return [PROPS['type'] => TYPES['changed'], PROPS['name'] => $key,
            PROPS['beforeValue'] => $value[0], PROPS['afterValue'] => $value[1]];
    }, array_keys($merged), $merged);

    return $changed;
}

function removed($before, $after)
{
    $result = array_diff_key($before, $after);

    $removed = array_map(function ($key, $value) {
        return [PROPS['type'] => TYPES['removed'], PROPS['name'] => $key,
            PROPS['beforeValue'] => $value, PROPS['afterValue'] => ''];
    }, array_keys($result), $result);

    return $removed;
}

function added($before, $after)
{
    $result = array_diff_key($after, $before);

    $added = array_map(function ($key, $value) {
        return [PROPS['type'] => TYPES['added'], PROPS['name'] => $key,
            PROPS['beforeValue'] => '', PROPS['afterValue'] => json_encode($value)];
    }, array_keys($result), $result);

    return $added;
}

function toString($ast)
{
    $types = [
        TYPES['unchanged'] => function ($value) {
            return "  {$value[PROPS['name']]}: {$value[PROPS['beforeValue']]}";
        },
        TYPES['changed'] => function ($value) {
            return "+ {$value[PROPS['name']]}: {$value[PROPS['afterValue']]}\n" .
                "- {$value['name']}: {$value[PROPS['beforeValue']]}";
        },
        TYPES['removed'] => function ($value) {
            return "- {$value[PROPS['name']]}: {$value[PROPS['beforeValue']]}";
        },
        TYPES['added'] => function ($value) {
            return "+ {$value[PROPS['name']]}: {$value[PROPS['afterValue']]}";
        },
        TYPES['nested'] => function ($value) {
            return 'nested';
        },
    ];

    $result = array_map(function ($value) use ($types) {
        return $types[$value[PROPS['type']]]($value);
    }, $ast);
    return "{\n" . join("\n", $result) . "\n}\n";
}
