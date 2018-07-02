<?php 
 class Commonmodel extends CI_Model
 {
 	
 	public function insert($table,$data)
 	{
 		$this->db->insert($table,$data);
 	}
 	public function select($table)
 	{
 		$data = $this->db->get($table);
 		$result = $data->result_array();
 		return $result;
 	}
 	public function selectedit($table,$id)
 	{
 		$this->db->where('id',$id);
 		$data = $this->db->get($table);
 		$result = $data->result_array();
 		return $result;
 	}
 	public function editperformer($table,$datas,$id)
 	{	
 		print_r($datas);
 		$this->db->where('id',$id);
 		$this->db->update($table,$datas);
 		//$data = array('name'=>$_POST['name'],'email'=>$_POST['email'],'email_b'=>$_POST['email_b'],'mobile_a'=>$_POST['mobile_a'],'mobile_b'=>$_POST['mobile_b'],'category'=>$_POST['category'],'description'=>$_POST['description']);
 		
 		
 		
 	}
 }
?>