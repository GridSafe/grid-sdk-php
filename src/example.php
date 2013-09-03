<?php

/**
 * 请先修改config.inc.php文件中的email和signature
 *
 */

require_once "./cdnzzv1.sdk.class.php";

try{

    $cdnzz = new CDNZZ();
    
    $url = "https://***.com/index.php?a=asd&b=mid";
    //清缓存
    $response = $cdnzz->purge_cache($url);
    print_r($response);

    //预加载
    $response = $cdnzz->preload($url);
    print_r($response);

}catch(Exception $e){
    echo $e->getMessage();
}

