<?php
extract($_POST);
session_start();
require_once                        __DIR__ . "/src/captcha.php";
$captcha                            = new captcha();

if (isset($carregaCaptcha) && $carregaCaptcha) {
    $captcha->base64                = True;
    $imagem                         = $captcha->captcha(300, 70, 5, 40);
    $_SESSION["cap"]                = $captcha->ultimo_texto;

    echo json_encode(
        array(
            "imagem"                => $imagem,
        )
    );
}

if (isset($validarCaptcha) && $validarCaptcha) {
    echo json_encode(
        array(
            "valido"                => 1
        )
    );
}