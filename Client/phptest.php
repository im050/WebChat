<?php
///*
//$redis = new Redis();
//$redis->connect('127.0.0.1', 6379);
//$redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
//
//$count = 1000;
//while(($keys = $redis->scan($it, '', 1000))) {
//    foreach($keys as $key) {
//        if (!preg_match("/^record\_[0-9]+/", $key)) {
//            $redis->del($key);
//        }
//    }
//}
//
//$abc = array(
//    array(
//        'aa'=>'11',
//        'bb'=>'22'
//    ),
//    array(
//        'aa'=>'33',
//        'bb'=>'44'
//    )
//);
//
//foreach($abc as &$val) {
//    $val['cc'] = $val['aa'];
//    unset($val['aa']);
//}
//
//print_r($abc);
//*/
//
///*
//$time1 = microtime();
//
//$value = "abc";
//$string = '';
//
//$string .= 'hello:' . $value . $value . $value . $value . $value;
//sleep(1);
//
//$time2 = microtime();
//
//echo $time2 - $time1;
//
//echo "<br/>";
//
//$time1 = microtime();
//
//$string = '';
//
//$string .= "hello:{$value}{$value}{$value}{$value}{$value}";
//sleep(1);
//
//$time2 = microtime();
//
//echo $time2 - $time1;*/
//
//class User
//{
//    public $username = '';
//    private $password = '';
//
//    public function __construct($username, $password)
//    {
//        $this->username = $username;
//        $this->password = $password;
//    }
//
//    public function __get($name) {
//        return $this->$name;
//    }
//
//}
//
//function obj2json($object) {
//    $ref = new ReflectionClass($object);
//    $data = [];
//    foreach($ref->getDefaultProperties() as $key => $val) {
//        $data[$key] = $object->$key;
//    }
//    return json_encode($data);
//}
//
//$user = new User('memory', '123456');
//
//echo serialize($user);
//
//echo obj2json($user);

$server = server_http_server('0.0.0.0', 9900);

$server->on('request', function($request, $response){
   echo "呵呵";
});
