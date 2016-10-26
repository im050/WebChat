<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/26
 * Time: 上午10:07
 */

include('JWT.php');

$secret = 'memory';

echo "Welcome";

$payload = JWT::decode($_COOKIE['jwt'], $secret);

var_dump($payload);