<?php
	class Drawresult_model extends CI_Model{

        public function get_results($start = null, $limit = null){
            
            $this->db->select('id,drawtime, drawdate, result');
            $this->db->from('results');
            $this->db->order_by("drawdate", "desc");
            $this->db->order_by("drawtime", "asc");
            
            if($limit != ''){
                $this->db->limit($limit, $start);
            }
            $query  = $this->db->get();

            $return = array();
            foreach ($query->result() as $row)
                array_push($return, $row);
        
            return $return;
        }

		public function login($data){
			$query = $this->db->get_where('ci_users', array('email' => $data['email']));
			if ($query->num_rows() == 0){
				return false;
			}
			else{
				//Compare the password attempt with the password we have stored.
				$result = $query->row_array();
			    $validPassword = password_verify($data['password'], $result['password']);
			    if($validPassword){
			        return $result = $query->row_array();
			    }
			}
		}

	}

?>