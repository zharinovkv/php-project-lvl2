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

function createItem($type, $name, $beforeValue = null, $afterValue = null, $children = null)
{
    return [
        PROPS['type'] => $type, PROPS['name'] => $name,
        PROPS['beforeValue'] => json_encode($beforeValue), PROPS['afterValue'] => json_encode($afterValue),
        PROPS['children'] => $children];
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

    $mapper = function ($key) use ($before, $after) {

        if (isset($before[$key]) && !isset($after[$key])) {
            return [
                PROPS['type'] => TYPES['removed'], PROPS['name'] => $key,
                PROPS['beforeValue'] => json_encode($before[$key]), PROPS['afterValue'] => null];
        }
        
        if (!isset($before[$key]) && isset($after[$key])) {
            return [
                PROPS['type'] => TYPES['added'], PROPS['name'] => $key,
                PROPS['beforeValue'] => null, PROPS['afterValue'] => json_encode($after[$key])];
        }

        if(isset($before[$key]) && isset($after[$key])) {

            if (is_array($before[$key]) && is_array($after[$key])) {
                
                return [
                    PROPS['type'] => TYPES['nested'], PROPS['name'] => $key,
                    PROPS['beforeValue'] => null, PROPS['afterValue'] => null,                
                    PROPS['children'] => getInnerRepresentation($before[$key], $after[$key])];
            } else {
                if ($before[$key] === $after[$key]) {
                    return [
                        PROPS['type'] => TYPES['unchanged'], PROPS['name'] => $key,
                        PROPS['beforeValue'] => json_encode($before[$key]), PROPS['afterValue'] => json_encode($after[$key])];
                }
        
                if ($before[$key] !== $after[$key]) {
                    return [
                        PROPS['type'] => TYPES['changed'], PROPS['name'] => $key,
                        PROPS['beforeValue'] => json_encode($before[$key]), PROPS['afterValue'] => json_encode($after[$key])];
                }
            }
        }
    };

    $mapped = array_map($mapper, $keys);    
    print_r($mapped);

    return [];
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
