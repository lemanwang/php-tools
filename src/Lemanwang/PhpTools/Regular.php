<?php

namespace Lemanwang\PhpTools;

class Regular
{
    /**
     * 正则一些特殊字符转义
     * @return void
     */
    public function specialCharacterEscape(&$str){
        $str = preg_replace('/\(/', '\(',$str);
        $str = preg_replace('/\)/', '\)',$str);
        $str = preg_replace('/\[/', '\[',$str);
        $str = preg_replace('/\]/', '\]',$str);
        $str = preg_replace('/\{/', '\{',$str);
        $str = preg_replace('/\}/', '\}',$str);
        $str = preg_replace('/\//', '\/',$str);
        $str = preg_replace('/\\/', '\\',$str);
        $str = preg_replace('/\*/', '\*',$str);
        $str = preg_replace('/\+/', '\+',$str);
        $str = preg_replace('/\./', '\.',$str);
        $str = preg_replace('/\?/', '\?',$str);
        $str = preg_replace('/\^/', '\^',$str);
        $str = preg_replace('/\$/', '\$',$str);
    }
}