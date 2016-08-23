<?php
$token      = fopen("./cache/access_token.txt", "r");       // 打开access_token文件流
$token      = fread($token, 1023);                          // 获取token
$tokenTime  = fopen("./cache/access_tokenTime.txt", "r");   // 打开access_tokenTime文件流
$prevTime   = intval(fread($tokenTime, 1023));              // 获取上次缓存文件时间
$nowTime    = time() - $prevTime;                           // 计算时间差值

if ($nowTime >= 3600) { // 如果时间差值大于等于3600秒，那么就重新获取access_token
    $res = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxbb878801f06451f2&secret=be839559f4fffbcef9b565621176987f'); // 测试账号的appid与secret
    $res = json_decode($res, true);
    $token = $res['access_token'];
    // $json_string = json_encode($res,JSON_UNESCAPED_UNICODE);
    // 注意：这里需要将获取到的token缓存起来（或写到数据库中）
    // 不能频繁的访问https://api.weixin.qq.com/cgi-bin/token，每日有次数限制
    // 通过此接口返回的token的有效期目前为2小时。令牌失效后，JS-SDK也就不能用了。
    // 因此，这里将token值缓存1小时，比2小时小。缓存失效后，再从接口获取新的token，这样
    // 就可以避免token失效。
    
    // 缓存token码
    $myfile = fopen("./cache/access_token.txt", "w") or die("Unable to open file!");
    fwrite($myfile, $token);
    fclose($myfile);
    // 最后缓存时间
    $myfileTime = fopen("./cache/access_tokenTime.txt", "w") or die("Unable to open file!");
    fwrite($myfileTime, time());
    fclose($myfileTime);
    echo json_encode($res);
} else {
    echo '{"access_token":"' . $token . '"}';
}
?>

