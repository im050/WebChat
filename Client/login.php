<?php
include('JWT.php');
$host = 'localhost';
$user = 'root';
$pass = 'root';
$db_name = 'webchat';
$db_prefix = 'wc_';
$db = new mysqli($host, $user, $pass, $db_name);

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM ".table('users')." WHERE username = '{$username}'";

$query = $db->query($sql);

$res = $query->fetch_assoc();

if ($res) {
    if ($res['password'] == $password) {
        $payload = [
            'username'=>$username,
            'user_id'=>$res['id'],
            'exp'=>time() + 10,
            'iat'=>time()
        ];

        $access_token = JWT::encode($payload, 'memory');

        $data = [
            'access_token'=>$access_token,
            'status'=>true
        ];
    } else {
        $data = [
            'status'=>false,
            'msg'=>'Password Wrong.'
        ];

    }
} else {
    $data = [
        'status'=>false,
        'msg'=>'The user doesn\'t exists'
    ];
}

echo json_encode($data);

function table($table) {
    global $db_prefix;
    return $db_prefix . $table;
}
?>