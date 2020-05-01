<?php

namespace Differ\parser;

use Symfony\Component\Yaml\Yaml;
use Funct\Collection\flatten;

const KEYS = ['path_before' => 'path_before', 'path_after' => 'path_after'];
const TYPES = [
    'unchanged' => 'unchanged', 'changed' => 'changed',
    'removed' => 'removed', 'added' => 'added',
    'nested' => 'nested'
];
const PROPS = [
    'type' => 'type', 'name' => 'name',
    'beforeValue' => 'beforeValue', 'afterValue' => 'afterValue',
    'children' => 'children'
];

function getContent($paths)
{
    $json = function ($path) {
        return json_decode(file_get_contents($path), true);
    };

    $yaml = function ($path) {
        return Yaml::parseFile($path, Yaml::PARSE_OBJECT_FOR_MAP);
    };

    $ext = strtolower(pathinfo($paths[KEYS['path_before']], PATHINFO_EXTENSION));

    return array_map(${$ext}, $paths);
}

function getBeforeAndAfter($content)
{
    return [$content[KEYS['path_before']], $content[KEYS['path_after']]];
}

function getInnerRepresentation($before, $after)
{
    //$both_files_properties = array_merge(array_keys($before), array_keys($after));
    //print_r($both_files_properties);
/*     
    print_r(array_merge($before, $after));
    print_r(array_keys(array_merge($before, $after)));
    print_r(array_unique(array_keys(array_merge($before, $after)))); */
    $keys = array_keys(array_merge($before, $after));
    //print_r($keys);
    //print_r($before);
    //print_r($after);    

    $mapped = array_map(function ($key) use ($before, $after) {

        if (isset($before[$key]) && !isset($after[$key])) {
            return [
                PROPS['type'] => TYPES['removed'], PROPS['name'] => $key,
                PROPS['beforeValue'] => $before[$key], PROPS['afterValue'] => ''];
        }
        
        if (!isset($before[$key]) && isset($after[$key])) {
            return [
                PROPS['type'] => TYPES['added'], PROPS['name'] => $key,
                PROPS['beforeValue'] => '', PROPS['afterValue'] => json_encode($after[$key])];
        }

        if(isset($before[$key]) && isset($after[$key])) {

            if (is_array($before[$key]) && is_array($after[$key])) {
                echo  'children';
                return ['children' => getInnerRepresentation($before[$key], $after[$key])];
            } else {
                if ($before[$key] === $after[$key]) {
                    return [
                        PROPS['type'] => TYPES['unchanged'], PROPS['name'] => $key,
                        PROPS['beforeValue'] => $before[$key], PROPS['afterValue'] => $after[$key]];
                }
        
                if ($before[$key] !== $after[$key]) {
                    return [
                        PROPS['type'] => TYPES['changed'], PROPS['name'] => $key,
                        PROPS['beforeValue'] => $before[$key], PROPS['afterValue'] => $after[$key]];
                }
            }
        }


        /*   print_r($before);
        print_r($after);
        print_r($before[$key]);
        print_r($after[$key]);
       */  


    }, $keys);

    $m = $mapped;
    print_r($m);


/*     $unchanged = unchanged($before, $after);
    $changed =  changed($before, $after);
    $removed =  removed($before, $after);
    $added =  added($before, $after);

    $result = array_merge($unchanged, $changed, $removed, $added);
    //print_r($result);

    return $result; */
    return [];
}

function unchanged($before, $after)
{
    $result = array_intersect($before, $after);

    $unchanged = array_map(function ($key, $value) {
        return [
                PROPS['type'] => TYPES['unchanged'],
                PROPS['name'] => $key,
                PROPS['beforeValue'] => $value, 
                PROPS['afterValue'] => $value
            ];
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
        print_r($value);
        return [PROPS['type'] => TYPES['changed'], PROPS['name'] => $key,
            PROPS['beforeValue'] => $value, PROPS['afterValue'] => $value];
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
