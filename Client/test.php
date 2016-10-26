<?php
/**
 * Created by PhpStorm.
 * User: linyulin
 * Date: 16/10/26
 * Time: 上午10:07
 */

include('JWT.php');

$secret = 'memory';

$payload = array(
    'sub'=>'token',
    'name'=>'hello1',
    'exp'=>time() + 3600
);


$jwt = JWT::encode($payload, $secret);


$jwt = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ0b2tlbiIsIm5hbWUiOiJoZWxsbyIsImV4cCI6MTQ3NzQ1NjQ3OX0.YzY1MTJkZTFjYzIxYWExNGMzNzZjOWQwMmU3N2U3OTIzMTYyMjVhYjU5MWZjMjg3YWVjMGUwOWQ3NTRhMTJmNw";
var_dump(JWT::verify($jwt, $secret));