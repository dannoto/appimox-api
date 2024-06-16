<?php
defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Rating extends REST_Controller
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
    }

    public function add_rating_post()
    {

        // set validation rules
        $this->form_validation->set_rules('rating_schedule_id', 'ID do agendamento', 'trim|required');
        $this->form_validation->set_rules('rating_property_id', 'ID do Imóvel', 'trim|required');

        $this->form_validation->set_rules('rating_owner_id', 'ID do avaliador', 'trim|required');
        $this->form_validation->set_rules('rating_rated_id', 'ID do avaliado', 'trim|required');

        $this->form_validation->set_rules('rating_average_note', 'Nota Média', 'trim|required');
        // $this->form_validation->set_rules('rating_content', 'Conteúdo da Avaliação', 'trim|required');


        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $data['rating_schedule_id'] = $this->input->post('rating_schedule_id');
            $data['rating_property_id']    = $this->input->post('rating_property_id');
            $data['rating_owner_id'] = $this->input->post('rating_owner_id');
            $data['rating_rated_id']    = $this->input->post('rating_rated_id');
            $data['rating_average_note'] = $this->input->post('rating_average_note');
            $data['rating_content']    = $this->input->post('rating_content');
            $data['rating_date']    = date('Y-m-d H:i:s');
            $data['is_deleted']    = 0;


            if ($this->rating_model->check_rating($data['rating_schedule_id'],  $data['rating_property_id'],  $data['rating_owner_id'], $data['rating_rated_id'])) {

                $final['status'] = false;
                $final['message'] = 'Você já avaliou este agendamento.';
                $final['note'] = 'Você já avaliou este agendamento.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }

            $rating_id = $this->rating_model->add_rating($data);

            if ($rating_id) {

                // update main rating note
                $this->update_main_user_rating($data['rating_rated_id']);

                $schedule_data = array(
                    'schedule_avaliation' => $rating_id,
                    'schedule_status' => 4
                );

                if ($this->schedule_model->update_broker_schedule($data['rating_schedule_id'], $schedule_data)) {

                    $schedule_data_action['schedule_id'] = $data['rating_schedule_id'];
                    $schedule_data_action['schedule_action_id'] = 4;
                    $schedule_data_action['schedule_action_description'] = 'Cliente avaliou o agendamento';
                    $schedule_data_action['schedule_action_date'] = date('Y-m-d H:i:s');
                    $schedule_data_action['is_deleted'] = 0;

                    $this->schedule_model->add_schedule_action($schedule_data_action);
                }

                $final['status'] = true;
                $final['message'] = 'Avaliação enviada com sucesso!';
                $final['note'] = 'Avaliação enviada com sucesso!';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao enviar avaliação. Tente novamente.';
                $final['note'] = 'Erro ao enviar avaliação. Tente novamente.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }



    function update_main_user_rating($rating_rated_id)
    {

        $rating_data = $this->rating_model->get_broker_ratings($rating_rated_id);

        if ($rating_data) {

            if (count($rating_data) > 0) {

                $total_rating = 0;
                $count_rating = 0;

                foreach ($rating_data as $r) {

                    $total_rating = $total_rating + $r->rating_average_note;
                    $count_rating = $count_rating + 1;
                }

                if ($total_rating > 0) {

                    $rating = round(($total_rating / $count_rating), 1);
                } else {

                    $rating = 0;
                }
            } else {

                $rating = 0;
            }
        } else {

            $rating = 0;
        }

        $this->rating_model->update_user_rating($rating_rated_id, $rating);
    }

    public function get_ratings_post()
    {

        // set validation rules
        $this->form_validation->set_rules('user_id', 'ID do usuario', 'trim|required');

        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $user_id = $this->input->post('user_id');
            $user_ratings =  $this->rating_model->get_broker_ratings($user_id);

            if ($user_ratings) {

                $response = array();

                foreach ($user_ratings as $f) {

                    $response_a =  array();

                    $response_a['rating_data'] = $f;
                    $response_a['owner_data'] = $this->user_model->get_user($f->rating_owner_id);

                    $response[] = $response_a;
                }


                $final['status'] = true;
                $final['response'] = $response;
                $final['message'] = 'Avaliação encontradas com sucesso!';
                $final['note'] = 'Avaliação encontradas com sucesso!';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Nenhuma avaliação encontrada.' . $user_id;
                $final['note'] = 'Nenhuma avaliação encontrada.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }



    public function check_rating_partner_post()
    {

        $this->form_validation->set_rules('rating_partner_id', 'ID da parceria', 'trim|required');
        $this->form_validation->set_rules('rating_owner_id', 'ID do avaliador', 'trim|required');
        $this->form_validation->set_rules('rating_rated_id', 'ID do avaliado', 'trim|required');

        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $data['rating_partner_id'] = $this->input->post('rating_partner_id');
            $data['rating_owner_id']    = $this->input->post('rating_owner_id');
            $data['rating_rated_id'] = $this->input->post('rating_rated_id');

            $rating_id = $this->rating_model->check_rating_partner($data['rating_partner_id'], $data['rating_owner_id'], $data['rating_rated_id']);

            if ($rating_id) {


                    $final['status'] = true;
                    $final['message'] = 'Avaliação encontrada com sucesso!';
                    $final['note'] = 'Avaliação encontrada com sucesso!';

                    // user creation failed, this should never happen
                    $this->response($final, REST_Controller::HTTP_OK);
            

            } else {

                $check_able = $this->partner_model->check_able_to_rating($data['rating_partner_id']);


                if (strlen($check_able->partner_expiration) == 0) {

                    $final['status'] = true;
                    $final['message'] = 'Avaliação encontrada com sucesso!';
                    $final['note'] = 'Avaliação encontrada com sucesso!';

                    // user creation failed, this should never happen
                    $this->response($final, REST_Controller::HTTP_OK);

                } else {


                    $final['status'] = false;
                    $final['message'] = 'Erro encontrar avaliação. Tente novamente.';
                    $final['note'] = 'Erro encontrar avaliação. Tente novamente.';

                    // user creation failed, this should never happen
                    $this->response($final, REST_Controller::HTTP_OK);
                }


               
            }
        }
    }

    public function add_rating_partner_post()
    {

        // set validation rules
        $this->form_validation->set_rules('rating_partner_id', 'ID do Imóvel', 'trim|required');

        $this->form_validation->set_rules('rating_owner_id', 'ID do avaliador', 'trim|required');
        $this->form_validation->set_rules('rating_rated_id', 'ID do avaliado', 'trim|required');

        $this->form_validation->set_rules('rating_average_note', 'Nota Média', 'trim|required');
        // $this->form_validation->set_rules('rating_content', 'Conteúdo da Avaliação', 'trim|required');


        if ($this->form_validation->run() === false) {


            $final['status'] = false;
            $final['message'] = validation_errors();
            $final['note'] = 'Erro no formulário.';

            $this->response($final, REST_Controller::HTTP_OK);
        } else {

            // set variables from the form
            $data['rating_partner_id']    = $this->input->post('rating_partner_id');
            $data['rating_owner_id'] = $this->input->post('rating_owner_id');
            $data['rating_rated_id']    = $this->input->post('rating_rated_id');
            $data['rating_average_note'] = $this->input->post('rating_average_note');
            $data['rating_content']    = $this->input->post('rating_content');
            $data['rating_date']    = date('Y-m-d H:i:s');
            $data['rating_type'] = "partner";
            $data['is_deleted']    = 0;


            if ($this->rating_model->check_rating_partner($data['rating_partner_id'], $data['rating_owner_id'], $data['rating_rated_id'])) {

                $final['status'] = false;
                $final['message'] = 'Você já avaliou esta parceria.';
                $final['note'] = 'Você já avaliou esta parceria.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }

            $rating_id = $this->rating_model->add_rating($data);

            if ($rating_id) {

                $final['status'] = true;
                $final['message'] = 'Avaliação enviada com sucesso!';
                $final['note'] = 'Avaliação enviada com sucesso!';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            } else {

                $final['status'] = false;
                $final['message'] = 'Erro ao enviar avaliação. Tente novamente.';
                $final['note'] = 'Erro ao enviar avaliação. Tente novamente.';

                // user creation failed, this should never happen
                $this->response($final, REST_Controller::HTTP_OK);
            }
        }
    }
}
