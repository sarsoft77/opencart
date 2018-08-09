<?php

define('MY_FILE', DIR_APPLICATION . 'trace.html');

function mytrace($vars = FALSE) {
    $backtrace = debug_backtrace();
    if (strpos($backtrace[0]['file'], 'library/db.php') !== FALSE) {
        if (strpos($backtrace[1]['file'], 'library/session/db.php') === FALSE) {
            return;
        }
    }

    $tab = 10 * (count($backtrace) - 1);

    if ((strpos($backtrace[0]['file'], 'index.php') !== FALSE) or
           (strpos($backtrace[0]['file'], 'exchange.php') !== FALSE)) {
        $str = "<div style=\"margin-left: ${tab}px; background-color: yellow; color: black; font-size: 100%; outline: 1px solid #CCC;\">";
        if (isset($_REQUEST['route']) && $_REQUEST['route'] == 'common/login') {
            unlink(MY_FILE);
            $str = '<!DOCTYPE html>
<html dir="ltr" lang="ru-RU">
<head>
<meta charset="UTF-8" />
        <title>MyTrace</title>' . $str;
        }
    } else {
        $str = "<div style=\"margin-left: ${tab}px; border: 1px; color: blue; font-size: 110%; border: 1px solid #CCC;\">";
    }

    $str .= date("Y-m-d H:i:s") . ' ' .
            $backtrace[0]['line'] . ' ' .
            str_replace(array(DIR_APPLICATION, DIR_SYSTEM), array('APP ', 'SYS '), $backtrace[0]['file']);

    if (isset($backtrace[1]['function'])) {
        $str .= ' ' . $backtrace[1]['function'];
    }

    if (isset($backtrace[1]['file'])) {
        $str .= ' (' . str_replace(array(DIR_APPLICATION, DIR_SYSTEM), array('APP ', 'SYS '), $backtrace[1]['file']) . ')';
    }

    if ((strpos($backtrace[0]['file'], 'index.php') !== FALSE) or ( strpos($backtrace[0]['file'], 'framework.php') !== FALSE)) {
        $str = '<b>' . $str . '</b>';
    }

    $mylog = $str . "\n";
    if ($vars) {
        //$newArray = compact($vars);
        $mylog .= "<div style=\"margin-left: 20px; color: green;\">";
        foreach ($vars as $key => $value) {
            $mylog .= $key . '=' . str_replace('[', '<br>[', print_r($value, TRUE)) . "<br>\n";
        }
        $mylog .= "</div>\n";
    }

    // Переменные выводимые всегда

    $vars2 = array(
//        '$_SERVER' => isset($_SERVER) ? $_SERVER : NULL
    );

    if ($vars2) {
        //$newArray = compact($vars);
        $mylog .= "<div style=\"margin-left: 20px; color: lightgreen;\">";
        foreach ($vars2 as $key => $value) {
            $mylog .= $key . '=' . str_replace('[', '<br>[', print_r($value, TRUE)) . "<br>\n";
        }
        $mylog .= "</div>\n";
    }

    $mylog .= "</div>\n";

    /*
      $mylog .= "REQUEST_URI=" . $_SERVER['REQUEST_URI'] . "<br>\n" ;
      $mylog .= "REQUEST_METHOD=" .  $_SERVER['REQUEST_METHOD'] . "<br>\n";
      $mylog .= "REQUEST_METHOD=" .  $_REQUEST['redirect'] . "<br>\n";

      ob_start();
      var_dump($_REQUEST);
      $out = ob_get_clean();
      $mylog .= $out . "<br>\n";
     */
    file_put_contents(MY_FILE, $mylog, FILE_APPEND | LOCK_EX);
}

function getVarName($var) {
    $tmp = array($var => '');
    $keys = array_keys($tmp);
    return trim($keys[0]);
}

?>