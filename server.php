#!/usr/bin/env php
<?php

spl_autoload_register(function($classname){
    require_once("classes" . DIRECTORY_SEPARATOR . $classname.".php");
});

try
{
    $params = getopt('', ['address::', 'port::', 'threads::']);

    $address = $params['address'] ?? '127.0.0.1';
    $port    = $params['port']    ?? 11111;
    $threads = $params['threads'] ?? 1;

    $acceptor = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if($acceptor === false) {
        throw new Exception("Socket created failed " . socket_strerror(socket_last_error()));
    }

    socket_set_option($acceptor, SOL_SOCKET, SO_REUSEADDR, 1);

    if (!socket_bind($acceptor, $address, $port))
        throw new Exception("Socket bind failed " . socket_strerror(socket_last_error()));

    if (!socket_listen($acceptor, 1))
        throw new Exception("Socket listen failed " . socket_strerror(socket_last_error()));

    for ($i = 0; $i < $threads; $i++)
    {

        $pid = pcntl_fork();
        if ($pid == 0)
        {

            while(true)
            {
                $socket = socket_accept($acceptor);

                try
                {
                    $message = socket_read($socket, 256);

                    if (!$message)
                    {
                       throw new Exception("Empty message");
                    }

                    if (($nameArray = json_decode($message, TRUE)) === null)
                    {
                        throw new Exception("Wrong format message");
                    }

                    if(!isset($nameArray['name']) 
                    or !isset($nameArray['surname']) 
                    or !isset($nameArray['maxlength']))
                    {
                        throw new Exception("no data in message");
                    }

                    $name      = $nameArray['name'];
                    $surname   = $nameArray['surname'];
                    $maxlength = $nameArray['maxlength'];

                }
                catch (Exception $e)
                {
                    socket_write($socket, '');
                    socket_close($socket);
                    echo "Wrong input: " . $e->getMessage() . "\n";
                    continue;
                }

                $getLogin = new GetLogin($name, $surname);
                $login = $getLogin->getLogin($maxlength);

                socket_write($socket, $login);
                socket_close($socket);
            }
        }
    }

    while (($cid = pcntl_waitpid(0, $status)) != -1)
    {
        $exitcode = pcntl_wexitstatus($status);
    }

    socket_close($acceptor);

}
catch (Exception $e)
{
    // There must be logging.
    echo "Fatal error: " . $e->getMessage() . "\n";
}


