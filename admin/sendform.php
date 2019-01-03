<?php

require_once(__DIR__."/functions.php");

if (solveCaptcha() == true) {
    sendMessage();
}

function sendMessage()
{
    $from = "mm.mosko@gmail.com";
    $subject = $_POST['subject'];
    $to = $_POST['email'];
    $txt = $_POST['text'];

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    $headers .= 'From: Ing. Miroslav Moško <mm.mosko@gmail.com>' . "\r\n";
    $headers .= 'Cc: <'.$from.'>' . "\r\n";

    mail($to, $subject, $txt, $headers);

    file_put_contents("email.txt", "To: ".$to."\n\n Subject: ".$subject."\n\n From: ".$from."\n\n Txt: ".$txt."\n\n Headers: ".$headers);
}
