<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// functions to override codeigniter error throwing
function my_error_handler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)){
        // This error code is not included in error_reporting
        return;
    }
    log_message('error', "$errstr @$errfile::$errline($errno)" );
    throw new ErrorException( $errstr, $errno, 0, $errfile, $errline );
}
set_error_handler("my_error_handler");

function my_exception_handler($exception)
{
    echo '<pre>';
    print_r($exception->getMessage());
    echo '</pre>';
}
set_exception_handler("my_exception_handler");

function my_fatal_handler()
{
    $errfile = "unknown file";
    $errstr  = "Fatal error";
    $errno   = E_CORE_ERROR;
    $errline = 0;
    $error = error_get_last();
    if ($error !== NULL){
        echo '<pre>';
        print_r($error['message']);
        echo '</pre>';
    }
}
register_shutdown_function("my_fatal_handler");

function my_assert_handler($file, $line, $code)
{
    log_message('debug', "assertion failed @$file::$line($code)");
    throw new Exception("assertion failed @$file::$line($code)");
}
assert_options(ASSERT_ACTIVE,     1);
assert_options(ASSERT_WARNING,    0);
assert_options(ASSERT_BAIL,       0);
assert_options(ASSERT_QUIET_EVAL, 0);
assert_options(ASSERT_CALLBACK,   'my_assert_handler');

/**
*  Generate JWT for current login.
*
*  @param  string   $username   Username to be saved in payload.
*  @return string  $token       Generated token.
*/
function generateToken($username)
{
    $THIS = get_instance();
    $payload = array(
        'usr' => $username
    );
    $token = JWT::encode($payload, $THIS->config->item('jwt_key'));
    return $token;
}

/**
*  Validate JWT in the request.
*
*  @param  array    $headers        Headers in the request.
*  @return array    $decoded_token  Data from token.
*/
function validateToken($headers)
{
    $THIS = get_instance();
    if (isset($headers['Authorization'])) {
        try {
            $token = explode(' ', $headers['Authorization']);
            $token = $token[1];
            $decoded_token = JWT::decode($token, $THIS->config->item('jwt_key'));
            return $decoded_token;
        } catch (\Exception $e) {
            $THIS->response(null, 401);
        }
    } else {
        $THIS->response(null, 401);
    }
}