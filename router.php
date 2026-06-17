<?php
$uri = urldecode(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
if (strpos($_SERVER["REQUEST_URI"], '/api/') !== false) {
    if (!isset($_SERVER["HTTP_AUTHORIZATION"]) && function_exists("apache_request_headers")) {
        $headers = apache_request_headers();
        if (isset($headers["Authorization"])) {
            $_SERVER["HTTP_AUTHORIZATION"] = $headers["Authorization"];
        }
    }
    $_SERVER["SCRIPT_NAME"] = "/src/api/project_api.php";
    $_SERVER["PHP_SELF"] = "/src/api/project_api.php";
    require __DIR__ . "/src/api/project_api.php";
    return true;
}
if ($uri !== "/" && file_exists(__DIR__ . $uri)) {
    return false;
}
include __DIR__ . "/index.php";
