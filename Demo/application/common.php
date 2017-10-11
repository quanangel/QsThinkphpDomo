<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * Auth:Qs
 * Name:获取模块名
 * Time:2017/10/11 15:34
 * @return string
 **/
 function getMODULE(){
    return Request::instance()->module();
}
/**
 * Auth:Qs
 * Name:获取控制器名
 * Time:2017/10/11 15:34
 * @return string
 **/
function getCONTROLLER(){
    return Request::instance()->controller();
}
/**
 * Auth:Qs
 * Name:获取操作方法名
 * Time:2017/10/11 15:34
 * @return string
 **/
function getACTION(){
    return Request::instance()->action();
}

/*
 * Auth:Qs
 * Name:获取MAC地址
 * Time:2017/10/11 15:34
 */
function getRuleName(){
    $name=getMODULE().'/'.getCONTROLLER().'/'.getACTION();
    return $name;
}

/*
 * Auth:Qs
 * Name:获取文章关键词
 * @param   $str            分析的文章
 * @param   $num            需提取多少个关键词,默认为4，输出5个
 * @param   $source_charset 来源文章的文字编码
 * @param   $target_charset 生成关键词的文字编码
 * @return  string          返回字符串用,号分隔
 * Time:2017/10/11 15:38
 */
 function getKeyWord($str,$num=4,$source_charset='utf-8',$target_charset='utf-8'){
    $KeyWord = new \Qs\phpanalysis\PhpAnalysis($source_charset, $target_charset);
    $KeyWord->LoadDict();
    $KeyWord->SetSource($str);
    $KeyWord->StartAnalysis();
    return $KeyWord->GetFinallyKeywords($num);
}