<?php
namespace Tasteez\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

use PDO;

class Contact { 

  public function sendEmail($email = "", $name = "", $message = "") {
    if($email == "" || $name == "" || $message == "") {
        $errMsg = "Please specify ";
        $fields = array();
        $email == "" ? array_push($fields, "an email address") : "";
        $name == "" ? array_push($fields, "a name") : "";
        $message == "" ? array_push($fields, "a message") : "";
        for ($i=0; $i < sizeof($fields); $i++) { 
            if($i+1 == sizeof($fields)) {
                $errMsg .= $fields[$i] . ".";
            } else if($i == sizeof($fields)-2) {
                $errMsg .= $fields[$i] . ", and ";
            } else {
                $errMsg .= $fields[$i] . ", ";
            }
        }

        return array("message" => $errMsg, "status" => 400);
    } else {
        $mail = new PHPMailer();
        $from = getenv('EMAIL');
        $password = getenv('PASSWORD');
            try {
                $mail->Mailer = 'smtp';
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $from;
                $mail->Password = $password;
                $mail->Port = 587;

                $mail->setFrom($from);
                $mail->addAddress($from);
                $mail->Subject = "New email from user of Tasteez";
                $mail->Body = "New message from $name ($email).\n$message";

                $mail->send();
                return array("message" => "Email Successfully sent!", "status" => 200);
            } catch (Exception $e) {
                return array("message" => "Email Could not be sent!", "error" => $e, "status" => 500);
            }
        }
    }

}
