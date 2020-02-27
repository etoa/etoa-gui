<?php

$mysqlHandle;

if (!function_exists('mysql_num_rows')) {
    function mysql_num_rows($res) {
        return mysqli_num_rows($res);
    }
}

if (!function_exists('mysql_fetch_row')) {
    function mysql_fetch_row($res) {
        return mysqli_fetch_row($res);
    }
}

if (!function_exists('mysql_fetch_all')) {
    function mysql_fetch_all($res) {
        return mysqli_fetch_all($res);
    }
}

if (!function_exists('mysql_fetch_array')) {
    function mysql_fetch_array($res) {
        return mysqli_fetch_array($res);
    }
}

if (!function_exists('mysql_fetch_assoc')) {
    function mysql_fetch_assoc($res) {
        return mysqli_fetch_assoc($res);
    }
}

if (!function_exists('mysql_num_fields')) {
    function mysql_num_fields($res) {
        return mysqli_num_fields($res);
    }
}

if (!function_exists('mysql_fetch_row')) {
    function mysql_fetch_row($res) {
        return mysqli_fetch_row($res);
    }
}

if (!function_exists('mysql_query')) {
    function mysql_query($query, $handle = null) {
        if (!$handle) {
            global $mysqlHandle;
            $handle = $mysqlHandle;
        }

        return mysqli_query($handle, $query);
    }
}

if (!function_exists('mysql_free_result')) {
    function mysql_free_result($res) {
        mysqli_free_result($res);
    }
}

if (!function_exists('mysql_fetch_field')) {
    function mysql_fetch_field($res) {
        return mysqli_fetch_field($res);
    }
}

if (!function_exists('mysql_connect')) {
    function mysql_connect($host = '', $user = '', $password = '', $database = '') {
        global $mysqlHandle;
        $hostParts = explode(':', $host, 2);
        if (count($hostParts) === 2) {
            $host = $hostParts[0];
            $port = (int) $hostParts[1];
        } else {
            $port = null;
        }

        $mysqlHandle = mysqli_connect($host, $user, $password, $database, $port);
        return $mysqlHandle;
    }
}

if (!function_exists('mysql_close')) {
    function mysql_close($mysqlHandle) {
        mysqli_close($mysqlHandle);
    }
}

if (!function_exists('mysql_get_client_info')) {
    function mysql_get_client_info() {
        mysqli_get_client_info();
    }
}

if (!function_exists('mysql_error')) {
    function mysql_error() {
        global $mysqlHandle;
        return mysqli_error($mysqlHandle);
    }
}

if (!function_exists('mysql_select_db')) {
    function mysql_select_db($dbname) {
        global $mysqlHandle;
        return mysqli_select_db($mysqlHandle, $dbname);
    }
}


if (!function_exists('mysql_insert_id')) {
    function mysql_insert_id() {
        global $mysqlHandle;
        return mysqli_insert_id($mysqlHandle);
    }
}

if (!function_exists('mysql_affected_rows')) {
    function mysql_affected_rows() {
        global $mysqlHandle;
        return mysqli_affected_rows($mysqlHandle);
    }
}

if (!function_exists('mysql_real_escape_string')) {
    function mysql_real_escape_string($escapestr) {
        global $mysqlHandle;
        return mysqli_real_escape_string($mysqlHandle, $escapestr);
    }
}

if (!function_exists('mysql_result')) {
    function mysql_result($res, $offset) {
        $row = array_values(mysqli_fetch_row($res));

        return isset($row[$offset]) ? $row[$offset] : 0;
    }
}

if (!function_exists('mysql_field_name')) {
    function mysql_field_name($res, $offset) {
        $result = mysqli_fetch_field_direct($res, $offset);

        return $result ? $result->name : '';
    }
}
