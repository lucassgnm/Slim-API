<?php
require_once('src/PHPMailer.php');
require_once('src/SMTP.php');
require_once('src/Exception.php');
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/** * 
 * * Função para envio de emails
 * * @access public 
 * * @param String $para       -> Para quem será enviado
 * * @param String $assunto    -> Assunto do email
 * * @param String $corpocomhtml  -> Coprpo com HTML
 * * @param String $corposemhtml  -> Coprpo sem HTML
 * * @return void 
 * */

function EnviarEmail($para, $assunto, $corpocomhtml, $corposemhtml = "") {
    try {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = EMAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_USER;
        $mail->Password = EMAIL_PASS;
        $mail->Port = 587;
     
        $mail->setFrom('contato@slimws.tk', 'SlimApp Compras No-Reply Mailer');
        $mail->addAddress($para);
     
        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body = $corpocomhtml;
        $mail->AltBody = $corposemhtml;
     
        if($mail->send()) {
            echo 'Email enviado com sucesso';
        } else {
            echo 'Email nao enviado';
        }
    } catch (Exception $e) {
        echo "Erro ao enviar mensagem: {$mail->ErrorInfo}";
    }
}