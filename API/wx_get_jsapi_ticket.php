<?php
$ticket         = fopen("./cache/ticket.txt", "r");     // 打开ticket文件流
$ticket         = fread($ticket, 1023);                 // 获取ticket
$ticketTime     = fopen("./cache/ticketTime.txt", "r"); // 打开ticketTime文件流
$prevTime       = intval(fread($ticketTime, 1023));     // 获取上次缓存文件时间
$nowTime        = time() - $prevTime;                   // 计算时间差值

if ($nowTime >= 3600) { // 如果时间差值大于等于3600秒，那么就重新获取ticket
    do {
        $token  = $_GET['access_token'];
        $url2   = sprintf("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi", $token);
        $res    = file_get_contents($url2);
        $res    = json_decode($res, true);
        $ticket = $res['ticket'];
    } while (0);
    
    // 注意：这里需要将获取到的ticket缓存起来（或写到数据库中）
    // ticket和token一样，不能频繁的访问接口来获取，在每次获取后，我们把它保存起来。
    // 缓存临时票据
    $myfile_ = fopen("./cache/ticket.txt", "w") or die("Unable to open file!");
    fwrite($myfile_, $ticket);
    fclose($myfile_);
    // 缓存时间
    $myfile_Time = fopen("./cache/ticketTime.txt", "w") or die("Unable to open file!");
    fwrite($myfile_Time, time());
    fclose($myfile_Time);
    
    echo json_encode($res);
} else {
    echo '{"ticket":"' . $ticket . '"}';
}
?>