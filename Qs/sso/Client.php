<?php
/*
 * File Name:Client.php
 * Auth:Qs
 * Name:
 * Note:
 * Time:2017/10/26 9:58
 */

namespace Qs\sso;

use think\Session;

Class Client{
    protected $sso_url;
    protected $sso_app;
    protected $sso_secret;

    public function __construct($sso_url, $sso_app, $sso_secret){
        $this->sso_url = $sso_url;
        $this->sso_app = $sso_app;
        $this->sso_secret = $sso_secret;
    }

    public function client_set_cookie($cookieName, $cookieValue) {
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

        if ( !empty($cookieName) && !empty($cookieValue) ) {
            $host = array_filter(explode('.',$_SERVER['HTTP_HOST']));
            $host = count($host)==2 ? implode('.',$host) : $host[1] . '.' . $host[2] ;
            setcookie($cookieName, $cookieValue, time()+3600*24, "/", $host);
        }
    }

    public function client_unset_cookie($cookieName) {
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

        if ( !empty($cookieName) ) {
            $host = array_filter(explode('.',$_SERVER['HTTP_HOST']));
            $host = count($host)==2 ? implode('.',$host) : $host[1] . '.' . $host[2] ;
            setcookie($cookieName,'', time()-3600, "/", $host);
        }
    }


    public function client_login($username, $password, $url = '') {

        if ( empty($username) || empty($password) ) return '用户密码错误';
        $url = empty($url) ? $this->sso_url : $url;
        //$url = 'http://www.trya.com/index/Index/login';
        $post_date['appName'] = $this->sso_app;
        $post_date['secret'] = $this->sso_secret;
        $post_date['username'] = $username;
        $post_date['password'] = $password;
        $post_date['type'] = 'login';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_date);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        if ( stristr($output,'登录成功') ) {
            echo str_ireplace('登录成功', '', $output);
            return '登录成功';
        }
        return '登录失败' ;
        //if ( !empty($output) ) echo $output;
    }

    public function client_logout($sso_app = '', $sso_secret = '', $url = ''){
        $post_date['appName'] = empty($sso_app) ? $this->sso_app : $sso_app;
        $post_date['secret'] = empty($sso_secret) ? $this->sso_secret : $sso_secret;
        $post_date['type'] = 'logout';
        if ( empty($_COOKIE[md5($post_date['appName'] . $post_date['secret'])]) ) {
            Session::clear();
            return '登出成功';
        }
        $post_date['keyName'] = $_COOKIE[md5($post_date['appName'] . $post_date['secret'])];
        $url = empty($url) ? $this->sso_url : $url;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_date);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);

        if ( stristr($output,'登出成功') ) {
            echo str_ireplace('登出成功', '', $output);
            return '登出成功';
        }
        return '登出失败' ;

    }

}