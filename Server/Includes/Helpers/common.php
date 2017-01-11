<?php
function safeStr($str)
{
    return str_ireplace(array("<", ">"), array("&lt;", "&gt;"), $str);
}

function log_message($str, $show_time = true)
{
    if ($show_time) {
        $str = "[" . date("Y-m-d H:i:s", time()) . "] " . $str;
    }
    echo $str . "\r\n";
}