<?php
/**
 * 
 */
class Auth
{
    
    public static function autentica()
    {
        @session_start();
        $logged = $_SESSION['logado'];
        if ($logged == false) {
            session_destroy();
            header('Location: login/');
            exit;
        }
    }
    
}