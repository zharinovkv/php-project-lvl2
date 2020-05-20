<?php

namespace Differ\settings;

const KEYS = ['path_before' => 'path_before', 'path_after' => 'path_after'];

const ASSETS = 'assets/';

const SPACE = '    ';

const PLUSE = '+';

const MINUS = '-';

const BLANK = ' ';

const TYPES = [
    'unchanged' => 'unchanged', 'changed' => 'changed',
    'removed' => 'removed', 'added' => 'added',
    'nested' => 'nested'
];

const PROPS = [
    'type' => 'type', 'name' => 'name',
    'beforeValue' => 'before', 'afterValue' => 'after',
    'children' => 'children', 'depth' => 'depth'
];
