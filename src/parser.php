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

function getBeforeAndAfter($content)
{
    return [$content[KEYS['path_before']], $content[KEYS['path_after']]];
}

function getAst($before, $after)
{
    $keys = array_keys(array_merge(get_object_vars($before), get_object_vars($after)));

    $mapper = function ($key) use ($before, $after) {

        if (property_exists($before, $key) && property_exists($after, $key)) {
            if (is_object($before->$key) && is_object($after->$key)) {
                return [
                    PROPS['type'] => TYPES['nested'],
                    PROPS['name'] => $key,
                    PROPS['children'] => getAst($before->$key, $after->$key)
                ];
            } else {
                if ($before->$key === $after->$key) {
                    return  [
                        PROPS['type'] => TYPES['unchanged'],
                        PROPS['name'] => $key,
                        PROPS['beforeValue'] => $before->$key,
                        PROPS['afterValue'] => $after->$key
                    ];
                } elseif ($before->$key !== $after->$key) {
                    return  [
                        PROPS['type'] => TYPES['changed'],
                        PROPS['name'] => $key,
                        PROPS['beforeValue'] => $before->$key,
                        PROPS['afterValue'] => $after->$key
                    ];
                }
            }
        } elseif (property_exists($before, $key) && !property_exists($after, $key)) {
            return  [
                PROPS['type'] => TYPES['removed'],
                PROPS['name'] => $key,
                PROPS['beforeValue'] => $before->$key
            ];
        } elseif (!property_exists($before, $key) && property_exists($after, $key)) {
            return  [
                PROPS['type'] => TYPES['added'],
                PROPS['name'] => $key,
                PROPS['afterValue'] => $after->$key
            ];
        }
    };

    $mapped = array_map($mapper, $keys);
    return $mapped;
}


function toString($ast)
{


    $types = [
        TYPES['unchanged'] => function ($value) {
            return "  {$value[PROPS['name']]}: {$value[PROPS['beforeValue']]}";
        },
        TYPES['changed'] => function ($value) {
            return  "+ {$value[PROPS['name']]}: {$value[PROPS['afterValue']]}\n" .
                "- {$value['name']}: {$value[PROPS['beforeValue']]}";
        },
        TYPES['removed'] => function ($value) {
            $item = json_encode($value[PROPS['beforeValue']]);
            return "- {$value[PROPS['name']]}: {$item}";
        },
        TYPES['added'] => function ($value) {
            $item = json_encode($value[PROPS['afterValue']]);
            return "+ {$value[PROPS['name']]}: {$item}";
        },
        TYPES['nested'] => function ($value) {
            //print_r($value);

            return [
                $value[PROPS['name']] => toString($value[PROPS['children']])
            ];
        },
    ];

    $mapper = function ($acc, $child) use (&$mapper, $types) {
        $acc[] = $types[$child[PROPS['type']]]($child);
        return $acc;
    };

    $result = array_reduce($ast, $mapper, []);

    print_r($result);

    //return "{\n" . join("\n", $result) . "\n}\n";
}
