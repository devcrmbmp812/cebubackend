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
			$sql = 'SELECT *, (SELECT COUNT(agent_id) FROM bet WHERE bet_draw=drawtime AND bet_date=drawdate AND bet_number=result) AS winners FROM results ORDER BY drawdate DESC, drawtime ASC LIMIT '.$start.','.$limit;

            $query  = $this->db->query($sql);

            $return = array();
            foreach ($query->result() as $row)
                array_push($return, $row);
        
            return $return;
        }

		public function add_NewBet($data) {
			$this->db->insert('bet', $data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
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

		public function get_mypicklist($start = null, $limit = null, $agent_id){
			
			$sql = 'SELECT id, bet_draw, bet_date, bet_amt, bet_number FROM bet WHERE agent_id = '.$agent_id.' ORDER BY bet_date DESC, bet_draw ASC LIMIT '.$start.','.$limit;
			$query  = $this->db->query($sql);
			if($query->num_rows() == 0) {
				return 0;
			} else {
				$return = array();
				foreach ($query->result() as $row)
					array_push($return, $row);
				return $return;
			}
            
        }
	}

?>