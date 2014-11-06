<?php

setcookie("server_cookie_1", "one");
setcookie("server_cookie_2", "two");
setcookie("server_cookie_3", "three");
var_dump($_SERVER);
var_dump($_COOKIE);
