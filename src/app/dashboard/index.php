<?php

use Lib\Auth\Auth;

$user = Auth::getInstance()->getPayload();

echo 'user: <pre>';
print_r($user);
echo '</pre>';

$userRole = $user->userRole->name;
echo "userRole: $userRole";
