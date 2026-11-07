<?php
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
if (admin_logged_in()) { header('Location: ' . app_url('admin/index.php')); exit; }
$setup = !is_file(ADMIN_FILE); $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!verify_csrf()) $error='Your session expired. Refresh and try again.';
    elseif ($setup) {
        $password=(string)($_POST['password']??''); $confirm=(string)($_POST['confirm']??'');
        if (strlen($password)<12) $error='Choose a password with at least 12 characters.';
        elseif ($password!==$confirm) $error='The passwords do not match.';
        else {
            if (!write_json(ADMIN_FILE,['password_hash'=>password_hash($password,PASSWORD_DEFAULT)])) $error='Could not save admin setup.';
            else { session_regenerate_id(true); $_SESSION['admin']=true; header('Location: ' . app_url('admin/index.php')); exit; }
        }
    } else {
        $attempts=(int)($_SESSION['login_attempts']??0); $last=(int)($_SESSION['login_last']??0);
        if($attempts>=6 && time()-$last<900) $error='Too many attempts. Wait 15 minutes, then try again.';
        else {
            $credentials=read_json(ADMIN_FILE,[]); $hash=(string)($credentials['password_hash']??'');
            if($hash!=='' && password_verify((string)($_POST['password']??''),$hash)) {
                session_regenerate_id(true); $_SESSION['admin']=true; $_SESSION['login_attempts']=0; header('Location: ' . app_url('admin/index.php')); exit;
            }
            $_SESSION['login_attempts']=$attempts+1; $_SESSION['login_last']=time(); $error='Password not recognized.';
        }
    }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $setup?'Admin setup':'Admin login'?> — Portfolio</title><link rel="stylesheet" href="/assets/css/admin.css"></head><body class="admin-login"><main class="login-card"><a class="admin-back" href="/">↙ BACK TO PORTFOLIO</a><span class="section-kicker">PRIVATE AREA / 00<?= $setup?'1':'2'?></span><h1><?= $setup?'SET UP':'ADMIN' ?><br><span><?= $setup?'YOUR ACCESS':'ACCESS.'?></span></h1><p><?= $setup?'Create a private password for managing your portfolio. It is stored as a one-way hash.':'Sign in to edit the portfolio content and images.'?></p><?php if($error):?><div class="notice error"><?=h($error)?></div><?php endif;?><form method="post" autocomplete="off"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><label>Password<input type="password" name="password" minlength="12" required autocomplete="new-password"></label><?php if($setup):?><label>Confirm password<input type="password" name="confirm" minlength="12" required autocomplete="new-password"></label><?php endif;?><button class="admin-button" type="submit"><?= $setup?'CREATE ADMIN ACCESS':'SIGN IN'?> ↗</button></form><small>Admin access requires this password even when opened with the portfolio shortcut.</small></main></body></html>
