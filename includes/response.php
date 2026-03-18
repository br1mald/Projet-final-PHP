<?php

function json_response($data, $status = 200)
{
    http_response_code($status);
    header("Content-Type: application/json");
    echo json_encode($data);
    exit();
}

function json_error($message, $status = 400)
{
    json_response(["error" => $message], $status);
}

?>
