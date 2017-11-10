<?php
/*
 * File Name:QsSso.php
 * Auth:Qs
 * Name:
 * Note:
 * Time:2017/10/24 11:24
 */
namespace Qs\sso;

use Qs\redis\QsRedis;
use think\Db;

Class Service {

    /*
     * Auth:Qs
     * Name:SSO service 配置
     * Note:
     * Time:2017/10/24 11:47
     */
    protected $__config = [
        'USER_TABLE' => 'ecm_member',
        'TABLE_NAME' => 'user_name',
        'TABLE_PWD' => 'password',
        'COOKIE_TIME' => 3600 ,
        'SERVICE_NAME' => 'SSO',
        'SERVICE_SECRET' => '124acghmdt',
        'SSO'  => ['secret'=>'124acghmdt', 'url'=>'http://www.trya.com/client/Sso/index'],
        'YiLiao'  => ['secret'=>'6y4fcghmdt', 'url'=>'http://www.tryb.com/client/Sso/index'],
    ];

    /*
     * Auth:Qs
     * Name:Redis的配置
     * Note:
     * Time:2017/10/24 11:49
     */
    protected $__redis = [
        'HOST'       => '192.168.2.103',
        'PORT'       => 6379,
        'PASSWORD'   => '123456', //验证密码
        'SELECT'     => 15,
        'TIMEOUT'    => 0,
        'EXPIRE'     => 3600, // 有效时间
        'PERSISTENT' => false,
        'PREFIX'     => 'now_',
    ];

    protected $__cookie_name = ''; //用于保存cookie名
    protected $handler = null; // 用于保存QsRedis

    public function __construct($__config = [], $__redis = []) {
        $this->__config = empty($__config) ? $this->__config : array_merge($this->__config, $__config);
        $this->__redis = empty($__redis) ? $this->__redis : array_merge($this->__redis, $__redis);
        $this->__cookie_name = $this->get_cookie_name();
        $this->handler = new QsRedis($this->__redis);
    }

    /*
     * Auth:Qs
     * Name:验证是否SSO内的配置应用
     * Note:
     * @param   string      $appName    需要验证APP的应用名
     * @param   string      $appSecret  需要验证APP的secret
     * @return  boolean
     * Time:2017/10/24 13:19
     */
    public function check_app($appName, $appSecret) {
        if ( empty($this->__config[$appName]) ) return false;
        if ( md5($this->__config[$appName]['secret']) == $appSecret ) return true;
        return false;
    }

    /*
     * Auth:Qs
     * Name:判断是否已登录
     * Note:
     * @param   string      $appName    SSO的APP的应用名
     * @param   string      $appSecret  SSO的APP的secret
     * @return  boolean
     * Time:2017/10/24 16:09
     */
    public function is_attached($appName, $appSecret) {
        $appName = empty($appName) ? $this->__config['SERVICE_NAME'] : $appName;
        $appSecret = empty($appSecret) ? md5($this->__config['SERVICE_SECRET']) : $appSecret;
        if ( !$this->check_app($appName, $appSecret) ) return false;
        if ( !empty($_COOKIE[$this->get_cookie_name()]) ) return [$this->get_cookie_name()=>$_COOKIE[$this->get_cookie_name()]];
        return false;
    }

    /*
     * Auth:Qs
     * Name:登录
     * Note:
     * @param   string      $appName    操作的APP的应用名
     * @param   string      $appSecret  操作的APP的secret
     * @param   string      $username   登录的用户名
     * @param   string      $password   登录的用户密码
     * @return  boolean
     * Time:2017/10/24 16:10
     */
    public function login($appName, $appSecret, $username, $password) {
        if ( !$this->check_app($appName, $appSecret) ) return false;
        $cookieName = $this->get_cookie_name();
        $result = Db::table($this->__config['USER_TABLE'])->where([ $this->__config['TABLE_NAME']=>$username, $this->__config['TABLE_PWD']=>$password ])->find();
        if ( !empty($result) && $this->set(md5($cookieName . $username), $username . '||' . $result['user_id'], $this->__config['COOKIE_TIME']) ) {
            return [$cookieName,md5($cookieName . $username)];
        }
        return false;
    }

    /*
     * Auth:Qs
     * Name:登出
     * Note:
     * @param   string      $appName    操作的APP的应用名
     * @param   string      $appSecret  操作的APP的secret
     * @return  boolean
     * Time:2017/10/24 16:11
     */
    public function logout($appName, $appSecret, $keyName) {
        if ( !$this->check_app($appName, $appSecret) ) return false;
        if ($this->handler->rm($keyName)) return true;
        return false;
    }

    /*
     * Auth:Qs
     * Name:获取保存的COOKIE名
     * Note:
     * @param   string      $appName    SSO的APP的应用名
     * @param   string      $appSecret  SSO的APP的secret
     * @return  string
     * Time:2017/10/24 13:26
     */
    public function get_cookie_name($appName = null, $appSecret = null) {
        $appName = empty($appName) ? $this->__config['SERVICE_NAME'] : $appName;
        $appSecret = empty($appSecret) ? md5($this->__config['SERVICE_SECRET']) : $appSecret;
        return $appName . '_' .$appSecret;
    }

    /*
     * Auth:Qs
     * Name:获取COOKIE的值
     * Note:
     * @param   string      $cookieName     Cookie的name
     * @return  string
     * Time:2017/10/24 13:37
     */
    public function get_cookie($cookieName = null) {
        $cookieName = empty($cookieName) ? $this->__cookie_name : $cookieName;
        return $_COOKIE[$cookieName];
    }

    /*
     * Auth:Qs
     * Name:判断REDIS是否有记录
     * Note:
     * @param   string      $cookieName     KEY名
     * $return  boolean
     * Time:2017/10/24 16:12
     */
    public function has($cookieName = null) {
        $cookieName = empty($cookieName) ? $this->__cookie_name : $cookieName;
        if ($this->handler->get($_COOKIE[$cookieName]) ) return true;
        return false;
    }

    /*
     * Auth:Qs
     * Name:设置REDIS的KEY、VALUE
     * Note:
     * @param   string      $cookieName     KEY名
     * @param   string      $value          VALUE值
     * @param   string      $expire         时效
     * @return  boolean
     * Time:2017/10/24 16:15
     */
    public function set($cookieName, $value, $expire = null) {
        $expire = is_null($expire) ? $this->__config['COOKIE_TIME'] : $expire;
        if ( is_int($expire) && $expire ) {
            $result = $this->handler->handler()->setex($cookieName, $expire, $value);
        } else {
            $result = $this->handler->handler()->set($cookieName, $value);
        }
        return $result;
    }


}