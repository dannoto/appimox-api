

<?php

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Support extends REST_Controller
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

    public function get_support_categorias_post() 	{

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|required');


		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);

		} else {


            $response = array();

            foreach($this->plans_model->get_categorias() as $e) {

                $artigos = $this->plans_model->get_categorias_artigos($e->id);

                $e['artigos'] = $artigos;

                $response[] = $e;
            }


	
			if ($response) {

                $final['status'] = true;
                $final['response'] = $response;
				$final['message'] = 'Categorias encontrados com sucesso.';
				$final['note'] = 'Categorias encontrados com sucesso.';
				// login failed
				$this->response($final, REST_Controller::HTTP_OK);
			} else {

				$final['status'] = false;
				$final['message'] = 'Nenhum Categoriasencontrado.';
				$final['note'] = 'Nenhum Categoriasencontrado.';
				// login failed
				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

    public function get_support_artigos() 	{

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|required');


		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);

		} else {

	
			if ($this->plans_model->get_terms()) {

                $final['status'] = true;
                $final['response'] = $this->plans_model->get_terms();
				$final['message'] = 'Planos encontrados com sucesso.';
				$final['note'] = 'Planos encontrados com sucesso.';
				// login failed
				$this->response($final, REST_Controller::HTTP_OK);
			} else {

				$final['status'] = false;
				$final['message'] = 'Nenhum plano encontrado.';
				$final['note'] = 'Nenhum plano encontrado.';
				// login failed
				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}

    public function get_support_artigo() 	{

        $this->form_validation->set_rules('user_id', 'User ID', 'trim|required');


		if ($this->form_validation->run() == false) {

			$final['status'] = false;
			$final['message'] = validation_errors();
			$final['note'] = 'Erro no formulário.';

			$this->response($final, REST_Controller::HTTP_OK);

		} else {

	
			if ($this->plans_model->get_terms()) {

                $final['status'] = true;
                $final['response'] = $this->plans_model->get_terms();
				$final['message'] = 'Planos encontrados com sucesso.';
				$final['note'] = 'Planos encontrados com sucesso.';
				// login failed
				$this->response($final, REST_Controller::HTTP_OK);
			} else {

				$final['status'] = false;
				$final['message'] = 'Nenhum plano encontrado.';
				$final['note'] = 'Nenhum plano encontrado.';
				// login failed
				$this->response($final, REST_Controller::HTTP_OK);
			}
		}
	}
}