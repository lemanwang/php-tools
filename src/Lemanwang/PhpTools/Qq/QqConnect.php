<?php
/**
 * Created by : UncleFreak
 * User: UncleFreak <00@z88j.com>
 * Date: 2022/7/5
 * Time: 22:30
 * qq互联SDK函数
 */

namespace Lemanwang\PhpTools\Qq;

class QqConnect
{
    private  $authorizeUrl;
    public function __construct(){
        $this->authorizeUrl = 'https://graph.qq.com/oauth2.0/authorize';
    }

    //获取Authorization Code
    public function getAuthorizeCode($appid,$redirect_uri,$state,$scope='get_user_info',$display = ''){
        $data = [
            'response_type'     => 'code',
            'client_id'         => $appid,// 申请QQ登录成功后，分配给应用的appid。
            'redirect_uri'      => urlencode($redirect_uri),// 成功授权后的回调地址，必须是注册appid时填写的主域名下的地址，建议设置为网站首页或网站的用户中心。注意需要将url进行URLEncode。
            'state'             => $state,// client端的状态值。用于第三方应用防止CSRF攻击，成功授权后回调时会原样带回。请务必严格按照流程检查用户与state参数状态的绑定。
            'scope'             => $scope,
            'display'           => $display,//仅PC网站接入时使用。用于展示的样式。不传则默认展示为PC下的样式。如果传入“mobile”，则展示为mobile端下的样式。
        ];
        $param = http_build_query($data);
        $url = $this->authorizeUrl.'?'.$param;
        var_dump($url);exit;

    }
}