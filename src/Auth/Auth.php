<?php
namespace Bid\Auth;

use Bid\Models\User;

class Auth
{
    public function attemp($username, $password)
    {
        $user = User::where("username", $username)->first();
        if (! $user) {
            return false;
        }
        if (password_verify($password, $user->password)) {
            $_SESSION["user_bid"] = $user->toArray();
            return true;
        }
        return false;
    }

    public function user() {
        return $_SESSION["user_bid"];
    }

    public function logout() {
        return session_destroy();
    }

    public function check() {
        if (isset($_SESSION['user_bid'])) {
            return true;
        }
        return false;
    }
}