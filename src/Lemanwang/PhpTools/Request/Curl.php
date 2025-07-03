<?php

namespace Lemanwang\PhpTools\Request;

class Curl
{
    function postJson( $url,  $data,  $headers = [], $timeout = 10)
    {
        $ch = curl_init($url);

        // 设置默认头部
        $defaultHeaders = [
            'Content-Type: application/json',
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array_merge($defaultHeaders, $headers),
            CURLOPT_TIMEOUT => $timeout,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return "cURL 错误: $error";
        }

        return $response;
    }
    function generateMd5SignatureExcludeToken(array $data, string $salt): string {
        $kvPairs = [];

        foreach ($data as $key => $value) {
            // 跳过 token 和空键
            if ($key === 'token' || $key === '-') {
                continue;
            }

            // 处理数组或多值字段
            if (is_array($value)) {
                $kvPairs[] = $key . '=' . implode(',', $value);
            } else {
                $kvPairs[] = $key . '=' . $value;
            }
        }

        // 升序排序
        sort($kvPairs, SORT_STRING);

        // 拼接字符串 + salt
        $joined = implode('&', $kvPairs) . $salt;

//        // 打印调试信息（可选）
//        echo "---------------start-2025年6月26日14点46分-" . date('Y-m-d H:i:s') . "---------------------\n";
//        echo "拼接后的字符串: $joined\n";

        // 返回小写 MD5 签名
        return strtolower(md5($joined));
    }
}