<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Drawresult extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api/drawresult_model', 'drawresult_model');
    }

    public function drawresultlist_get()
    {
        //Authentication part        
        $headers = $this->input->request_headers();

        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            if ($decodedToken == false) {
                $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
                return;
            }
        }
        //get results of draw
        
        $start = $this->input->get('page');
        $limit = $this->input->get('per_page');

        $results = $this->drawresult_model->get_results($start*$limit - $limit, $limit);
        //$results = array("drawtime"=>"11AM", "drawdate"=>"2017-09-25", "result"=>"105");
        $return = array();
        if($results){
            $this->set_response($results, REST_Controller::HTTP_OK);
        }
        else {
            $return['message'] = "Sever Error!";
            $this->set_response($return, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
}