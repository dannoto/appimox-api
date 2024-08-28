<?php
defined('BASEPATH') or exit('No direct script access allowed');


require './vendor/autoload.php';

require("./PHPMailer-master/src/PHPMailer.php");
require("./PHPMailer-master/src/SMTP.php");


class Email_model extends CI_Model
{

    /**
     * CONSTRUCTOR | LOAD DB
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('user_model');

    }


    public function send_user_recovery($user_email)
    {

        $user_data = $this->user_model->get_user_by_email($user_email);
        
        try {

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->IsSMTP(); // enable SMTP
            $mail->CharSet = "UTF-8";
            $mail->SMTPDebug = false; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "smtp.hostinger.com";
            $mail->Port = 465; // or 587
            $mail->IsHTML(true);
            $mail->Username = "contato@imo360brasil.com.br";
            $mail->Password = ";EPIPCe;6yE";
            $mail->SetFrom("contato@imo360brasil.com.br", "Imo360");

            $mail->Subject = 'Redefinição de Senha';

            $mail->AddAddress($user_data['user_email'], $user_data['user_name']);

            $mail->Body  = '

              testando...
            
            ';

            if ($mail->Send()) {
            
                return true;
            
            } else {

                return false;
            
            }

        } catch (Exception $e) {
            return false;
        }
    }

    // schedule

    public function broker_update_schedule($broker_data, $cliente_data, $schedule_data) {}
}
