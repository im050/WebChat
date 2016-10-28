<?php
function safeStr($str) {
    return str_ireplace(array("<", ">"), array("&lt;", "&gt;"), $str);
}