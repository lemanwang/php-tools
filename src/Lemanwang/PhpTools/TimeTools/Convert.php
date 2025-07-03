<?php

namespace Lemanwang\PhpTools\TimeTools;

class Convert
{
    /**
     *  时间转换成秒
     * @param $seconds
     * @return string
     */
    public function SecondsToMinutesString($seconds,$min ='min',$sec ='s')
    {
        $temp1= bcdiv($seconds,60);
        $result = "";
        if ($temp1 > 1){
            $result .= $temp1.$min;
            $temp2 = $seconds % 60;
            $result .= empty($temp2)?'':$temp2.$sec;
        }else{
            $result .= $temp1.$sec;
        }
        return  $result;
    }
}