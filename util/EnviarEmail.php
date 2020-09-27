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

function EnviarEmail($para, $assunto, $corpocomhtml, $corposemhtml) {
    try {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = '';
        $mail->SMTPAuth = true;
        $mail->Username = '';
        $mail->Password = '';
        $mail->Port = 587;
     
        $mail->setFrom('contato@slimws.tk', 'No-Reply Mailer SlimApp');
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