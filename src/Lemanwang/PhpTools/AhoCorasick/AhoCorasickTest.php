<?php

namespace Lemanwang\PhpTools\AhoCorasick;

class AhoCorasickTest
{
    function filterComment($comment, $bannedWords = []) {
        $ac = new AhoCorasick($bannedWords);
        $matches = $ac->search($comment);

        $triggered = !empty($matches);

        foreach ($matches as $match) {
            $comment = substr_replace($comment, str_repeat('*', strlen($match['pattern'])), $match['position'], strlen($match['pattern']));
        }

        return [
            'filteredComment' => $comment,
            'triggered' => $triggered
        ];
    }


//// 使用示例
//$bannedWords = ['word1', 'word2', 'anotherword', 'morewords'];
//$result = filterComment('This is a test with word1 and anotherword.', $bannedWords);
//
//echo "Filtered Comment: " . $result['filteredComment'] . "\n";
//echo "Triggered: " . ($result['triggered'] ? 'Yes' : 'No') . "\n";

}