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
        $this->db->insert('user_chat', $chat_data);
        return $this->db->insert_id();
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

    // public function get_broker_chats($broker_id)
    // {
    //     $this->db->where('chat_user_broker', $broker_id);
    //     $this->db->where('is_deleted', 0);
    //     $this->db->where('broker_is_deleted', 0);
    //     $chats = $this->db->get('user_chat')->result();

    //     $chats_with_messages = [];

    //     foreach ($chats as $chat) {
    //         // Verificar se existem mensagens para cada chat
    //         $this->db->where('chat_id', $chat->id);
    //         $this->db->where('is_deleted', 0);
    //         $messages = $this->db->get('user_chat_messages')->result();

    //         if (count($messages) > 0) {
    //             // Adicionar o chat à lista se houver pelo menos uma mensagem
    //             $chats_with_messages[] = $chat;
    //         }
    //     }

    //     return $chats_with_messages;
    // }

    public function get_broker_chats($broker_id, $query = null)
    {
        // Obter todos os chats do corretor que não foram deletados
        $this->db->where('chat_user_broker', $broker_id);
        $this->db->where('is_deleted', 0);
        $this->db->where('broker_is_deleted', 0);

        // Se uma consulta de pesquisa for fornecida, buscar o ID do cliente pelo nome
        if (!empty($query)) {
            $this->db->join('users', 'user_chat.chat_user_client = users.id');
            $this->db->like('users.user_name', $query);
            $this->db->select('user_chat.*'); // Selecionar apenas colunas da tabela user_chat para evitar conflito
        }

        $chats = $this->db->get('user_chat')->result();

        $chats_with_messages = [];

        foreach ($chats as $chat) {
            // Verificar se existem mensagens para cada chat
            $this->db->where('chat_id', $chat->id);
            $this->db->where('is_deleted', 0);
            $this->db->order_by('message_date', 'DESC');
            $messages = $this->db->get('user_chat_messages')->result();

            if (count($messages) > 0) {
                // Adicionar o chat à lista se houver pelo menos uma mensagem
                $chat->last_message_date = $messages[0]->message_date; // Obter a data da última mensagem
                $chats_with_messages[] = $chat;
            }
        }

        // Ordenar os chats pela data da última mensagem
        usort($chats_with_messages, function ($a, $b) {
            return strcmp($b->last_message_date, $a->last_message_date);
        });

        return $chats_with_messages;
    }

    public function get_client_chats($client_id, $query = null)
    {
        // Obter todos os chats do corretor que não foram deletados
        $this->db->where('chat_user_client', $client_id);
        $this->db->where('is_deleted', 0);
        $this->db->where('client_is_deleted', 0);

        // Se uma consulta de pesquisa for fornecida, buscar o ID do cliente pelo nome
        if (!empty($query)) {
            $this->db->join('users', 'user_chat.chat_user_broker = users.id');
            $this->db->like('users.user_name', $query);
            $this->db->select('user_chat.*'); // Selecionar apenas colunas da tabela user_chat para evitar conflito
        }

        $chats = $this->db->get('user_chat')->result();

        $chats_with_messages = [];

        foreach ($chats as $chat) {
            // Verificar se existem mensagens para cada chat
            $this->db->where('chat_id', $chat->id);
            $this->db->where('is_deleted', 0);
            $this->db->order_by('message_date', 'DESC');
            $messages = $this->db->get('user_chat_messages')->result();

            if (count($messages) > 0) {
                // Adicionar o chat à lista se houver pelo menos uma mensagem
                $chat->last_message_date = $messages[0]->message_date; // Obter a data da última mensagem
                $chats_with_messages[] = $chat;
            }
        }

        // Ordenar os chats pela data da última mensagem
        usort($chats_with_messages, function ($a, $b) {
            return strcmp($b->last_message_date, $a->last_message_date);
        });

        return $chats_with_messages;
    }


    public function search_broker_chats($broker_id, $query = null)
    {
        // Obter todos os chats do corretor que não foram deletados
        $this->db->where('chat_user_broker', $broker_id);
        $this->db->where('is_deleted', 0);
        $this->db->where('broker_is_deleted', 0);

        // Se uma consulta de pesquisa for fornecida, buscar o ID do cliente pelo nome
        if (!empty($query)) {
            $this->db->join('users', 'user_chat.chat_user_client = users.id');
            $this->db->like('users.user_name', $query);
            $this->db->select('user_chat.*'); // Selecionar apenas colunas da tabela user_chat para evitar conflito
        }

        $chats = $this->db->get('user_chat')->result();

        $chats_with_messages = [];

        foreach ($chats as $chat) {
            // Verificar se existem mensagens para cada chat
            $this->db->where('chat_id', $chat->id);
            $this->db->where('is_deleted', 0);
            $this->db->order_by('message_date', 'DESC');
            $messages = $this->db->get('user_chat_messages')->result();

            if (count($messages) > 0) {
                // Adicionar o chat à lista se houver pelo menos uma mensagem
                $chat->last_message_date = $messages[0]->message_date; // Obter a data da última mensagem
                $chats_with_messages[] = $chat;
            }
        }

        // Ordenar os chats pela data da última mensagem
        usort($chats_with_messages, function ($a, $b) {
            return strcmp($b->last_message_date, $a->last_message_date);
        });

        return $chats_with_messages;
    }

    public function search_client_chats($client_id, $query = null)
    {
        // Obter todos os chats do corretor que não foram deletados
        $this->db->where('chat_user_client', $client_id);
        $this->db->where('is_deleted', 0);
        $this->db->where('client_is_deleted', 0);

        // Se uma consulta de pesquisa for fornecida, buscar o ID do cliente pelo nome
        if (!empty($query)) {
            $this->db->join('users', 'user_chat.chat_user_broker = users.id');
            $this->db->like('users.user_name', $query);
            $this->db->select('user_chat.*'); // Selecionar apenas colunas da tabela user_chat para evitar conflito
        }

        $chats = $this->db->get('user_chat')->result();

        $chats_with_messages = [];

        foreach ($chats as $chat) {
            // Verificar se existem mensagens para cada chat
            $this->db->where('chat_id', $chat->id);
            $this->db->where('is_deleted', 0);
            $this->db->order_by('message_date', 'DESC');
            $messages = $this->db->get('user_chat_messages')->result();

            if (count($messages) > 0) {
                // Adicionar o chat à lista se houver pelo menos uma mensagem
                $chat->last_message_date = $messages[0]->message_date; // Obter a data da última mensagem
                $chats_with_messages[] = $chat;
            }
        }

        // Ordenar os chats pela data da última mensagem
        usort($chats_with_messages, function ($a, $b) {
            return strcmp($b->last_message_date, $a->last_message_date);
        });

        return $chats_with_messages;
    }


    // public function search_broker_chats($broker_id, $query)
    // {
    //     $this->db->where('chat_user_broker', $broker_id);
    //     $this->db->where('is_deleted', 0);
    //     $this->db->where('broker_is_deleted', 0);
    //     $chats = $this->db->get('user_chat')->result();

    //     $chats_with_messages = [];

    //     foreach ($chats as $chat) {
    //         // Verificar se existem mensagens para cada chat
    //         $this->db->where('chat_id', $chat->id);
    //         $this->db->where('is_deleted', 0);
    //         $messages = $this->db->get('user_chat_messages')->result();

    //         if (count($messages) > 0) {
    //             // Adicionar o chat à lista se houver pelo menos uma mensagem
    //             $chats_with_messages[] = $chat;
    //         }
    //     }

    //     return $chats_with_messages;
    // }

    public function add_chat_message($chat_data)
    {
        return $this->db->insert('user_chat_messages', $chat_data);
    }

    public function get_chat_message($chat_id)
    {
        $this->db->where('chat_id', $chat_id);
        $this->db->where('is_deleted', 0);

        return $this->db->get('user_chat_messages')->result();
    }

    public function get_chat_message_preview($chat_id)
    {
        $this->db->where('chat_id', $chat_id);
        $this->db->where('is_deleted', 0);
        $this->db->limit(1);
        $this->db->order_by('id', 'desc');

        return $this->db->get('user_chat_messages')->result();
    }
    public function unread_count($chat_id, $chat_user_broker)
    {

        $this->db->where('chat_id', $chat_id);
        $this->db->where('message_receiver_id', $chat_user_broker);
        $this->db->where('message_receiver_view', 0);

        $this->db->where('is_deleted', 0);

        $data = $this->db->get('user_chat_messages')->result();

        return count($data);
    }

    public function update_unread_count($chat_id, $user_id)
    {

        $this->db->where('chat_id', $chat_id);
        $this->db->where('message_receiver_id', $user_id);


        $data = array(
            'message_receiver_view' => 1
        );

        return $this->db->update('user_chat_messages', $data);
    }
}
