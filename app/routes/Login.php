<?php

class Login
{
    public static function handle()
    {
        if (App::request('logout')) {
            App::logout();
            App::redirect('/');
        } else {
            $user = Database::query(
                'Select * from boocompta_user WHERE login = :login AND password = :password',
                array(':login' => App::request('login'), ':password' => App::request('password'))
            );
            
            if ($user && $user !== true) {
                App::login($user);
                App::defaultRoute();
            } else {
                App::redirect('/');
            }
        }
    }
}
