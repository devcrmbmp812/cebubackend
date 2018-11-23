<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

    require(APPPATH.'/libraries/REST_Controller.php');

    use Restserver\Libraries\REST_Controller;
	class Auth extends REST_Controller {

		public function __construct(){
			parent::__construct();
			$this->load->model('admin/auth_model', 'auth_model');
		}

        public function create_jwt(){
            $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

            // Create token payload as a JSON string
            $payload = json_encode(['user_id' => 123]);

            // Encode Header to Base64Url String
            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

            // Encode Payload to Base64Url String
            $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

            // Create Signature Hash
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);

            // Encode Signature to Base64Url String
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            // Create JWT
            $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

            return $jwt;
        }

        public function index_get()
        {
            echo "here is result of index api root";
        }

		public function user_get(){

            echo "here is reduce";

			// if($this->input->post('submit')){
			// 	$this->form_validation->set_rules('email', 'Email', 'trim|required');
			// 	$this->form_validation->set_rules('password', 'Password', 'trim|required');

			// 	if ($this->form_validation->run() == FALSE) {
			// 		$this->load->view('admin/auth/login');
			// 	}
			// 	else {
			// 		$data = array(
			// 		'email' => $this->input->post('email'),
			// 		'password' => $this->input->post('password')
			// 		);
			// 		$result = $this->auth_model->login($data);
			// 		if ($result == TRUE) {
			// 			$admin_data = array(
			// 				'admin_id' => $result['id'],
			// 			 	'name' => $result['username'],
			// 			 	'firstname' => $result['firstname'],
			// 			 	'lastname' => $result['lastname'],
			// 			 	'is_admin_login' => TRUE
			// 			);
			// 			$this->session->set_userdata($admin_data);
			// 			redirect(base_url('admin/dashboard'), 'refresh');
			// 		}
			// 		else{
			// 			$data['msg'] = 'Invalid Email or Password!';
			// 			$this->load->view('admin/auth/login', $data);
			// 		}
			// 	}
			// }
			// else{
			// 	$data['title'] = 'Login';
			// 	$this->load->view('admin/auth/login');
			// }
		}	
			
	}  // end class


?>