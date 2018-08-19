<?php

function ping($addr) {
    $file = "{$global['systemRootPath']}cache/ping{$addr}.json";
    $lifetimeSeconds = 5;
    if (file_exists($file)) {
        $fileAge = time() - filemtime($file);
    } else {
        $fileAge = 0;
    }
    if ($fileAge > $lifetimeSeconds) {
        $addr = parse_url($addr);
        if (getenv("OS") == "Windows_NT") {
            exec("ping -n 1 {$addr['host']}", $output, $status);
            $average = end($output);
            $out = explode("=", $average);
            $average = intval(end($out));
        } else {
            $output = exec("ping -c 1 -s 64 -t 64 " . $addr['host']);
            $v = explode("=", $output);
            $array = explode("/", end($v));

            $average = floatval(@$array[1]);
        }
        $content = json_encode(array('value'=>$average, 'output'=>$output, 'addr'=>$addr));
        file_put_contents($file, $content);
    } else {
        $content = file_get_contents($file);
    }

    return json_decode($content);
}
