<?php

function gerarHash($s){
    $hash = password_hash($s, PASSWORD_DEFAULT);
    return $hash;
}

function testarHash($s, $hash){
    $ok = password_verify($s, $hash);
    return $ok;
}