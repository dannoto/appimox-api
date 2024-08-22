<?php

require APPPATH . '/libraries/REST_Controller.php';

require './vendor/autoload.php';

use ExpoSDK\Expo;
use ExpoSDK\ExpoMessage;


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

    public function index() {}


    function enviar_notificacao($recipients, $title, $body)
    {
        // Cria a mensagem da notificação
        $messages = [];

        foreach ($recipients as $recipient) {
            $messages[] = new ExpoMessage([
                'to' => $recipient,
                'title' => $title,
                'body' => $body,
            ]);
        }

        // Instancia a classe Expo
        $expo = new Expo();

        // Envia as mensagens
        try {
            $ok = $expo->send($messages)->push();
            return $ok;
        } catch (Exception $e) {
            return 'Erro ao enviar a notificação: ' . $e->getMessage();
        }
    }

    public function t_notificacao_get()
    {


        // Função para enviar a notificação push


        // Tokens de exemplo (certifique-se de que esses tokens são capturados corretamente no aplicativo)
        $tokens = [
            'ExponentPushToken[xxxxxxxxxxxxxx]', // Token Expo (geralmente para Android)
            'ExponentPushToken[yyyyyyyyyyyyyy]', // Token APNs (para iOS)
        ];

        // Chama a função para enviar a notificação
        $resultado = $this->enviar_notificacao($tokens, 'Título da Notificação', 'Corpo da Notificação');
        var_dump($resultado);
    }

    public function nf_new_favorit_get()
    {

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

        print_r($messages);

        /**
         * These recipients are used when ExpoMessage does not have "to" set
         */
        $defaultRecipients = [
            'ExponentPushToken[-z8PPVBsx930SUOHCbOcEX]',
            // 'ExponentPushToken[yyyy-yyyy-yyyy]'
        ];

        $ok = (new Expo)->send($messages)->to($defaultRecipients)->push();
        var_dump($ok);
    }
}
