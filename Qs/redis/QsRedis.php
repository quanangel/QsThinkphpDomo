<?php
/*
 * File Name:QsRedis.php
 * Auth:Qs
 * Name:Redis数据缓存
 * Note:
 * Time:2017/10/18 16:32
 */
namespace Qs\redis;

Class QsRedis {
    protected $tag; // 缓存名的标签
    protected $handler = null;
    protected $_config = array(
        'HOST'       => '127.0.0.1',
        'PORT'       => 6379,
        'PASSWORD'   => '123456', //验证密码
        'SELECT'     => 0,
        'TIMEOUT'    => 0,
        'EXPIRE'     => 3600, // 有效时间
        'PERSISTENT' => false,
        'PREFIX'     => 'now_',
    );
    public function __construct($_config = []) {
        if (!empty($_config)) $this->_config = array_merge($this->_config, $_config); // 更新配置

        $this->handler = new \Redis();
        $this->handler->connect($this->_config['HOST'],$this->_config['PORT'],$this->_config['TIMEOUT']);

        if ( !empty( $this->_config['PASSWORD'] ) ) {
            $this->handler->auth($this->_config['PASSWORD']);
        }

        if ( !empty( $this->_config['SELECT'] ) ) {
            $this->handler->select($this->_config['SELECT']);
        }
    }

    /*
     * Auth:Qs
     * Name:判断是否存在该缓存名
     * Note:
     * @param   string  $name   需查找的缓存名
     * @return  string
     * Time:2017/10/18 17:07
     */
    public function has($name) {
        return $this->handler->get($name) ? true : false;
    }

    /*
     * Auth:Qs
     * Name:获取缓存的值
     * Note:
     * @param   string      $name       需获取的缓存名
     * @param   boolean     $default    默认返回值为false
     * @return  string|array
     * Time:2017/10/18 17:36
     */
    public function get($name, $default = false) {
        $value = $this->handler->get($name);
        if( is_null($value) ) {
            return $default;
        }
        $jsonData = json_decode($value, true);
        // 判断$jsonData是否完全等于NULL，是：直接返回$value的值，否：返回JSON格式化的数组$jsonData
        return (null === $jsonData) ? $value : $jsonData;
    }

    /*
     * Auth:Qs
     * Name:
     * Note:
     * @param   string                  $name   设置的缓存名
     * @param   object|array|string     $value  缓存的值
     * @param   integer                 $expire 有效时间（秒）
     * @return  boolean
     * Time:2017/10/18 23:49
     */
    public function set($name, $value, $expire = null) {
        $expire = is_null($expire) ? $this->_config['EXPIRE'] : $expire;

        if ( $this->tag && !$this->has($name) ) {
            $first = true;
        }
        $key = $this->getRealKey($name);
        $value = ( is_object($value) || is_array($value) ) ? json_encode($value, true) : $value;
        if ( is_int($expire) && $expire ) {
            $result = $this->handler->setex($key, $expire, $value);
        } else {
            $result = $this->handler->set($key, $value);
        }
        isset($first) && $this->setTagItem($key);
        return $result;
    }

    /*
     * Auth:Qs
     * Name:删除缓存
     * Note:
     * @param   string  $name   需删除的缓存名
     * @return  boolean
     * Time:2017/10/18 23:57
     */
    public function rm($name) {
        return $this->handler->del($name);
    }

    /*
     * Auth:Qs
     * Name:清除缓存
     * Note:
     * @param   string  $tag    标签名
     * Time:2017/10/19 0:56
     */
    public function clear($tag = null) {
        if ( $tag ) {
            $keys = $this->getTagItem($tag);
            foreach ( $keys as $key ) {
                $this->handler->delete($key);
            }
            $this->rm('tag_' . md5($tag));
            return true;
        }
        return $this->handler->flushDB();
    }

    /*
     * Auth:Qs
     * Name:获取实际缓存名
     * Note:
     * @param   string  $name   需查找的缓存名
     * @return  string
     * Time:2017/10/18 17:04
     */
    public function getRealKey($name)
    {
        return $this->_config['PREFIX'] . $name;
    }

    /*
     * Auth:Qs
     * Name:更新缓存标识
     * Note:
     * @param   string  $name   缓存标识
     * Time:2017/10/19 0:07
     */
    protected function setTagItem($name) {
        if ( $this->tag ) {
            $key = 'tag_' . md5($this->tag);
            if ( $this->has($key) ) {
                $value = explode(',', $this->get($key));
                $value[] = $name;
                $value = implode(',', array_unique($value));
            } else {
                $value = $name;
            }
            $this->set($key, $value, 0);
        }
    }

    /*
     * Auth:Qs
     * Name:获取标签包含的缓存标识
     * Note:
     * @param   string  $tag    缓存标签
     * @return  array
     * Time:2017/10/19 0:53
     */
    public function getTagItem($tag) {
        $key = 'tag_' . md5($tag);
        $value = $this->get($key);
        if ( $value ) {
            return array_filter(explode(',', $value));
        } else {
            return [];
        }
    }

    /*
     * Auth:Qs
     * Name:返回句柄对象，可执行其它高级方法
     * Note:
     * @return  object
     * Time:2017/10/19 0:57
     */
    public function handler(){
        return $this->handler;
    }

}
