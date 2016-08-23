<?php
$timestamp  = $_GET['timestamp'];
$wxnonceStr = $_GET['nonceStr'];
$wxticket   = $_GET['ticket'];
// 将ticket、noncestr、timestamp、分享的url按字母顺序连接起来，进行sha1签名
$wxOri = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s", $wxticket, $wxnonceStr, $timestamp, $_GET['url']);
$wxSha1 = sha1($wxOri);
echo $wxSha1;
?>