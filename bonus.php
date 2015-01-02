<?php

/**
 * ECSHOP 红包类型的处理
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: druphliu $
 * $Id: bonus.php 17217 2011-01-19 06:29:08Z druphliu $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include('includes/cls_json.php');
$json = new JSON;
$res = array('err_msg' => '', 'result' => '', 'qty' => 1);
$uid = $_SESSION['user_id'];
if (!empty($_REQUEST['act']) && $_REQUEST['act'] == 'bonus') {
    $bonus_type_id = (isset($_REQUEST['id'])) ? intval($_REQUEST['id']) : 0;
    //当前用户是否已经领取过

    $hasGet = $db->getOne("select * from " . $ecs->table('user_bonus') . " where user_id=" . $uid . " and bonus_type_id=" . $bonus_type_id);

    if ($hasGet <= 0 && $uid>0) {
        //已领取的个数
        $count = $db->getOne("select count(*) from " . $ecs->table('user_bonus') . " where bonus_type_id=" . $bonus_type_id );
        $time = time();
        $bonus = $db->getRow("select * from " . $ecs->table('bonus_type') . " where send_type=" . SEND_BY_SEARCH . " and type_id=" . $bonus_type_id);
        if ($bonus && ($bonus['send_start_date'] <= $time && $bonus['send_end_date'] >= $time) && $count<$bonus['count']) {
            $res['result'] = $bonus;
        }
    }
    die($json->encode($res));

} else if (!empty($_REQUEST['act']) && $_REQUEST['act'] == 'get_bonus') {
    $bonus_type_id = (isset($_REQUEST['id'])) ? intval($_REQUEST['id']) : 0;
    $uid = $_SESSION['user_id'];
    $hasGet = $db->getOne("select * from " . $ecs->table('user_bonus') . " where user_id=" . $uid . " and bonus_type_id=" . $bonus_type_id);
    if ($hasGet <= 0 && $uid>0) {
        //已领取的个数
        $count = $db->getOne("select count(*) from " . $ecs->table('user_bonus') . " where bonus_type_id=" . $bonus_type_id);
        $time = time();
        $bonus = $db->getRow("select * from " . $ecs->table('bonus_type') . " where send_type=" . SEND_BY_SEARCH . " and type_id=" . $bonus_type_id);
        if ($bonus && ($bonus['send_start_date'] <= $time && $bonus['send_end_date'] >= $time)&& $count<$bonus['count']) {
            $sql = "insert into ".$ecs->table('user_bonus')."(bonus_type_id,user_id)values($bonus_type_id,$uid)";
            $db->query($sql);
        }else{
            $res['err_msg'] = '已经过期了';
        }
    }else{
        $res['err_msg'] = '已经领取了';
    }
    die($json->encode($res));
}


?>