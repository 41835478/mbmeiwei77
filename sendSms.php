<?php

/**
 * ECSHOP 生成验证码
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: captcha.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);
define('INIT_NO_SMARTY', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once('includes/cls_json.php');
include_once('includes/cls_sms.php');
$json= new json();
$result['error']   = 1;
$result['err_msg'] = '非法提交';
$token = $_GET['token'];
$username = $_GET['username'];
if($_SESSION['token']===$token){
//检查用户是否需要发送短信验证
    $result['err_msg'] = '';
    $userInfo = $db->getRow("select * from ".$ecs->table('users')." where user_name='".$username."'");
    if($userInfo){
        if($userInfo['user_rank']){
            $userRank = $db->getRow("select * from ".$ecs->table('user_rank')." where rank_id=".$userInfo['user_rank']);
            if($userRank &&$userRank['special_rank']){
                $result['err_msg'] = '此用户不需要验证码就可登录，请刷新页面重试';
            }
        }
    }else{
        $result['err_msg'] = '用户不存在，请先注册';
    }
    if($result['err_msg']==''){
        //判断手机号
        if($userInfo['mobile_phone']){
            $result['error'] = 0;
            $word = generate_word();
            $_SESSION['smsWord'] = $word;
            $sms = new sms();
            $content = "中圆指尖购物网验证码：【".$word."】，如非本人操作请忽略。";
            $sms->send($userInfo['mobile_phone'],$content);
            $result['content'] = $word;
        }else{
            $result['err_msg'] = '检测到该账户为设置手机号码，请联系客服录入手机号码';
        }
    }


}
die($json->encode($result));
function generate_word($length = 4)
{
    $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

    for ($i = 0, $count = strlen($chars); $i < $count; $i++)
    {
        $arr[$i] = $chars[$i];
    }

    mt_srand((double) microtime() * 1000000);
    shuffle($arr);

    return substr(implode('', $arr), 5, $length);
}
?>