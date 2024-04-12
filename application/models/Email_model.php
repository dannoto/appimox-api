<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Email_model extends CI_Model
{

    /**
     * CONSTRUCTOR | LOAD DB
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    /**
     * send email with recovery link
     * CONSTRUCTOR | LOAD DB
     */
    public function send_user_recovery($user_emnail)
    {
        return true;
    }
}
