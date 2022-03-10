<?php

namespace Lemanwang\PhpTools;

class Regular
{
    /**
     * 正则一些特殊字符转义
     * @return void 引用传递
     */
    public function specialCharacterEscape(&$str){
        $str = preg_replace('/\(/', '\(',$str);
        $str = preg_replace('/\)/', '\)',$str);
        $str = preg_replace('/\[/', '\[',$str);
        $str = preg_replace('/\]/', '\]',$str);
        $str = preg_replace('/\{/', '\{',$str);
        $str = preg_replace('/\}/', '\}',$str);
        $str = preg_replace('/\//', '\/',$str);
//        $str = preg_replace('/\\/', '\\',$str);
        $str = preg_replace('/\*/', '\*',$str);
        $str = preg_replace('/\+/', '\+',$str);
        $str = preg_replace('/\./', '\.',$str);
        $str = preg_replace('/\?/', '\?',$str);
        $str = preg_replace('/\^/', '\^',$str);
        $str = preg_replace('/\$/', '\$',$str);
    }
}