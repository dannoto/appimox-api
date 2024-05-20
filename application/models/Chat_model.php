<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chat_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function check_chat($chat_user_broker, $chat_user_client)
    {

        $this->db->where('chat_user_broker', $chat_user_broker);
        $this->db->where('chat_user_client', $chat_user_client);
        $this->db->where('is_deleted', 0);

        return $this->db->get('user_chat')->row();

    }

    public function add_chat($chat_data)
    {
        return $this->db->insert('user_chat', $chat_data);
        
    }
    
    public function get_chat($chat_id)
    {
        $this->db->where('id', $chat_id);
        $this->db->where('is_deleted', 0);
        return $this->db->insert('user_chat')->row();
        
    }

    public function get_client_chats_with_messages($client_id)
    {
        // Obter todos os chats do cliente que não foram deletados
        $this->db->where('chat_user_client', $client_id);
        $this->db->where('is_deleted', 0);
        $this->db->where('client_is_deleted', 0);
        $chats = $this->db->get('user_chat')->result();
    
        $chats_with_messages = [];
    
        foreach ($chats as $chat) {
            // Verificar se existem mensagens para cada chat
            $this->db->where('chat_id', $chat->id);
            $this->db->where('is_deleted', 0);
            $messages = $this->db->get('user_chat_messages')->result();
    
            if (count($messages) > 0) {
                // Adicionar o chat à lista se houver pelo menos uma mensagem
                $chats_with_messages[] = $chat;
            }
        }
    
        return $chats_with_messages;
    }
    
    public function get_broker_chats($broker_id)
    {
        $this->db->where('chat_user_broker', $broker_id);
        $this->db->where('is_deleted', 0);
        $this->db->where('broker_is_deleted', 0);
        $chats = $this->db->get('user_chat')->result();
    
        $chats_with_messages = [];
    
        foreach ($chats as $chat) {
            // Verificar se existem mensagens para cada chat
            $this->db->where('chat_id', $chat->id);
            $this->db->where('is_deleted', 0);
            $messages = $this->db->get('user_chat_messages')->result();
    
            if (count($messages) > 0) {
                // Adicionar o chat à lista se houver pelo menos uma mensagem
                $chats_with_messages[] = $chat;
            }
        }
    
        return $chats_with_messages;
        
    }

    public function add_chat_message() {
        
    }

}
