<?php
/*
 * File Name:qsAuth.php
 * Auth:Qs
 * Name:Qs简易AUTH控制器
 * Note:
 * Time:2017/7/19 10:45
 */
namespace Qs\auth;
use think\Db;
Class qsAuth{
    protected $_config=array(
        'AUTH_ON'           => true, // 认证开关
        'AUTH_TYPE'         => 1, // 认证方式，1为实时认证；
        'AUTH_GROUP'        => 'yl_admin_group', // 用户组数据表名
        'AUTH_GROUP_ACCESS' => 'yl_admin_group_access', // 用户-用户组关系表
        'AUTH_RULE'         => 'yl_admin_rule', // 权限规则表
        'AUTH_USER'         => 'ecm_member', // 用户信息表
    );

    /*
     * Auth:Qs
     * Name:检查权限
     * Note:
     * @param name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
     * @param uid  int           认证用户的id
     * @param string mode        执行check的模式
     * @param relation string    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
     * @return boolean           通过验证返回true;失败返回false
     * Time:2017/7/19 11:06
     */
    public function check($name, $uid, $type = 1)
    {
        if (!$this->_config['AUTH_ON']) {
            return true;
        }
        $authList = $this->getAuthList($uid, $type); //获取用户需要验证的所有有效规则列表
        if (is_string($name)) {
            $name = strtolower($name);
        }
        $list = array(); //保存验证通过的规则名
        foreach ($authList as $auth) {
            if($name==$auth){
                return true;
            }
        }
        return false;
    }

    /*
     * Auth:Qs
     * Name:根据用户id获取用户组,返回值为数组
     * Note:
     * @param  uid int     用户id
     * @return array       用户所属的用户组 array(
     * array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'), ...)
     * Time:2017/7/19 11:07
     */
    public function getGroups($uid){
        static $groups = array();
        if (isset($groups[$uid])) {
            return $groups[$uid];
        }
        $result=Db::table($this->_config['AUTH_GROUP_ACCESS'])
            ->alias('a')
            ->where(['user_id'=>$uid,'status'=>1])
            ->join($this->_config['AUTH_GROUP'].' b','a.group_id=b.id')
            ->select();
        $groups[$uid]=$result?:array();

        return $groups[$uid];
    }

    /*
     * Auth:Qs
     * Name:获得权限列表
     * Note:
     * @param integer $uid  用户id
     * @param integer $type
     * Time:2017/7/19 11:33
     */
    protected function getAuthList($uid, $type)
    {
        static $_authList=array(); //保存用户验证通过的权限列表
        $t=implode(',', (array) $type);
        if (isset($_authList[$uid.$t])) {
            return $_authList[$uid.$t];
        }

        //读取用户所属用户组
        $groups=$this->getGroups($uid);
        $ids=array(); //保存用户所属用户组设置的所有权限规则id
        foreach ($groups as $g) {
            $ids=array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid . $t] = array();
            return array();
        }

        $map = array(
            'id'     => array('IN', $ids),
            'type'   => $type,
            'status' => 1,
        );

        //读取用户组所有权限规则
        $rules=Db::table($this->_config['AUTH_RULE'])->where($map)->field('name')->select();

        //循环规则，判断结果。
        $authList=array(); //

        foreach ($rules as $rule) {
            //只要存在就记录
            $authList[] = strtolower($rule['name']);
        }

        $_authList[$uid . $t] = $authList;
        return array_unique($authList);
    }

}