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

    public function drawresultlist_get() {
        //Authentication part        
        $headers = $this->input->request_headers();

        // log_message('info','USER_INFO '.print_r($_REQUEST,TRUE));

        // $req_dump = print_r($headers, TRUE);
        // $fp = fopen('request.log', 'a');
        // fwrite($fp, $req_dump);
        // fclose($fp);

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
        /*
        if (array_key_exists('authorization', $headers) && !empty($headers['authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['authorization']);
            if ($decodedToken == false) {
                $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
                return;
            } else {
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
        } else {
            $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
        }*/
    }

    function permute($arg) {
        $array = is_string($arg) ? str_split($arg) : $arg;
        if(1 === count($array))
            return $array;
        $result = array();
        foreach($array as $key => $item)
            foreach($this->permute(array_diff_key($array, array($key => $item))) as $p)
                $result[] = $item . $p;
        return $result;
    }

    function remove_repetition($arg) {
        $taken = array();
        foreach($arg as $key => $item) {
            if(!in_array($item, $taken)) {
                $taken[] = $item;
            } else {
                unset($item);
            }
        }
        return $taken;
    }
    public function submitquickpick_post()
    {
        //Authentication part        
        $headers = $this->input->request_headers();
        
        /**debug track */
        // log_message('info','USER_INFO '.print_r($_REQUEST,TRUE));

        // $req_dump = print_r($headers, TRUE);
        // $fp = fopen('request.log', 'a');
        // fwrite($fp, $req_dump);
        // fclose($fp);
        /**debug track */
        
        // $agent_id = '';
        // if (array_key_exists('authorization', $headers) && !empty($headers['authorization'])) {
            
        //     $decodedToken = AUTHORIZATION::validateToken($headers['authorization']);
        //     $agent_id = $decodedToken->id;
        //     if ($decodedToken == false) {
        //         $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
        //         return;
        //     }
        // }

        $agent_id = '273';

        $bet_number = $this->input->post('bet_number');
        $bet_type = $this->input->post('bet_type');
        $bet_amount = $this->input->post('bet_amount');
       
        /** get date of Sever  */ 
        $date = new DateTime();
        $datetime = $date->format('Y-m-d H:i:s');
        $bet_date = $date->format('Y-m-d');

        $datetime1 = $date->format('H');
        $date_input1 = $datetime1;

        if($date_input1<'11')
        {
            $bet_draw='11AM';
        }
        elseif($date_input1>='11' && $date_input1<'16')
        {
            $bet_draw='4PM';
        }
        elseif($date_input1>='16' && $date_input1<'21')
        {
            $bet_draw='9PM';
        }
        else
        {
            $bet_draw='NODRAW';
        }

        
        /**  End of Get Sever Time    */

        if ($bet_type == 1) { // Rambolito
            $power_set = $this->permute(str_split($bet_number));
            $bet_num_array = $this->remove_repetition($power_set);
            foreach($bet_num_array as $key => $bet_number) {
                $data = array(
                    'bet_created' => $datetime,
                    'bet_draw' => $bet_draw,
                    'bet_date' => $bet_date,
                    'bet_amt' => $bet_amount,
                    'bet_number' => $bet_number,
                    'bet_code' => 'bet_code_default',
                    'agent_id' => $agent_id,
                );
                $result = $this->drawresult_model->add_NewBet($data);
            }
            
        } else if ($bet_type == 0) {// Straight
            $data = array(
                'bet_created' => $datetime,
                'bet_draw' => $bet_draw,
                'bet_date' => $bet_date,
                'bet_amt' => $bet_amount,
                'bet_number' => $bet_number,
                'bet_code' => 'bet_code_default',
                'agent_id' => $agent_id,
            );
            $result = $this->drawresult_model->add_NewBet($data);
        }
        
        $return = array();
        if($result) {
            $return['success'] = true;
            $return['data'] = $result;
            $this->set_response($return, REST_Controller::HTTP_OK);
        } else {
            $return['success'] = false;
            $return['data'] = 'Sever Error!';
            $this->set_response($return, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
        return;
    }

    public function mypicklist_get() {

        $agent_id = '273';

        $start = $this->input->get('page');
        $limit = $this->input->get('per_page');

        $results = $this->drawresult_model->get_mypicklist($start*$limit - $limit, $limit, $agent_id);
        $return = array();

        if($results == 0){
            $this->set_response(array('flag' => '1'), REST_Controller::HTTP_OK);
        } else if($results){
            $this->set_response($results, REST_Controller::HTTP_OK);
        } else {
            $return['message'] = "Sever Error!";
            $this->set_response($return, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
         //Authentication part        
        // $headers = $this->input->request_headers();

        // if (array_key_exists('authorization', $headers) && !empty($headers['authorization'])) {
        //     $decodedToken = AUTHORIZATION::validateToken($headers['authorization']);
        //     $agent_id = $decodedToken->id;
        //     if ($decodedToken == false) {
        //         $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
        //     }
        //     else {
        //         //get results of draw
        //         $start = $this->input->get('page');
        //         $limit = $this->input->get('per_page');

        //         $results = $this->drawresult_model->get_mypicklist($start*$limit - $limit, $limit, $agent_id);
        //         $return = array();

        //         if($results == 0){
        //             $this->set_response(array('flag' => '1'), REST_Controller::HTTP_OK);
        //         } else if($results){
        //             $this->set_response($results, REST_Controller::HTTP_OK);
        //         } else {
        //             $return['message'] = "Sever Error!";
        //             $this->set_response($return, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        //         }
        //     }
        // } else {
        //     $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
        // }
        
    }
    
}


