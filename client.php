#!/usr/bin/env php
<?php
 
    $params = getopt('', [
                            'address::',
                            'port::',
                            'name::',
                            'surname::',
                            'maxlength::']);

    $address   = $params['address']    ?? '127.0.0.1';
    $port      = $params['port']       ?? 11111;
    $name      = $params['name']       ?? '';
    $surname   = $params['surname']    ?? '';
    $maxlength = $params['maxlength']  ?? 20;

    if (!$name and !$surname)
    {
        die("empty name and surname\n");
    }

    $message = json_encode(array('name'=>$name, 'surname'=>$surname, 'maxlength'=>$maxlength));

    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if($socket === false) {
        die("Socket created failed " . socket_strerror(socket_last_error()) . "\n");
    }

    $connect = socket_connect($socket, $address, $port);
    if($connect === false) {
        die("Socket created failed " . socket_strerror(socket_last_error()) . "\n");
    }

    socket_write($socket, $message, strlen($message));

    $answer = "";
    while (($line = socket_read($socket, 2048)) !== "")
    {
        $answer .= $line;
    }

    echo "login = " . $answer . "\n";

    socket_close($socket);


