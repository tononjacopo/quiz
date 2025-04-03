<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    session_unset();
    session_destroy();

    try {
        echo json_encode(["success" => true, "message" => "Logout effettuato"], JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {}

} else {
    http_response_code(405); // Method Not Allowed

    try {
        echo json_encode(["success" => false, "message" => "Invalid request method"], JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {}

}