<?php
header("Access-Control-Allow-Origin: *");
// inclure functions.php
require_once "functions.php";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $url = $_SERVER["REQUEST_URI"];
    $url = trim($url, "\/");
    $url = explode("/", $url);
    $action = $url[1];

    if ($action == "getuserlist") {
        getListUser();
    }
} else {
    // ce que l'utilisateur envvoi un formulaire on recupere
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data["action"] == "login") {
        // appel de la fonction login
        login($data['pseudo'], $data['password']);
    } else if ($data["action"] == "register") {
        // on fait appel a la fonction register pour enregistrer le user
        register($data['firstname'], $data['lastname'], $data['pseudo'], $data['password']);
    } else if ($data["action"] == "send message") {
        // appel de la fonction sendMessage
        sendMessage($data["expeditor"], $data['receiver'], $data['message']);
    } else {
        echo json_encode([
            "satus"     => 404,
            "message"   => "service not found"
        ]);
    }
}
