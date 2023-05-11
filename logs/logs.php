<?php

function logs($update)
{
    if (file_exists(ROOT . "/hook.json")) {
        $log = file_get_contents(ROOT . "/hook.json");
        $log = json_decode($log, true);
    } else {
        $log = [];
    }


    $t = explode(" ",microtime());
    $log[date("Y-m-d H:i:s", $t[1]).substr((string)$t[0],1,4)] = $update;
    $log = json_encode($log, JSON_UNESCAPED_UNICODE);
    file_put_contents(ROOT . "/hook.json", $log);
}


