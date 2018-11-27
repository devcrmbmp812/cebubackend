<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

/*
 * Changes:
 * 1. This project contains .htaccess file for windows machine.
 *    Please update as per your requirements.
 *    Samples (Win/Linux): http://stackoverflow.com/questions/28525870/removing-index-php-from-url-in-codeigniter-on-mandriva
 *
 * 2. Change 'encryption_key' in application\config\config.php
 *    Link for encryption_key: http://jeffreybarke.net/tools/codeigniter-encryption-key-generator/
 * 
 * 3. Change 'jwt_key' in application\config\jwt.php
 *
 */

class Auth extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/user_model', 'user_model');
    }

    /**
     * URL: http://localhost/CodeIgniter-JWT-Sample/auth/token
     * Method: GET
     */
    public function user_post()
    {
        $data = array(
            'email' => $this->input->post('username'),
            'firstname' => $this->input->post('firstname'),
            'lastname' => $this->input->post('lastname'),
            'password' =>  password_hash($this->input->post('password'), PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d : h:m:s'),
            'updated_at' => date('Y-m-d : h:m:s'),
        );
        $data = $this->security->xss_clean($data);
        $user_id = $this->user_model->add_user($data);
        $tokenData = array();
        if($user_id){
            $tokenData['id'] = $user_id;
            $token = AUTHORIZATION::generateToken($tokenData);
            $output['token']['token'] = $token;
            $output['token']['id'] = $user_id;
            $output['message'] = '';

            $this->set_response($output, REST_Controller::HTTP_OK);
        }
        else {
            $output['message'] = "DB Write error!";
            $this->set_response($output, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function user_get()
    {
        $headers = $this->input->request_headers();

        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            if ($decodedToken != false) {
                $this->set_response($decodedToken, REST_Controller::HTTP_OK);
                return;
            }
        }

        $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
    }
    
}