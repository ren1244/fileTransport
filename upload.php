<?php
require_once("libDataEnc.php");
//======設定=======
$privateKey=file_get_contents("pri.key");
$url="http://localhost/file/remote/reciver.php";
//================
//資料處理
$fList=[];
for($i=1;$i<count($argv);++$i)
	if(!is_dir($argv[$i]))
		$fList[]=$argv[$i];
$data=packDatas($fList); //包裝
$data=encData($data,$privateKey); //加密
$data=addHashVfy($data,$privateKey); //驗證雜湊

echo "傳送：".strlen($data)."位元組\n";
//傳送資料
$ch=curl_init();
curl_setopt_array($ch,[
	CURLOPT_URL=>$url,
	CURLOPT_RETURNTRANSFER=>true,
	CURLOPT_HTTPHEADER=>[
		'Content-Length: '.strlen($data)
	],
	CURLOPT_POSTFIELDS=>$data
]);
$r=curl_exec($ch);
curl_close($ch);
echo "回應：\n$r";
