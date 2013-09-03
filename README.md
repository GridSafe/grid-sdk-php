# 格安PHP SDK

为了让广大用户能更好地使用速致的产品，我们开发了相应的SDK开发包。

有需要的用户可以下载对应语言版本的开发包进行使用。

SDK开发包主要实现了速致提供的api的简单调用。

现提供PHP、Python、Nodejs、Java等的SDK开发包。

其他语言版本的正在开发中。

要了解速致的产品信息，请查看官方网站。

速致官网：https://www.cdnzz.com

api文档地址：https://www.cdnzz.com/help/user_api


## Requirements

php 5.3+

php curl extension

## Usage

第1步. 修改config.inc.php文件

   - 登陆 https://www.cdnzz.com/account 页面，查看自己的email地址和接口标识
     (signature)
   - 修改config.inc.php的对应的值


第2步. 调用sdk

  ```
    require_once './cdnzzv1.sdk.class.php';
    $cdnzz = new CDNZZ();
    $url = "https://****/index.php?a=asd&b=mid";

    //清缓存
    $response = $cdnzz->purge_cache($url);

    //预加载
    $response1 = $cdnzz->preload($url);
  ```

## Change Log

Please see 'CHANGE_LOG.md'



