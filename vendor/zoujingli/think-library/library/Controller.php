<?php

// +----------------------------------------------------------------------
// | Library for ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2018 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://library.thinkadmin.top
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/ThinkLibrary
// +----------------------------------------------------------------------

namespace library;

use library\tools\Cors;
use library\traits\Jump;

/**
 * 标准控制器基类
 * --------------------------------
 * Class Controller
 * @package library
 * --------------------------------
 * @method logic\Search _search($dbQuery)
 * @method array _validate($data, $rule = [], $message = [])
 * @method mixed _delete($dbQuery, $pkField = '', $where = [])
 * @method mixed _save($dbQuery, $data = [], $pkField = '', $where = [])
 * @method array _page($dbQuery, $isPage = true, $isDisplay = true, $total = false)
 * @method mixed _form($dbQuery, $tplFile = '', $pkField = '', $where = [], $extendData = [])
 * --------------------------------
 * @author Anyon <zoujingli@qq.com>
 * @date 2018/08/10 11:31
 */
class Controller
{

    use Jump;

    /**
     * 当前请求对象
     * @var \think\Request
     */
    protected $request;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        Cors::optionsHandler();
        $this->request = request();
    }

    /**
     * 实例方法调用
     * @param string $name 函数名称
     * @param array $arguments 调用参数
     * @return mixed
     */
    public function __call($name, $arguments = [])
    {
        $className = "library\\logic\\" . ucfirst(ltrim($name, '_'));
        if (class_exists($className)) {
            $app = app($className, $arguments);
            return method_exists($app, 'apply') ? $app->apply($this) : $app;
        }
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        }
    }

    /**
     * 数据回调处理机制
     * @param string $name 回调方法名称
     * @param mixed $one 回调引用参数1
     * @param mixed $two 回调引用参数2
     * @return boolean
     */
    public function _callback($name, &$one, &$two = [])
    {
        $action = $this->request->action();
        foreach ([$name, "_{$action}{$name}"] as $method) {
            if (method_exists($this, $method) && false === $this->$method($one, $two)) {
                return false;
            }
        }
        return true;
    }

}