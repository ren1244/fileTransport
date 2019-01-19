<?php

/** 
 * 加上數位簽章
 *
 * @param string data 要傳送的資料
 * @param string priKey 私鑰
 * @return string 加上數位簽章的字串(二進位資料)
 */
function addHashVfy($data,$priKey)
{
	$h=hash("sha512",$data);
	if(!openssl_private_encrypt($h,$cHash,$priKey))
		exit("非對稱加密錯誤");
	return $cHash.$data;
}

/** 
 * 驗證數位簽章
 *
 * @param string data 要驗證的字串(二進位資料)
 * @param string pubKey 公鑰
 * @return bool 驗證通過與否
 */
function hashVfy(&$data,$pubKey)
{
	$cHash=substr($data,0,256);
	if(!openssl_public_decrypt($cHash,$h,$pubKey))
		exit("非對稱解密錯誤");
	$data=substr($data,256);
	return strcmp(hash("sha512",$data),$h)==0?true:false;
}

/** 
 * 打包資料
 *
 * @param array fPathNameList 檔名
 * @return string 打包的二進位字串
 */
function packDatas($fPathNameList)
{
	$n=count($fPathNameList);
	$dataList=[];
	for($i=0;$i<$n;++$i)
	{
		$fPathName=$fPathNameList[$i];
		$dataList[]=packData($fPathName);
	}
	return implode("",$dataList);
}

/** 
 * 解包資料
 *
 * @param string data 打包的二進位字串
 * @return void
 */
function unpackDatas($data,$callback=NULL)
{
	$pos=0;
	$n=strlen($data);
	while($pos+6<$n)
	{
		$pos+=unpackData($data,$fn,$ct,$pos);
		if($callback)
			$callback($fn,$ct);
	}
}

/** 
 * 檔案轉為二進位資料，供 packDatas 使用
 *
 * @param string filePathName 路徑及檔名
 * @return string 「檔名長度|內容長度|檔名|內容」的二進位資料
 */
function packData($filePathName)
{
	$content=file_get_contents($filePathName);
	$fileName=($filePathName);
	return pack("vV",strlen($fileName),strlen($content)).$fileName.$content;
}

/** 
 * 將二進位資料解開為：檔名、內容
 *
 * @param string data 二進位資料
 * @param string fName 儲存檔名
 * @param string content 儲存內容
 * @param int offset 位移
 * @return int 使用掉的位元數(提供計算下一區塊資訊)
 */
function unpackData($data,&$fName,&$content,$offset)
{
	$info=unpack("vfLen/VcLen",$data,$offset);
	$content=substr($data,$offset+6+$info["fLen"],$info["cLen"]);
	$fName=substr($data,$offset+6,$info["fLen"]);
	return $info["fLen"]+$info["cLen"]+6;
}

/** 
 * 對資料做AES加密
 *
 * @param string data 資料
 * @param string privateKey 私鑰，用來加密對稱密鑰
 * @return string 對稱加密密鑰(被非對稱加密)|iv|chipertext
 */
function encData($data,$privateKey)
{
	$chiper="aes-256-cbc";
	$chiperKeyLen=32;
	$key=openssl_random_pseudo_bytes($chiperKeyLen);
	if(!openssl_private_encrypt($key,$cKey,$privateKey))
	{
		exit ("非對稱加密錯誤");
	}
	$iv=openssl_random_pseudo_bytes(openssl_cipher_iv_length($chiper));
	$ciphertext_raw=openssl_encrypt($data,$chiper,$key,OPENSSL_RAW_DATA,$iv);
	if($ciphertext_raw===false)
		exit ("對稱加密錯誤");
	return $cKey.$iv.$ciphertext_raw;
}

/** 
 * 對資料做AES解密
 *
 * @param string data 加密的資料
 * @param string publicKey 公鑰，用來解密對稱密鑰
 * @return string 原來的二進位字串
 */
function decData($data,$publicKey)
{
	$chiper="aes-256-cbc";
	$chiperKeyLen=32;
	$ivLen=openssl_cipher_iv_length($chiper);
	$cKey=substr($data,0,256);
	$iv=substr($data,256,$ivLen);
	$data=substr($data,256+$ivLen);
	if(!openssl_public_decrypt($cKey,$key,$publicKey))
	{
		exit ("非對稱解密錯誤".openssl_error_string());
	}
	$orig_raw=openssl_decrypt($data,$chiper,$key,OPENSSL_RAW_DATA,$iv);
	if($orig_raw===false)
		exit ("對稱解密錯誤");
	return $orig_raw;
}
