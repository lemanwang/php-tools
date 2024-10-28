<?php

namespace Lemanwang\PhpTools\AhoCorasick;

class AhoCorasick
{
    private $root;

    public function __construct($keywords) {
        $this->root = new AhoCorasickNode();
        $this->buildTrie($keywords);
        $this->buildFailureLinks();
    }

    private function buildTrie($keywords) {
        foreach ($keywords as $keyword) {
            $node = $this->root;
            foreach (str_split($keyword) as $char) {
                if (!isset($node->children[$char])) {
                    $node->children[$char] = new AhoCorasickNode();
                }
                $node = $node->children[$char];
            }
            $node->output[] = $keyword;
        }
    }

    private function buildFailureLinks() {
        $queue = [];
        foreach ($this->root->children as $child) {
            $child->fail = $this->root;
            $queue[] = $child;
        }

        while (!empty($queue)) {
            $current = array_shift($queue);
            foreach ($current->children as $char => $child) {
                $fail = $current->fail;
                while ($fail !== null && !isset($fail->children[$char])) {
                    $fail = $fail->fail;
                }
                $child->fail = $fail ? $fail->children[$char] : $this->root;
                $child->output = array_merge($child->output, $child->fail->output);
                $queue[] = $child;
            }
        }
    }

    public function search($text) {
        $node = $this->root;
        $results = [];

        for ($i = 0; $i < strlen($text); $i++) {
            while ($node !== null && !isset($node->children[$text[$i]])) {
                $node = $node->fail;
            }
            if ($node === null) {
                $node = $this->root;
                continue;
            }
            $node = $node->children[$text[$i]];

            foreach ($node->output as $pattern) {
                $results[] = ['position' => $i - strlen($pattern) + 1, 'pattern' => $pattern];
            }
        }
        return $results;
    }

}