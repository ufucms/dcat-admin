<?php

namespace Dcat\Admin\Http\Controllers;

use App\Http\Controllers\Controller;

class AddonsController extends Controller
{
    /**
     * $msg   返回提示消息
     * $data  返回数据
     */
    public function success($data = [], $msg = '获取数据成功', $code = 200)
    {
        if( is_string($data) && $msg == '获取数据成功'){
            $msg  = $data;
            $data = null;
        }
        return response()->json([
            'status' => 1,
            'code'   => $code,
            'msg'    => $msg,
            'data'   => $data,
        ]);
    }

    /**
     * $msg   返回提示消息
     * $data  返回数据
     */
    public function fail($data = [], $msg = '请求接口出错', $code = 403)
    {
        if( is_string($data) && $msg == '请求接口出错'){
            $msg  = $data;
            $data = null;
        }
        return response()->json([
            'status' => 0,
            'code'   => $code,
            'msg'    => $msg,
            'data'   => $data,
        ]);
    }
}
