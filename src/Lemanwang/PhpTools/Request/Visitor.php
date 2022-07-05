<?php
/**
 * Created by : UncleFreak
 * User: UncleFreak <00@z88j.com>
 * Date: 2022/1/21
 * Time: 17:39
 */

namespace Lemanwang\PhpTools\Request;


class Visitor
{
    /**
     * 获取浏览器名称
     * Author: UncleFreak <00@z88j.com>
     * @return string
     */
    public static function getBrowserName()
    {
        $br = '';
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $br = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/MSIE/i', $br)) {
                $br = 'MSIE';
            } elseif (preg_match('/Firefox/i', $br)) {
                $br = 'Firefox';
            } elseif (preg_match('/Chrome/i', $br)) {
                $br = 'Chrome';
            } elseif (preg_match('/Safari/i', $br)) {
                $br = 'Safari';
            } elseif (preg_match('/Opera/i', $br)) {
                $br = 'Opera';
            } else {
                $br = 'Other';
            }
        }
        return $br;
    }
}
