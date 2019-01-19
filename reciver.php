<?php
//=====設定區=======
//確認路徑正確
require_once("libDataEnc.php");
$publicKey=file_get_contents("pub.key");

//定義取得檔名與檔案內容後如何處理
function onGetFile($f,$c) //解包用callback函式
{
	$r=file_put_contents("tmp/".$f,$c);
	echo "$f ... ".($r!==false?"success\n":"failure\n");
}
//================================

$data=file_get_contents("php://input");
if(!hashVfy($data,$publicKey)) //驗證
	exit("驗證錯誤");
$data=decData($data,$publicKey); //解密
unpackDatas($data,"onGetFile"); //解包


