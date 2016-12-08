# php log udp service 
by swoole

## 环境
安装php swoole(www.swoole.com)

## 目录
├── config.inc.php  
├── lib  
│   ├── exception.php  
│   ├── log_behavior.php  
│   ├── log_client.php  
│   ├── package.php  
│   ├── swoole_behavior.php  
│   └── swoole_service.php  
├── log  
│   ├── 2016120810.log  
│   └── a.log  
├── logservice.php  
├── start.sh  
└── test  
    └── test.php  
  
## 使用
### 服务启动
/usr/local/php7/bin/php logservice.php --host 127.0.0.1 --port 12150 --worker 1 --task 2  
或  
修改start.sh文件，配置php路径，保存后执行  
./start.sh  

### 参数简述
--host listen ip  
--port listen port  
--worker swoole worker num  
--task swoole task worker num  

### 使用
请看test/test.php文件  
