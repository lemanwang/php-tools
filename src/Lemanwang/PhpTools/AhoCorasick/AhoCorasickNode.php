<?php

namespace Lemanwang\PhpTools\AhoCorasick;

class AhoCorasickNode
{
    public $children = [];
    public $fail = null;
    public $output = [];
}