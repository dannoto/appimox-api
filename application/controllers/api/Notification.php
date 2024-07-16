<?php

use ExpoSDK\Expo;
use ExpoSDK\ExpoMessage;

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Notification extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('user_model');
        $this->load->model('email_model');
        $this->load->model('broker_model');
        $this->load->model('location_model');
        $this->load->model('followers_model');
        $this->load->model('rating_model');
        $this->load->model('schedule_model');
        $this->load->model('partner_model');
        $this->load->model('property_model');
        $this->load->model('plans_model');
    }


    public function nf_new_favorit_post() {

                /**
         * Composed messages, see above
         * Can be an array of arrays, ExpoMessage instances will be made internally
         */
        $messages = [
            [
                'title' => 'Test notification',
                // 'to' => 'ExponentPushToken[-z8PPVBsx930SUOHCbOcEX]',
            ],
            new ExpoMessage([
                'title' => 'Notification for default recipients',
                'body' => 'Because "to" property is not defined',
            ]),
        ];

        /**
         * These recipients are used when ExpoMessage does not have "to" set
         */
        $defaultRecipients = [
            'ExponentPushToken[-z8PPVBsx930SUOHCbOcEX]]',
            'ExponentPushToken[yyyy-yyyy-yyyy]'
        ];

        (new Expo)->send($messages)->to($defaultRecipients)->push();

    }
}