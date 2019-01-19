<?php
$opensslCfg=[
	'config' => 'C:\MyAMP\bin\php7.1.17\extras\ssl\openssl.cnf',
];
$res=openssl_pkey_new($opensslCfg);
if($res===false)
	exit("new");

if(!openssl_pkey_export($res,$privkey,null,$opensslCfg))
	exit("pri");
$pubkey = openssl_pkey_get_details($res);
$pubkey = $pubkey["key"];
echo "<pre>".print_r($privkey,true)."</pre>";
echo "<pre>".print_r($pubkey,true)."</pre>";

file_put_contents("pri.key",$privkey);
file_put_contents("pub.key",$pubkey);

$enc="";
$dec="";
$txt="abc";

if(!openssl_private_encrypt($txt,$enc,$privkey))
	exit("-1");
if(!openssl_public_decrypt($enc,$dec,$pubkey))
	exit("-2".strlen($enc));
echo $dec;
