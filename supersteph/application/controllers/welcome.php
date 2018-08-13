<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once APPPATH.'/third_party/MPDF57/mpdf.php';

class Welcome extends CI_Controller {

	public function __construct(){

		parent::__construct();
		$this->load->model('Commonmodel','cmodel');		
     	$this->load->helper('url'); 
   		date_default_timezone_set('australia/brisbane');
   			 $config = Array(
          'protocol' => 'sendmail',  
          'mailtype' => 'html', 
          'charset' => 'utf-8',
          'wordwrap' => TRUE

      );


     //$message = $this->load->view('admin/ad_pages/send_email', '', true);  
     
     $this->load->library('email', $config);
   		
	}

	private function uploadImage($inputName,$uploads_dir,$max_width_thumb,$max_height_thumb){
    
	    $imgName = pathinfo($inputName['name']);
	    $ext = strtolower($imgName['extension']);
	    $filename = strtolower($imgName['filename']);
	    
	    $size = $inputName['size'];
	    $tmp_name = $inputName['tmp_name']; 
	    
	    $newImgName = $filename."_".rand(10,10000000).".".$ext;
	    $imagePath = $uploads_dir.$newImgName;
	      
	    $result_image = move_uploaded_file($tmp_name, $imagePath);
	    
	    if(!empty($result_image)){
	      
	      return $newImgName; 
	    } 
  	}  

	private function sendMail($from_email='', $to_email='', $message='', $subject='') {
		
	    $this->load->library('email');
	   
	    $this->email->from($from_email, 'Supersteph Test');
	    $this->email->to($to_email);
	    $this->email->subject($subject);
	    $this->email->message($message);

	    $result = $this->email->send();

	    return $result;
	}


	private function addHistory($type='', $subtype='', $message='') {

	   	$postData = array(
			'type'=> $type,
			'subtype'=> $subtype,
			'message'=> $message,
			'date'=>date('Y-m-d H:i:s'),
			'created_on'=>date('Y-m-d H:i:s'),
			'updated_on'=>date('Y-m-d H:i:s')
		);

		$result = $this->db->insert('histories',$postData);
	     
	    return true;
	}

	private function checkLogin(){
    
	    $session = $this->session->all_userdata();
	    if(empty($session['id'])){
	      redirect('welcome/login', 'refresh');
	    }
  	}

	public function index(){
		redirect('welcome/login', 'refresh');
	}

	

  	public function login(){

		$this->load->view('login');

		$jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    if(!empty($jsondata)){

	      $email = !empty($jsondata['email']) ? $jsondata['email'] : "";
	      $password = !empty($jsondata['password']) ? $jsondata['password'] : "";

	      $owner_total = $this->db->query("SELECT * FROM admin where email='$email' and password='$password';")->result_array();

	      if(!empty($owner_total)){ 
	        
	        $this->load->library('session');
	        $ownerdata = array( 
	          'id'  => $owner_total['0']['id'],
	          'name'  => $owner_total['0']['name'],
	          'email'  => $owner_total['0']['email']
	        );  
	        $session = $this->session->set_userdata($ownerdata);

	        $resultHistory = $this->addHistory('Login', '', 'Admin login successfully!');

	        echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Admin login successfully!")); die;
	      }
	      else{
	        echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Sorry, this username & password does not match!")); die;
	      }
	    }
	}


	
	public function categoryList(){

	    $this->checkLogin();  

	    $response = array();
	    $temp = array();

      	$category_details = $this->db->query("SELECT * FROM categories;")->result_array();
      
      	if(!empty($category_details)){
	        foreach($category_details  as $category_list){
	          
	          	$temp['id'] = !empty($category_list['id']) ? $category_list['id'] : "";
	          	$temp['category_name'] = !empty($category_list['category']) ? $category_list['category'] : "";
	          	
	          	$response[] =$temp;
	        }
      	}
  
      	if(!empty($response)){
        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Category List!", 'response' =>$response)); die;
      	}
      	else{
        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Category List!", 'response' =>$response)); die;
      	}  
  	}



 	public function performer(){

	    $this->load->view('header');
	    $this->load->view('performer');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();
	    
    	if(!empty($jsondata)){

	      	if(!empty($jsondata['searchtype'])){
	        	$searchdata = $jsondata['searchtype'];
	  		} 
	      	else{
	        	$searchdata = "";
	      	}

	      	if(!empty($searchdata)){
		        $performer_total = $this->db->query("SELECT count(*) as total FROM performers where (name like '%$searchdata%') ;")->result_array();
		        $total = !empty($performer_total[0]['total']) ? $performer_total[0]['total'] : 0;
	      	}
	      	else{
		        $performer_total = $this->db->query("SELECT count(*) as total FROM performers;")->result_array();
		        $total = !empty($performer_total[0]['total']) ? $performer_total[0]['total'] : 0;
	      	}
      
      		//Pagination Start
	      	$page=$jsondata['page'];
	      	$limit=$jsondata['limit'];
	      	$last=ceil($total/$limit);
	      	if($last<1)
	      	$last=1;
	      	if($page<1)
	      	$page=1;
	      	elseif($page>$last)
	      	$page=$last;
	      	$limitto = 'limit '.($page - 1) * $limit.','.$limit;
	      	//Pagination end
      
	      	$performer_details = $this->db->query("SELECT * FROM performers where (name like '%$searchdata%') order by id desc $limitto;")->result_array();
	      
	      	if(!empty($performer_details)){
		        foreach($performer_details  as $performer_list){
		          
		          	$temp['id'] = !empty($performer_list['id']) ? $performer_list['id'] : "";
		          	$temp['name'] = !empty($performer_list['name']) ? $performer_list['name'] : "";
		          	$temp['category_id'] = !empty($performer_list['category']) ? $performer_list['category'] : "";
		          	$temp['primary_email'] = !empty($performer_list['email']) ? $performer_list['email'] : "";
		          	$temp['secondary_email'] = !empty($performer_list['email_b']) ? $performer_list['email_b'] : "";
		          	$temp['primary_mobile'] = !empty($performer_list['mobile_a']) ? $performer_list['mobile_a'] : "";
		          	$temp['secondary_mobile'] = !empty($performer_list['mobile_b']) ? $performer_list['mobile_b'] : "";
		          	$temp['description'] = !empty($performer_list['description']) ? $performer_list['description'] : "";
		          	$temp['profile_image'] = !empty($performer_list['profile_image']) ? GET_PERFORMER_IMAGE_PATH.$performer_list['profile_image'] : GET_DUMMY_IMAGE_PATH.'dummy.png';

		          	$category = !empty($performer_list['category']) ? $performer_list['category'] : "";
					$category_details = explode(",",$category);
					$typeArray = array();
					$typeArray1 = array();


					if(!empty($category_details)){
						foreach($category_details as $category_list){
							
							$category_details = $this->db->query("SELECT * FROM categories where id='$category_list';")->result_array();
							$typeArray1[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}	
					}
					else{
						$typeArray1[] = "";
					}

					$temp['category'] = !empty($typeArray1) ? implode(",",$typeArray1) : "";

		          	$temp['booked_status'] = !empty($performer_list['booked_status']) ? $performer_list['booked_status'] : 0;
		          	$temp['block_status'] = !empty($performer_list['block_status']) ? $performer_list['block_status'] : 0;
		          	
		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Performer List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Performer List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function addperformer(){

	    $this->load->view('header');
	    $this->load->view('addperformer');
	    $this->load->view('footer');

	    $this->checkLogin();  
	    
	    if(!empty($_FILES)){
			$jsondata=$_POST['data'];	
		}
		else{
			$jsondata=file_get_contents("php://input");
			$jsondata=json_decode($jsondata,true);
		}	

	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$name = !empty($jsondata['name']) ? $jsondata['name'] : "";
	    	$category = !empty($jsondata['category_name']) ? implode(',', $jsondata['category_name']) : "";
	    	$email = !empty($jsondata['primary_email']) ? $jsondata['primary_email'] : "";
	    	$email_b = !empty($jsondata['secondary_email']) ? $jsondata['secondary_email'] : "";
	    	$mobile_a = !empty($jsondata['primary_mobile']) ? $jsondata['primary_mobile'] : "";
	    	$mobile_b = !empty($jsondata['secondary_mobile']) ? $jsondata['secondary_mobile'] : "";
	    	$description = !empty($jsondata['description']) ? $jsondata['description'] : "";
// or email_b='$email_b')
	    	$performer_detail = $this->db->query("SELECT count(*) as total FROM performers where email='$email' ;")->result_array();
	    	$total = !empty($performer_detail[0]['total']) ? $performer_detail[0]['total'] : "";

	    	//if($total=='0'){

	    		if(!empty($_FILES['file'])) {     
					$uploads_dir = UPLOAD_PERFORMER_IMAGE_PATH;
					$profile_image = $this->uploadImage($_FILES['file'],$uploads_dir, 50, 50); 
				}else{
					$profile_image = 'dummy.png';
				}

				$postData = array(
					'name'=> $name,
					'category'=> $category,
					'email'=> $email,
					'email_b'=>$email_b,
					'mobile_a'=>$mobile_a,
					'mobile_b'=>$mobile_b,
					'description'=>$description,
					'profile_image'=>$p