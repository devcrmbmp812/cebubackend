<?php
	class User_model extends CI_Model{

		public function add_user($data){
			$this->db->insert('ci_users', $data);
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

	}

?>