<?php
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
if($_SERVER['REQUEST_METHOD']!=='POST'||!verify_csrf()){http_response_code(403);exit('Request could not be verified.');}
$_SESSION=[];
if(ini_get('session.use_cookies')){$params=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$params['path'],$params['domain'],$params['secure'],$params['httponly']);}
session_destroy();header('Location: ' . app_url('/'));exit;
