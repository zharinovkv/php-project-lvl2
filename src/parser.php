<?php

namespace Differ\parser;

use Symfony\Component\Yaml\Yaml;

const KEYS = ['path_before', 'path_after'];

function parser($paths)
{
    $json = function ($path) {
        return json_decode(file_get_contents($path), true);
    };

    $yaml = function ($path) {
        return Yaml::parseFile($path/* , Yaml::PARSE_OBJECT_FOR_MAP */);
    };

    $ext = strtolower(pathinfo($paths[KEYS[0]], PATHINFO_EXTENSION));

    return array_map(${$ext}, $paths);
}

function toAst($content)
{
    [$before, $after] = [(array) getBeforeAfter($content, KEYS[0]), (array) getBeforeAfter($content, KEYS[1])];

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
        return ['type' => 'unchanged', 'name' => $key, 'oldValue' => $value, 'newValue' => $value];
    }, array_KEYS($result), $result);

    return $unchanged;
}

function changed($before, $after)
{
    $changedItems = function ($my_array, $allowed) {
        $filtered = array_filter(
            $my_array,
            function ($val, $key) use ($allowed) {
                return isset($allowed[$key]) && ($allowed[$key] === true || $allowed[$key] !== $val);
            },
            ARRAY_FILTER_USE_BOTH
        );
        return $filtered;
    };

    $filtered1 = $changedItems($before, $after);
    $filtered2 = $changedItems($after, $before);
    $merged = array_merge_recursive($filtered1, $filtered2);

    $result2 = array_map(function ($key, $value) {
        return ['type' => 'changed', 'name' => $key, 'oldValue' => $value[0], 'newValue' => $value[1]];
    }, array_KEYS($merged), $merged);

    return $result2;
}

function removed($before, $after)
{
    $result = array_diff_key($before, $after);

    $result2 = array_map(function ($key, $value) {
        return ['type' => 'removed', 'name' => $key, 'oldValue' => $value, 'newValue' => ''];
    }, array_KEYS($result), $result);

    return $result2;
}

function added($before, $after)
{
    $result = array_diff_key($after, $before);

    $result2 = array_map(function ($key, $value) {
        return ['type' => 'added', 'name' => $key, 'oldValue' => '', 'newValue' => json_encode($value)];
    }, array_KEYS($result), $result);

    return $result2;
}

function getBeforeAfter($content, $index)
{
    return $content[$index];
}

function render($ast)
{
    //var_dump($ast);

    $types = [
        'unchanged' => function ($value) {
            return "  {$value['name']}: {$value['oldValue']}";
        },
        'changed' => function ($value) {
            return "+ {$value['name']}: {$value['newValue']}\n- {$value['name']}: {$value['oldValue']}";
        },
        'removed' => function ($value) {
            return "- {$value['name']}: {$value['oldValue']}";
        },
        'added' => function ($value) {
            return "+ {$value['name']}: {$value['newValue']}";
        },
        'nested' => function ($value) {
            return 'nested';
        },
    ];

    $result = array_map(function ($value) use ($types) {
        return $types[$value['type']]($value);
    }, $ast);

    return "{\n" . join("\n", $result) . "\n}\n";
}
