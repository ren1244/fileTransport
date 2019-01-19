# 命令列上傳工具

## 使用方式

1. 先在 localhost 執行 key.php 檔案，這會重新產生 pub.key 與 pri.key 這兩個檔案。
2. 把 reciver.php libDataEnc.php 與 pub.key 傳送到遠端伺服器。
3. 在本地端執行 php upload.php 檔名 [檔名]... 就會更新本地端的檔案到遠端。

### 注意事項

* upload.php 跟 reciver.php 請放在同層級的資料夾，以便相對路徑對應
* 檔名可以帶相對路徑，例如：`dirName/fileName`