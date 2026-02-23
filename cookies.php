<?php
$_COOKIE_name+"user";
$_COOKIE_value="chakkichakki@gmail.com";

setcookie($_COOKIE_name,$_COOKIE_value,time()+(86400*30),"/");

if(isset($_COOKIE[$_COOKIE_name])){
    echo $_COOKIE[$_COOKIE_name];
}
else{
    echo "cookie is not set";

}








?>