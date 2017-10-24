<?php
namespace app\index\controller;

use think\Controller;
use think\Cookie;
use think\Request;
use think\Session;
use Qs\sso\Service;

class Index extends Controller
{
    public function index() {

    }

    public function login() {
        $data=Request::instance()->param();
        $sso_service = new Service();
        if ( $sso_service->login($data['appName'], md5($data['secret']), $data['username'], md5($data['pwd'])) ) {
            $this->success('登录成功', $data['rUrl']);
        }
        $this->error('登录失败',$data['rUrl'],'',60);
    }

    public function check() {
        $data=Request::instance()->param();
        $sso_service = new Service();
        if( $sso_service->is_attached($data['appName'], $data['secret']) ) {
            $this->redirect($data['rUrl']);
        }
    }

    public function logout(){
        $data=Request::instance()->param();
        $sso_service = new Service();
        if ( $sso_service->logout($data['appName'], $data['secret']) ) {
            $this->success('退出成功', $data['rUrl']);
        };
        $this->error('登录失败',$data['rUrl']);
    }

}
