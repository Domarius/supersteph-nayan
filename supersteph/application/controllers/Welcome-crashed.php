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
					'profile_image'=>$profile_image,
					'created_on'=>date('Y-m-d H:i:s')
				);

	    		$result = $this->db->insert('performers',$postData);

	    		$historyMessage = "Admin added ".$name." performer details";
	    		$resultHistory = $this->addHistory('Performer', 'Add', $historyMessage);

		      	if(!empty($result)){
		        	echo json_encode(array('status'=>1, 'type'=>'success', "message" => "Performer added successfully!")); die;
		      	}
		      	else{
		        	echo json_encode(array('status'=>0, 'type'=>'error', "message" => "Performer not added!")); die;
		      	}
	    	//}
	    	// else{
	     //    	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Primary or Secondary email already exist!")); die;
	     //  	}
    	}
  	}

  	public function editperformer(){
	    $this->load->view('header');
	    $this->load->view('editperformer');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();
	    $typeArray = array();
		$typeArray1 = array();

    	if(!empty($jsondata)){

    		$id = !empty($jsondata['id']) ? $jsondata['id'] : "";

	      	$performer_details = $this->db->query("SELECT * FROM performers where id='$id';")->result_array();
	      
	      	if(!empty($performer_details)){
	        	foreach($performer_details  as $performer_list){
	          
		          	$temp['id'] = !empty($performer_list['id']) ? $performer_list['id'] : "";
		          	$temp['name'] = !empty($performer_list['name']) ? $performer_list['name'] : "";
		          	
		          	$temp['primary_email'] = !empty($performer_list['email']) ? $performer_list['email'] : "";
		          	$temp['secondary_email'] = !empty($performer_list['email_b']) ? $performer_list['email_b'] : "";
		          	$temp['primary_mobile'] = !empty($performer_list['mobile_a']) ? $performer_list['mobile_a'] : "";
		          	$temp['secondary_mobile'] = !empty($performer_list['mobile_b']) ? $performer_list['mobile_b'] : "";
		          	$temp['description'] = !empty($performer_list['description']) ? $performer_list['description'] : "";
		          	$temp['performer_image'] = !empty($performer_list['profile_image']) ? GET_PERFORMER_IMAGE_PATH.$performer_list['profile_image'] : GET_DUMMY_IMAGE_PATH.'dummy.png';
		          	$temp['old_performer_image'] = !empty($performer_list['profile_image']) ? GET_PERFORMER_IMAGE_PATH.$performer_list['profile_image'] : GET_DUMMY_IMAGE_PATH.'dummy.png';

		          	$category = !empty($performer_list['category']) ? $performer_list['category'] : "";
					$category_details = explode(",",$category);
					if(!empty($category_details)){
						foreach($category_details as $category_list){
							$typeArray1['id'] = !empty($category_list) ? $category_list : "";
							
							$category_details = $this->db->query("SELECT * FROM categories where id='$category_list';")->result_array();
							$typeArray1['category_name'] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
							
							$typeArray[] = $typeArray1;
						}	
					}
					else{
						$typeArray[] = "";
					}

					$temp['category_name'] = !empty($typeArray) ? $typeArray : "";
		         
		          	$response[] =$temp;
	        	}
	      	}
      
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Performer List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Performer List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function saveEditperformer(){

	    $this->load->view('header');
	    $this->load->view('editperformer');
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

	    	$categoryDetails = !empty($jsondata['category']) ? $jsondata['category'] : "";
	    	$categoryArray = array();
	    	if(!empty($categoryDetails)){
	    		foreach ($categoryDetails as $categoryList) {
	    			$categoryArray[] = !empty($categoryList['id']) ? $categoryList['id'] : "";
	    		}
	    	}

	    	$category_id = !empty($categoryArray) ? implode(',', $categoryArray) : "";

	    	$id = !empty($jsondata['id']) ? $jsondata['id'] : "";
	    	$name = !empty($jsondata['name']) ? $jsondata['name'] : "";
	    	$category = !empty($category_id) ? $category_id : "";
	    	$email = !empty($jsondata['primary_email']) ? $jsondata['primary_email'] : "";
	    	$email_b = !empty($jsondata['secondary_email']) ? $jsondata['secondary_email'] : "";
	    	$mobile_a = !empty($jsondata['primary_mobile']) ? $jsondata['primary_mobile'] : "";
	    	$mobile_b = !empty($jsondata['secondary_mobile']) ? $jsondata['secondary_mobile'] : "";
	    	$description = !empty($jsondata['description']) ? $jsondata['description'] : "";

	    	if(!empty($_FILES['file'])) {     
				$uploads_dir = UPLOAD_PERFORMER_IMAGE_PATH;
				$profile_image = $this->uploadImage($_FILES['file'],$uploads_dir, 50, 50); 

				$postData = array(
					'name'=> $name,
					'category'=> $category,
					'email'=> $email,
					'email_b'=> $email_b,
					'mobile_a'=> $mobile_a,
					'mobile_b'=> $mobile_b,
					'description'=> $description,
					'profile_image'=>$profile_image
				);
			}
			else{
				$postData = array(
					'name'=> $name,
					'category'=> $category,
					'email'=> $email,
					'email_b'=> $email_b,
					'mobile_a'=> $mobile_a,
					'mobile_b'=> $mobile_b,
					'description'=> $description
				);
			}

			$this->db->where('id',$id);
    		$result = $this->db->update('performers',$postData);

    		$historyMessage = "Admin edited ".$name." performer details";
    		$resultHistory = $this->addHistory('Performer', 'Add', $historyMessage);

	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Performer edited successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Performer not edited!")); die;
	      	}
    	}
  	}

  	public function blockPerformer(){

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$id = !empty($jsondata['id']) ? $jsondata['id'] : "";
	    	$blockStatus = !empty($jsondata['blockStatus']) ? $jsondata['blockStatus'] : "";

			$postData = array(
				'block_status'=> $blockStatus
			);

			$this->db->where('id',$id);
    		$result = $this->db->update('performers',$postData);

    		$performer_details = $this->db->query("SELECT * FROM performers  where id='$id';")->result_array();
	        $name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : 0;

    		$historyMessage = "Admin ".$blockStatus." ".$name." performer";
    		$resultHistory = $this->addHistory('Performer', 'Block', $historyMessage);

	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Admin change status successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Admin does not change status!")); die;
	      	}
    	}
  	}

  	public function viewbookingPerformer(){

	    $this->load->view('header');
	    $this->load->view('viewbookingPerformer');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$performer_id = !empty($jsondata['id']) ? $jsondata['id'] : "";

	      	$performer_total = $this->db->query("SELECT count(*) as total FROM assign_booking  where performer_id='$performer_id';")->result_array();
	        $total = !empty($performer_total[0]['total']) ? $performer_total[0]['total'] : 0;
      
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
      
	      	$performer_details = $this->db->query("SELECT * FROM assign_booking where performer_id='$performer_id' order by id desc $limitto;")->result_array();
	      
	      	if(!empty($performer_details)){
		        foreach($performer_details  as $performer_list){
		          
		          	$temp['id'] = !empty($performer_list['id']) ? $performer_list['id'] : "";
		          	$booking_request_id = !empty($performer_list['booking_request_id']) ? $performer_list['booking_request_id'] : "";

		          	$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id';")->result_array();
		        	$temp['name'] = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : 0;

		          	$temp['performer_id'] = !empty($performer_list['performer_id']) ? $performer_list['performer_id'] : "";
		          	$temp['status'] = !empty($performer_list['status']) ? $performer_list['status'] : "";
		          	$temp['reason'] = !empty($performer_list['reason']) ? $performer_list['reason'] : "";
		          	$temp['assign_date'] = !empty($performer_list['assign_date']) ? $performer_list['assign_date'] : "";
		          	
		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Performer List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Performer List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function performerName(){

	    $this->load->view('header');
	    $this->load->view('viewbookingPerformer');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$performer_id = !empty($jsondata['id']) ? $jsondata['id'] : "";

	      	$performer_list = $this->db->query("SELECT * FROM performers where id='$performer_id';")->result_array();
	      	$performer_name = !empty($performer_list[0]['name']) ? $performer_list[0]['name'] : "";
	      
	      	if(!empty($performer_name)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Performer name List!", 'response' =>$performer_name)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Performer name List!", 'response' =>$performer_name)); die;
	      	}
	    }
  	}

  	public function deleteperformer(){

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

    	if(!empty($jsondata)){

    		$performer_id = !empty($jsondata['id']) ? $jsondata['id'] : "";

    		$performer_details = $this->db->query("SELECT * FROM performers  where id='$performer_id';")->result_array();
	        $name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : 0;

    		$historyMessage = "Admin deleted ".$name." performer";
    		$resultHistory = $this->addHistory('Performer', 'Delete', $historyMessage);

	      	$this -> db -> where('performer_id', $performer_id);
  			$result1 = $this -> db -> delete('assign_booking');
  			
  			$this -> db -> where('id', $performer_id);
  			$result = $this -> db -> delete('performers');

	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Performer deleted successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Performer does not deleted!")); die;
	      	}
	    }
  	}




  	public function bookingRequest(){

	    $this->load->view('header');
	    $this->load->view('bookingRequest');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$filter = !empty($jsondata['filter']) ? $jsondata['filter'] : "";
    		$searchdata = !empty($jsondata['searchtype']) ? $jsondata['searchtype'] : "";
    		$startDate = !empty($jsondata['start_date']) ? date('Y-m-d H:i:s', strtotime($jsondata['start_date'])) : "";
    		$endDate = !empty($jsondata['end_date']) ? date('Y-m-d H:i:s', strtotime($jsondata['end_date'])) : "";

    		if($filter == '1'){
    			$currentDate = date("Y-m-d");
  				$oneDay = date('Y-m-d', strtotime($currentDate . ' +1 day'));
				$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$oneDay') ";
    		}
    		else if($filter == '3'){
    			$currentDate = date("Y-m-d");
  				$threeDay = date('Y-m-d', strtotime($currentDate . ' +3 day'));
				$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$threeDay') ";
    		}
    		else if($filter == '7'){
    			$currentDate = date("Y-m-d");
  				$sevenDay = date('Y-m-d', strtotime($currentDate . ' +7 day'));
				$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$sevenDay') ";
    		}
    		else{
    			$upcomingFilter="";
    		}

    		if(!empty($searchdata)){
	        	$searchFilter = "and (name like '%$searchdata%') or ( email like '%$searchdata%' ) or ( child_name like '%$searchdata%' ) or ( party_address like '%$searchdata%' ) or ( home_address like '%$searchdata%' )";
	  		} 
	      	else{
	        	$searchFilter = "";
	      	}

	      	if($startDate && $startDate!='' && $endDate && $endDate!=''){
				$daterange="and ( start_datetime >= '$startDate' and end_datetime <= '$endDate' )";
			}
			else{
				$daterange='';
			}


	      	if(!empty($searchFilter)){
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status!='Cancelled' and booking_status!='Completed' $searchFilter ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	      	}
	      	else if(!empty($upcomingFilter)){
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' $upcomingFilter ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	      	}
	      	else if(!empty($daterange)){
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' $daterange ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	      	}
	      	else{
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
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
      		
      		if(!empty($searchFilter)){
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' $searchFilter order by event_date ASC $limitto;")->result_array();
      		}
      		else if(!empty($upcomingFilter)){
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' $upcomingFilter order by event_date ASC $limitto;")->result_array();
      		}
      		else if(!empty($daterange)){
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' $daterange order by event_date ASC $limitto;")->result_array();
      		}
      		else{
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' order by event_date ASC $limitto;")->result_array();
      		}
	      	
	      
	      	if(!empty($booking_details)){
		        foreach($booking_details  as $booking_list){
		          
		          	$booking_id = !empty($booking_list['id']) ? $booking_list['id'] : "";

		          	$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
		          	$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
		          	$temp['email'] = !empty($booking_list['email']) ? $booking_list['email'] : "";
		          	$temp['mobile_number'] = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		          	$temp['event_date'] = !empty($booking_list['event_date']) ? date('d-m-Y', strtotime($booking_list['event_date'])) : "";
		          	
		          	$temp['show_time'] = !empty($booking_list['show_time']) ? date('h:i A', strtotime($booking_list['show_time'])) : "";
		          	$temp['duration'] = !empty($booking_list['duration']) ? $booking_list['duration'] : "";
		          	$temp['show_end_time'] = !empty($booking_list['show_end_time']) ? $booking_list['show_end_time'] : "";

		          	$temp['start_datetime'] = !empty($booking_list['start_datetime']) ? $booking_list['start_datetime'] : "";
		          	$temp['end_datetime'] = !empty($booking_list['end_datetime']) ? $booking_list['end_datetime'] : "";

		          	$temp['booking_status'] = !empty($booking_list['booking_status']) ? $booking_list['booking_status'] : "";
		          	$temp['assign_status'] = !empty($booking_list['assign_status']) ? $booking_list['assign_status'] : "";
		          	$temp['performer_count'] = !empty($booking_list['performer_count']) ? $booking_list['performer_count'] : 0;
		          	$temp['mail_count'] = !empty($booking_list['mail_count']) ? $booking_list['mail_count'] : 0;
		          	$temp['paperwork_count'] = !empty($booking_list['paperwork_count']) ? $booking_list['paperwork_count'] : 0;
		          	$temp['looking_count'] = !empty($booking_list['looking_count']) ? $booking_list['looking_count'] : 0;
		          	$temp['print_count'] = !empty($booking_list['print_count']) ? $booking_list['print_count'] : 0;

		          	$temp['event_amount'] = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : 0;
		          	$temp['paid_amount'] = !empty($booking_list['paid_amount']) ? $booking_list['paid_amount'] : 0;
		          	$remain=$booking_list['event_amount']-$booking_list['paid_amount'];
		          	$temp['remain_amount'] = $remain;	
		          	
		          	//$temp['remain_amount'] = !empty($booking_list['remain_amount']) ? $booking_list['remain_amount'] : 0;
		          	$temp['feedback_count'] = !empty($booking_list['feedback_count']) ? $booking_list['feedback_count'] : 0;

		          	$temp['add_amount_count'] = !empty($booking_list['add_amount_count']) ? $booking_list['add_amount_count'] : 0;

		          	$temp['currentDate'] = date('Y-m-d H:i:s');
		          	
	          		$cancel_details = $this->db->query("SELECT * FROM cancel_booking where booking_request_id='$booking_id';")->result_array();
					$temp['cancel_reason'] = !empty($cancel_details[0]['cancel_reason']) ? $cancel_details[0]['cancel_reason'] : "-";
		          	
  					
		          	$show_type = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";
					$show_type_details = explode(",",$show_type);
					$typeArray = array();
					
					if(!empty($show_type_details)){
						foreach($show_type_details as $show_type_list){
							
							$category_details = $this->db->query("SELECT * FROM categories where id='$show_type_list';")->result_array();
							$typeArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}	
					}
					else{
						$typeArray[] = "";
					}

					$temp['show_type'] = !empty($typeArray) ? implode(",",$typeArray) : "";
		          	
		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Request List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Request List!", 'response' =>$response)); die;
	      	}
	    }
  	}



  	public function bulkdownload(){

	    $this->load->view('header');
	    $this->load->view('bulkdownload');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$filter = !empty($jsondata['filter']) ? $jsondata['filter'] : "";
    		$searchdata = !empty($jsondata['searchtype']) ? $jsondata['searchtype'] : "";
    		$startDate = !empty($jsondata['start_date']) ? date('Y-m-d H:i:s', strtotime($jsondata['start_date'])) : "";
    		$endDate = !empty($jsondata['end_date']) ? date('Y-m-d H:i:s', strtotime($jsondata['end_date'])) : "";

    		if($filter == '1'){
    			$currentDate = date("Y-m-d");
  				$oneDay = date('Y-m-d', strtotime($currentDate . ' +1 day'));
				$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$oneDay') ";
    		}
    		else if($filter == '3'){
    			$currentDate = date("Y-m-d");
  				$threeDay = date('Y-m-d', strtotime($currentDate . ' +3 day'));
				$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$threeDay') ";
    		}
    		else if($filter == '7'){
    			$currentDate = date("Y-m-d");
  				$sevenDay = date('Y-m-d', strtotime($currentDate . ' +7 day'));
				$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$sevenDay') ";
    		}
    		else{
    			$upcomingFilter="";
    		}

    		if(!empty($searchdata)){
	        	$searchFilter = "and (name like '%$searchdata%') or ( email like '%$searchdata%' ) or ( child_name like '%$searchdata%' ) or ( party_address like '%$searchdata%' ) or ( home_address like '%$searchdata%' )";
	  		} 
	      	else{
	        	$searchFilter = "";
	      	}

	      	if($startDate && $startDate!='' && $endDate && $endDate!=''){
				$daterange="and ( start_datetime >= '$startDate' and end_datetime <= '$endDate' )";
			}
			else{
				$daterange='';
			}


	      	if(!empty($searchFilter)){
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status!='Cancelled' and booking_status!='Completed' $searchFilter ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	      	}
	      	else if(!empty($upcomingFilter)){
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' $upcomingFilter ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	      	}
	      	else if(!empty($daterange)){
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' $daterange ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	      	}
	      	else{
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
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
      		
      		if(!empty($searchFilter)){
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' $searchFilter order by event_date ASC $limitto;")->result_array();
      		}
      		else if(!empty($upcomingFilter)){
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' $upcomingFilter order by event_date ASC $limitto;")->result_array();
      		}
      		else if(!empty($daterange)){
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' $daterange order by event_date ASC $limitto;")->result_array();
      		}
      		else{
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status!='Cancelled'  and booking_status!='Completed' order by event_date ASC $limitto;")->result_array();
      		}
	      	
	      
	      	if(!empty($booking_details)){
		        foreach($booking_details  as $booking_list){
		          
		          	$booking_id = !empty($booking_list['id']) ? $booking_list['id'] : "";

		          	$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
		          	$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
		          	$temp['email'] = !empty($booking_list['email']) ? $booking_list['email'] : "";
		          	$temp['mobile_number'] = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		          	$temp['event_date'] = !empty($booking_list['event_date']) ? date('d-m-Y', strtotime($booking_list['event_date'])) : "";
		          	
		          	$temp['show_time'] = !empty($booking_list['show_time']) ? date('h:i A', strtotime($booking_list['show_time'])) : "";
		          	$temp['duration'] = !empty($booking_list['duration']) ? $booking_list['duration'] : "";
		          	$temp['show_end_time'] = !empty($booking_list['show_end_time']) ? $booking_list['show_end_time'] : "";

		          	$temp['start_datetime'] = !empty($booking_list['start_datetime']) ? $booking_list['start_datetime'] : "";
		          	$temp['end_datetime'] = !empty($booking_list['end_datetime']) ? $booking_list['end_datetime'] : "";

		          	$temp['booking_status'] = !empty($booking_list['booking_status']) ? $booking_list['booking_status'] : "";
		          	$temp['assign_status'] = !empty($booking_list['assign_status']) ? $booking_list['assign_status'] : "";
		          	$temp['performer_count'] = !empty($booking_list['performer_count']) ? $booking_list['performer_count'] : 0;
		          	$temp['mail_count'] = !empty($booking_list['mail_count']) ? $booking_list['mail_count'] : 0;
		          	$temp['paperwork_count'] = !empty($booking_list['paperwork_count']) ? $booking_list['paperwork_count'] : 0;
		          	$temp['looking_count'] = !empty($booking_list['looking_count']) ? $booking_list['looking_count'] : 0;
		          	$temp['print_count'] = !empty($booking_list['print_count']) ? $booking_list['print_count'] : 0;

		          	$temp['event_amount'] = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : 0;
		          	$temp['paid_amount'] = !empty($booking_list['paid_amount']) ? $booking_list['paid_amount'] : 0;
		          	$temp['remain_amount'] = !empty($booking_list['remain_amount']) ? $booking_list['remain_amount'] : 0;
		          	$temp['feedback_count'] = !empty($booking_list['feedback_count']) ? $booking_list['feedback_count'] : 0;

		          	$temp['add_amount_count'] = !empty($booking_list['add_amount_count']) ? $booking_list['add_amount_count'] : 0;

		          	$temp['currentDate'] = date('Y-m-d H:i:s');
		          	
	          		$cancel_details = $this->db->query("SELECT * FROM cancel_booking where booking_request_id='$booking_id';")->result_array();
					$temp['cancel_reason'] = !empty($cancel_details[0]['cancel_reason']) ? $cancel_details[0]['cancel_reason'] : "-";
		          	
  					
		          	$show_type = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";
					$show_type_details = explode(",",$show_type);
					$typeArray = array();
					
					if(!empty($show_type_details)){
						foreach($show_type_details as $show_type_list){
							
							$category_details = $this->db->query("SELECT * FROM categories where id='$show_type_list';")->result_array();
							$typeArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}	
					}
					else{
						$typeArray[] = "";
					}

					$temp['show_type'] = !empty($typeArray) ? implode(",",$typeArray) : "";
		          	
		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Request List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Request List!", 'response' =>$response)); die;
	      	}
	    }
  	}

	public function actionbookingRequest(){

	    $this->load->view('header');
	    $this->load->view('actionbookingRequest');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$booking_id = !empty($jsondata['id']) ? $jsondata['id'] : "";
    		
      		$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_id';")->result_array();
	      	
	      
	      	if(!empty($booking_details)){
		        foreach($booking_details  as $booking_list){
		          
		          	$booking_id = !empty($booking_list['id']) ? $booking_list['id'] : "";

		          	$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
		          	$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
		          	$temp['email'] = !empty($booking_list['email']) ? $booking_list['email'] : "";
		          	$temp['mobile_number'] = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		          	$temp['event_date'] = !empty($booking_list['event_date']) ? date('d-m-Y', strtotime($booking_list['event_date'])) : "";
		          	
		          	$temp['show_time'] = !empty($booking_list['show_time']) ? $booking_list['show_time'] : "";
		          	$temp['duration'] = !empty($booking_list['duration']) ? $booking_list['duration'] : "";
		          	$temp['show_end_time'] = !empty($booking_list['show_end_time']) ? $booking_list['show_end_time'] : "";

		          	$temp['start_datetime'] = !empty($booking_list['start_datetime']) ? $booking_list['start_datetime'] : "";
		          	$temp['end_datetime'] = !empty($booking_list['end_datetime']) ? $booking_list['end_datetime'] : "";

		          	$temp['booking_status'] = !empty($booking_list['booking_status']) ? $booking_list['booking_status'] : "";
		          	$temp['assign_status'] = !empty($booking_list['assign_status']) ? $booking_list['assign_status'] : "";
		          	$temp['performer_count'] = !empty($booking_list['performer_count']) ? $booking_list['performer_count'] : 0;
		          	$temp['mail_count'] = !empty($booking_list['mail_count']) ? $booking_list['mail_count'] : 0;
		          	$temp['paperwork_count'] = !empty($booking_list['paperwork_count']) ? $booking_list['paperwork_count'] : 0;
		          	$temp['looking_count'] = !empty($booking_list['looking_count']) ? $booking_list['looking_count'] : 0;
		          	$temp['print_count'] = !empty($booking_list['print_count']) ? $booking_list['print_count'] : 0;

		          	$temp['event_amount'] = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : 0;
		          	$temp['paid_amount'] = !empty($booking_list['paid_amount']) ? $booking_list['paid_amount'] : 0;
		          	$temp['remain_amount'] = !empty($booking_list['remain_amount']) ? $booking_list['remain_amount'] : 0;
		          	$temp['feedback_count'] = !empty($booking_list['feedback_count']) ? $booking_list['feedback_count'] : 0;

		          	$temp['add_amount_count'] = !empty($booking_list['add_amount_count']) ? $booking_list['add_amount_count'] : 0;

		          	$temp['currentDate'] = date('Y-m-d H:i:s');
		          	
	          		$cancel_details = $this->db->query("SELECT * FROM cancel_booking where booking_request_id='$booking_id';")->result_array();
					$temp['cancel_reason'] = !empty($cancel_details[0]['cancel_reason']) ? $cancel_details[0]['cancel_reason'] : "-";
		          	
  					
		          	$show_type = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";
					$show_type_details = explode(",",$show_type);
					$typeArray = array();
					
					if(!empty($show_type_details)){
						foreach($show_type_details as $show_type_list){
							
							$category_details = $this->db->query("SELECT * FROM categories where id='$show_type_list';")->result_array();
							$typeArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}	
					}
					else{
						$typeArray[] = "";
					}

					$temp['show_type'] = !empty($typeArray) ? implode(",",$typeArray) : "";
		          	
		          	$response[] =$temp;
		        }
	      	}
      
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Action Booking Request List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Action Booking Request List!", 'response' =>$response)); die;
	      	}
	    }
  	}


  	public function blockedbookingRequest(){

	    $this->load->view('header');
	    $this->load->view('blockedbookingRequest');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$filter = !empty($jsondata['filter']) ? $jsondata['filter'] : "";
    		$searchdata = !empty($jsondata['searchtype']) ? $jsondata['searchtype'] : "";
    		$startDate = !empty($jsondata['start_date']) ? date('Y-m-d H:i:s', strtotime($jsondata['start_date'])) : "";
    		$endDate = !empty($jsondata['end_date']) ? date('Y-m-d H:i:s', strtotime($jsondata['end_date'])) : "";
    		
    		if($filter == '1'){
    			$currentDate = date("Y-m-d");
  				$oneDay = date('Y-m-d', strtotime($showDate . ' +1 day'));
				$upcomingFilter = "and event_date='$oneDay' ";
    		}
    		else if($filter == '3'){
    			$currentDate = date("Y-m-d");
  				$threeDay = date('Y-m-d', strtotime($showDate . ' +3 day'));
				$upcomingFilter = "and event_date='$threeDay' ";
    		}
    		else if($filter == '5'){
    			$currentDate = date("Y-m-d");
  				$fiveDay = date('Y-m-d', strtotime($showDate . ' +5 day'));
				$upcomingFilter = "and event_date='$fiveDay' ";
    		}
    		else if($filter == '10'){
    			$currentDate = date("Y-m-d");
  				$tenDay = date('Y-m-d', strtotime($showDate . ' +10 day'));
				$upcomingFilter = "and event_date='$tenDay' ";
    		}
    		else{
    			$upcomingFilter="";
    		}

    		if(!empty($searchdata)){
	        	$searchFilter = "and (name like '%$searchdata%')";
	  		} 
	      	else{
	        	$searchFilter = "";
	      	}

	      	if($startDate && $startDate!='' && $endDate && $endDate!=''){
				$daterange="and ( start_datetime >= '$startDate' and end_datetime <= '$endDate' )";
			}
			else{
				$daterange='';
			}



	      	if(!empty($searchFilter)){
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status='Cancelled' $searchFilter ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	      	}
	      	else if(!empty($upcomingFilter)){
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status='Cancelled' $upcomingFilter ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	      	}
	      	else if(!empty($daterange)){
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status='Cancelled' $daterange ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	      	}
	      	else{
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status='Cancelled';")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
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
      		
      		if(!empty($searchFilter)){
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status='Cancelled' $searchFilter order by id desc $limitto;")->result_array();
      		}
      		else if(!empty($upcomingFilter)){
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status='Cancelled' $upcomingFilter order by id desc $limitto;")->result_array();
      		}
      		else if(!empty($daterange)){
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status='Cancelled' $daterange order by id desc $limitto;")->result_array();
      		}
      		else{
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status='Cancelled' order by id desc $limitto;")->result_array();
      		}
	      	
	      
	      	if(!empty($booking_details)){
		        foreach($booking_details  as $booking_list){
		          
		          	$booking_id = !empty($booking_list['id']) ? $booking_list['id'] : "";

		          	$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
		          	$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
		          	$temp['email'] = !empty($booking_list['email']) ? $booking_list['email'] : "";
		          	$temp['mobile_number'] = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		          	$temp['event_date'] = !empty($booking_list['event_date']) ? date('d-m-Y', strtotime($booking_list['event_date'])) : "";
		          	
		          	$temp['show_time'] = !empty($booking_list['show_time']) ? date('h:i A', strtotime($booking_list['show_time'])) : "";
		          	$temp['duration'] = !empty($booking_list['duration']) ? $booking_list['duration'] : "";
		          	$temp['show_end_time'] = !empty($booking_list['show_end_time']) ? $booking_list['show_end_time'] : "";

		          	$temp['start_datetime'] = !empty($booking_list['start_datetime']) ? $booking_list['start_datetime'] : "";
		          	$temp['end_datetime'] = !empty($booking_list['end_datetime']) ? $booking_list['end_datetime'] : "";

		          	$temp['booking_status'] = !empty($booking_list['booking_status']) ? $booking_list['booking_status'] : "";
		          	$temp['assign_status'] = !empty($booking_list['assign_status']) ? $booking_list['assign_status'] : "";
		          	$temp['performer_count'] = !empty($booking_list['performer_count']) ? $booking_list['performer_count'] : 0;
		          	$temp['mail_count'] = !empty($booking_list['mail_count']) ? $booking_list['mail_count'] : 0;
		          	$temp['paperwork_count'] = !empty($booking_list['paperwork_count']) ? $booking_list['paperwork_count'] : 0;
		          	$temp['looking_count'] = !empty($booking_list['looking_count']) ? $booking_list['looking_count'] : 0;
		          	$temp['print_count'] = !empty($booking_list['print_count']) ? $booking_list['print_count'] : 0;

		          	$temp['event_amount'] = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : 0;
		          	$temp['paid_amount'] = !empty($booking_list['paid_amount']) ? $booking_list['paid_amount'] : 0;
		          	$temp['remain_amount'] = !empty($booking_list['remain_amount']) ? $booking_list['remain_amount'] : 0;
		          	$temp['feedback_count'] = !empty($booking_list['feedback_count']) ? $booking_list['feedback_count'] : 0;

		          	$temp['add_amount_count'] = !empty($booking_list['add_amount_count']) ? $booking_list['add_amount_count'] : 0;

		          	$temp['currentDate'] = date('Y-m-d H:i:s');
		          	
	          		$cancel_details = $this->db->query("SELECT * FROM cancel_booking where booking_request_id='$booking_id';")->result_array();
					$temp['cancel_reason'] = !empty($cancel_details[0]['cancel_reason']) ? $cancel_details[0]['cancel_reason'] : "-";
		          	
  					
		          	$show_type = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";
					$show_type_details = explode(",",$show_type);
					$typeArray = array();
					
					if(!empty($show_type_details)){
						foreach($show_type_details as $show_type_list){
							
							$category_details = $this->db->query("SELECT * FROM categories where id='$show_type_list';")->result_array();
							$typeArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}	
					}
					else{
						$typeArray[] = "";
					}

					$temp['show_type'] = !empty($typeArray) ? implode(",",$typeArray) : "";
		          	
		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Request List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Request List!", 'response' =>$response)); die;
	      	}
	    }
  	}
  	public function archiveManager(){

	    $this->load->view('header');
	    $this->load->view('archieveManager');
	    $this->load->view('footer');
	}
	
	 	public function completedbookingRequest(){

	    $this->load->view('header');
	    $this->load->view('completedbookingRequest');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$filter = !empty($jsondata['filter']) ? $jsondata['filter'] : "";
    		$searchdata = !empty($jsondata['searchtype']) ? $jsondata['searchtype'] : "";
    		$startDate = !empty($jsondata['start_date']) ? date('Y-m-d H:i:s', strtotime($jsondata['start_date'])) : "";
    		$endDate = !empty($jsondata['end_date']) ? date('Y-m-d H:i:s', strtotime($jsondata['end_date'])) : "";
    		
    		if($filter == '1'){
    			$currentDate = date("Y-m-d");
  				$oneDay = date('Y-m-d', strtotime($showDate . ' +1 day'));
				$upcomingFilter = "and event_date='$oneDay' ";
    		}
    		else if($filter == '3'){
    			$currentDate = date("Y-m-d");
  				$threeDay = date('Y-m-d', strtotime($showDate . ' +3 day'));
				$upcomingFilter = "and event_date='$threeDay' ";
    		}
    		else if($filter == '5'){
    			$currentDate = date("Y-m-d");
  				$fiveDay = date('Y-m-d', strtotime($showDate . ' +5 day'));
				$upcomingFilter = "and event_date='$fiveDay' ";
    		}
    		else if($filter == '10'){
    			$currentDate = date("Y-m-d");
  				$tenDay = date('Y-m-d', strtotime($showDate . ' +10 day'));
				$upcomingFilter = "and event_date='$tenDay' ";
    		}
    		else{
    			$upcomingFilter="";
    		}

    		if(!empty($searchdata)){
	        	$searchFilter = "and (name like '%$searchdata%')";
	  		} 
	      	else{
	        	$searchFilter = "";
	      	}

	      	if($startDate && $startDate!='' && $endDate && $endDate!=''){
				$daterange="and ( start_datetime >= '$startDate' and end_datetime <= '$endDate' )";
			}
			else{
				$daterange='';
			}



	      	if(!empty($searchFilter)){
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status='Completed' $searchFilter ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	      	}
	      	else if(!empty($upcomingFilter)){
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status='Completed' $upcomingFilter ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	      	}
	      	else if(!empty($daterange)){
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status='Completed' $daterange ;")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	      	}
	      	else{
		        $booking_total = $this->db->query("SELECT count(*) as total FROM booking_request where booking_status='Completed';")->result_array();
		        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
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
      		
      		if(!empty($searchFilter)){
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status='Completed' $searchFilter order by id desc $limitto;")->result_array();
      		}
      		else if(!empty($upcomingFilter)){
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status='Completed' $upcomingFilter order by id desc $limitto;")->result_array();
      		}
      		else if(!empty($daterange)){
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status='Completed' $daterange order by id desc $limitto;")->result_array();
      		}
      		else{
      			$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status='Completed' order by id desc $limitto;")->result_array();
      		}
	      	
	      
	      	if(!empty($booking_details)){
		        foreach($booking_details  as $booking_list){
		          
		          	$booking_id = !empty($booking_list['id']) ? $booking_list['id'] : "";

		          	$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
		          	$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
		          	$temp['email'] = !empty($booking_list['email']) ? $booking_list['email'] : "";
		          	$temp['mobile_number'] = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		          	$temp['event_date'] = !empty($booking_list['event_date']) ? date('d-m-Y', strtotime($booking_list['event_date'])) : "";
		          	
		          	$temp['show_time'] = !empty($booking_list['show_time']) ? date('h:i A', strtotime($booking_list['show_time'])) : "";
		          	$temp['duration'] = !empty($booking_list['duration']) ? $booking_list['duration'] : "";
		          	$temp['show_end_time'] = !empty($booking_list['show_end_time']) ? $booking_list['show_end_time'] : "";

		          	$temp['start_datetime'] = !empty($booking_list['start_datetime']) ? $booking_list['start_datetime'] : "";
		          	$temp['end_datetime'] = !empty($booking_list['end_datetime']) ? $booking_list['end_datetime'] : "";

		          	$temp['booking_status'] = !empty($booking_list['booking_status']) ? $booking_list['booking_status'] : "";
		          	$temp['assign_status'] = !empty($booking_list['assign_status']) ? $booking_list['assign_status'] : "";
		          	$temp['performer_count'] = !empty($booking_list['performer_count']) ? $booking_list['performer_count'] : 0;
		          	$temp['mail_count'] = !empty($booking_list['mail_count']) ? $booking_list['mail_count'] : 0;
		          	$temp['paperwork_count'] = !empty($booking_list['paperwork_count']) ? $booking_list['paperwork_count'] : 0;
		          	$temp['looking_count'] = !empty($booking_list['looking_count']) ? $booking_list['looking_count'] : 0;
		          	$temp['print_count'] = !empty($booking_list['print_count']) ? $booking_list['print_count'] : 0;

		          	$temp['event_amount'] = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : 0;
		          	$temp['paid_amount'] = !empty($booking_list['paid_amount']) ? $booking_list['paid_amount'] : 0;
		          	$temp['remain_amount'] = !empty($booking_list['remain_amount']) ? $booking_list['remain_amount'] : 0;
		          	$temp['feedback_count'] = !empty($booking_list['feedback_count']) ? $booking_list['feedback_count'] : 0;

		          	$temp['add_amount_count'] = !empty($booking_list['add_amount_count']) ? $booking_list['add_amount_count'] : 0;

		          	$temp['currentDate'] = date('Y-m-d H:i:s');
		          	
	          		$cancel_details = $this->db->query("SELECT * FROM cancel_booking where booking_request_id='$booking_id';")->result_array();
					$temp['cancel_reason'] = !empty($cancel_details[0]['cancel_reason']) ? $cancel_details[0]['cancel_reason'] : "-";
		          	
  					
		          	$show_type = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";
					$show_type_details = explode(",",$show_type);
					$typeArray = array();
					
					if(!empty($show_type_details)){
						foreach($show_type_details as $show_type_list){
							
							$category_details = $this->db->query("SELECT * FROM categories where id='$show_type_list';")->result_array();
							$typeArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}	
					}
					else{
						$typeArray[] = "";
					}

					$temp['show_type'] = !empty($typeArray) ? implode(",",$typeArray) : "";
		          	
		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Request List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Request List!", 'response' =>$response)); die;
	      	}
	    }
  	}
	



  	public function addbookingRequest(){

	    $this->load->view('header');
	    $this->load->view('addbookingRequest');
	    $this->load->view('footer');

	    $this->checkLogin();  
	    
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){
$date_today=date('Y-m-d');
	    	$name = !empty($jsondata['name']) ? $jsondata['name'] : "";
	    	$email = !empty($jsondata['email']) ? $jsondata['email'] : "";	    	
	    	$event_amount = !empty($jsondata['event_amount']) ? $jsondata['event_amount'] : "";
	    	$party_address = !empty($jsondata['party_address']) ? $jsondata['party_address'] : "";
	    	$show_type_text = !empty($jsondata['show_type_text']) ? $jsondata['show_type_text'] : "";
	    	
	    	$mobile_number = !empty($jsondata['mobile_number']) ? $jsondata['mobile_number'] : "";
	    	$event_date = !empty($jsondata['event_date']) ? date('Y-m-d', strtotime($jsondata['event_date'])) : "";
	    	if($event_date < $date_today){
	    		$event_date = $date_today;
	    	}else{
	    		$event_date = !empty($jsondata['event_date']) ? date('Y-m-d', strtotime($jsondata['event_date'])) : "";
	    	  	}
	    	$show_type = !empty($jsondata['show_type']) ? implode(',', $jsondata['show_type']) : "";

	    	$show_time = !empty($jsondata['show_time']) ? date('H:i', strtotime($jsondata['show_time'])) : "";
	    	$duration = !empty($jsondata['duration']) ? $jsondata['duration'] : "";

	    	$start_datetime = $event_date.' '.$show_time;

	    	if($duration == '1'){
	    		$end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime) + 60*60);
				$show_end_time = date('H:i:s', strtotime($start_datetime) + 60*60);
	    	}
	    	else if($duration == '1.5'){
	    		$end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime) + 60*60*1.5);
				$show_end_time = date('H:i:s', strtotime($start_datetime) + 60*60*1.5);
	    	}
	    	else if($duration == '2'){
	    		$end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime) + 60*60*2);
				$show_end_time = date('H:i:s', strtotime($start_datetime) + 60*60*2);
	    	}
	    	else if($duration == '2.5'){
	    		$end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime) + 60*60*2.5);
				$show_end_time = date('H:i:s', strtotime($start_datetime) + 60*60*2.5);
	    	}
	    	else{
	    		$end_datetime = "";
	    		$show_end_time = "";
	    	}

			$postData = array(
				'name'=> $name,
				'email'=> $email,
				'mobile_number'=> $mobile_number,
				'event_date'=> $event_date,
				'event_amount'=> $event_amount,
				'remain_amount'=> $event_amount,
				'show_type'=> $show_type,
				'show_type_text'=> $show_type_text,
				'show_time'=> $show_time,
				'duration'=> $duration,
				'party_address' =>$party_address,
				'show_end_time'=> $show_end_time,
				'start_datetime'=> $start_datetime,
				'end_datetime'=> $end_datetime,
				'created_on'=>date('Y-m-d H:i:s'),
				'updated_on'=>date('Y-m-d H:i:s')
			);

    		$result = $this->db->insert('booking_request',$postData);
    		$id=$this->db->insert_id();
    		$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='DETAIL_FORM_REQUEST' ")->result_array();


	        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
	        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
	        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";
	        $show_time = !empty($jsondata['show_time']) ? date('H:i A', strtotime($jsondata['show_time'])) : "";
	        $categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

    		$categoryDetails = !empty($show_type) ? explode(',', $show_type) : "";
    		$showArray = array();
    		if(!empty($categoryDetails)){
    			foreach ($categoryDetails as $categoryList) {

    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
    			}
    		}
    		$showType = !empty($showArray) ? implode(', ', $showArray) : "";


			
	        $booking_id = !empty($id) ? base64_encode($id) : "";
			
	        $url = HTTP_CONTROLLER."generalAddBooking/?".$booking_id;
	        $dayOfWeek = date("l", strtotime($event_date));
					
	        $data = array('LINK' => $url,'NAME' => $name,'FEE' => $event_amount, 'SHOW_DATE' => $event_date, 'DAY' => $dayOfWeek, 'SHOW_START_TIME' => $show_time, 'SHOW_TYPE' => $showType,'FIRST_NAME'=>$name,'SHOW_TEXT'=>$show_type_text);
	        if(!empty($data)){
	        	foreach($data as $key => $value){
					$message = str_replace('{{'.$key.'}}', $value, $message);
				}
	       	}
	       	 $config = Array(
          'protocol' => 'sendmail',  
          'mailtype' => 'html', 
          'charset' => 'utf-8',
          'wordwrap' => TRUE

      );


     //$message = $this->load->view('admin/ad_pages/send_email', '', true);  
     
     $this->load->library('email', $config);
	 	 	$result_mail = $this->sendMail($from_email, $email, $message, $subject);

    		$historyMessage = "Admin added ".$name." booking request details";
    		$resultHistory = $this->addHistory('Booking Request', 'Add', $historyMessage);

	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Request added successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Booking Request not added!")); die;
	      	}
    	}
  	}

  	public function editbookingRequest(){

	    $this->load->view('header');
	    $this->load->view('editbookingRequest');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();
	    $typeArray = array();
		$typeArray1 = array();

    	if(!empty($jsondata)){

    		$id = !empty($jsondata['id']) ? $jsondata['id'] : "";

	      	$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id';")->result_array();
	      
	      	if(!empty($booking_details)){
	        	foreach($booking_details  as $booking_list){
	          
		          	$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
		          	$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
		          	$temp['email'] = !empty($booking_list['email']) ? $booking_list['email'] : "";
		          	//$temp['event_amount'] = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : "";
		          	$temp['mobile_number'] = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		          	$temp['show_type_text'] = !empty($booking_list['show_type_text']) ? $booking_list['show_type_text'] : "";
		          	$temp['event_date1'] = !empty($booking_list['event_date']) ? gmdate("Y-m-d\TH:i:s.000\Z", strtotime($booking_list['event_date'])) : "";
		          	
		          	$temp['show_time'] = !empty($booking_list['show_time']) ? gmdate('Y-m-d\TH:i:s.000\Z', strtotime($booking_list['show_time'])) : "";
		          	$temp['duration'] = !empty($booking_list['duration']) ? $booking_list['duration'] : "";

		          	$temp['hear_supersteph'] = !empty($booking_list['hear_supersteph']) ? $booking_list['hear_supersteph'] : "";
		          	$temp['party_bags'] = !empty($booking_list['party_bags']) ? $booking_list['party_bags'] : "";
		          	$temp['party_address'] = !empty($booking_list['party_address']) ? $booking_list['party_address'] : "";
		          	$temp['home_address'] = !empty($booking_list['home_address']) ? $booking_list['home_address'] : "";
		          	$temp['parking_facility'] = !empty($booking_list['parking_facility']) ? $booking_list['parking_facility'] : "";

		          	$temp['party_in_out'] = !empty($booking_list['party_in_out']) ? $booking_list['party_in_out'] : "";
		          	$temp['rain_plan'] = !empty($booking_list['rain_plan']) ? $booking_list['rain_plan'] : "";
		          	$temp['second_mobile'] = !empty($booking_list['second_mobile']) ? $booking_list['second_mobile'] : "";
		          	$temp['child_name'] = !empty($booking_list['child_name']) ? $booking_list['child_name'] : "";
		          	$temp['age'] = !empty($booking_list['age']) ? $booking_list['age'] : "";
			      	$temp['event_amount'] = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : "";		      	
		          	$temp['paid_amount'] = !empty($booking_list['paid_amount']) ? $booking_list['paid_amount'] : "";
		          	$remain=$booking_list['event_amount']-$booking_list['paid_amount'];
		          	$temp['remain_amount'] = $remain;	
		          	
			      //	$temp['remain_amount'] = !empty($booking_list['remain_amount']) ? $booking_list['remain_amount'] : "";	
		          	$temp['pay_amount'] = 0;			      	
		          	$temp['dob'] = !empty($booking_list['dob']) ? $booking_list['dob'] : "";
		          	$temp['children_party'] = !empty($booking_list['children_party']) ? $booking_list['children_party'] : "";
		          	$temp['gender_party'] = !empty($booking_list['gender_party']) ? $booking_list['gender_party'] : "";
		          	$temp['children_count'] = !empty($booking_list['children_count']) ? $booking_list['children_count'] : "";
		          	$temp['party_seen'] = !empty($booking_list['party_seen']) ? $booking_list['party_seen'] : "";
		          	$temp['show_fullname'] = !empty($booking_list['show_fullname']) ? $booking_list['show_fullname'] : "";

		          	$category = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";
					$category_details = explode(",",$category);
					if(!empty($category_details)){
						foreach($category_details as $category_list){
							$typeArray1['id'] = !empty($category_list) ? $category_list : "";
							
							$category_details = $this->db->query("SELECT * FROM categories where id='$category_list';")->result_array();
							$typeArray1['category_name'] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
							
							$typeArray[] = $typeArray1;
						}	 
					}
					else{
						$typeArray[] = "";
					}

					$temp['category_name'] = !empty($typeArray) ? $typeArray : "";
		          	
		          	$response[] =$temp;
	        	}
	      	}
      
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Request List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Request List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function saveEditBookingRequest(){

	    $this->load->view('header');
	    $this->load->view('editbookingRequest');
	    $this->load->view('footer');

	    $this->checkLogin();  
	    
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$show_typeDetails = !empty($jsondata['show_type']) ? $jsondata['show_type'] : "";
	    	$categoryArray = array();
	    	if(!empty($show_typeDetails)){
	    		foreach ($show_typeDetails as $show_typeList) {
	    			$categoryArray[] = !empty($show_typeList['id']) ? $show_typeList['id'] : "";
	    		}
	    	}

	    	$category_id = !empty($categoryArray) ? implode(',', $categoryArray) : "";
			//$show_type = !empty($category_id) ? $category_id : "";
	    	
	    	$id = !empty($jsondata['id']) ? $jsondata['id'] : "";
	    	$name = !empty($jsondata['name']) ? $jsondata['name'] : "";
	    	$email = !empty($jsondata['email']) ? $jsondata['email'] : "";

	    	$remain_amount = !empty($jsondata['remain_amount']) ? $jsondata['remain_amount'] : "";
	    	$paid_amount = !empty($jsondata['paid_amount']) ? $jsondata['paid_amount'] : "";
	    	$event_amount = !empty($jsondata['event_amount']) ? $jsondata['event_amount'] : "";
	    	$show_type_text = !empty($jsondata['show_type_text']) ? $jsondata['show_type_text'] : "";
	    	$mobile_number = !empty($jsondata['mobile_number']) ? $jsondata['mobile_number'] : "";
	    	$event_date = !empty($jsondata['event_date']) ? date('Y-m-d', strtotime($jsondata['event_date'])) : "";
	    	$show_type = !empty($category_id) ? $category_id : "";
	    	$show_time = !empty($jsondata['show_time']) ? date('H:i A', strtotime($jsondata['show_time'])) : "";
	    	$duration = !empty($jsondata['duration']) ? $jsondata['duration'] : "";

	    	$hear_supersteph = !empty($jsondata['hear_supersteph']) ? $jsondata['hear_supersteph'] : "";
	    	$party_bags = !empty($jsondata['party_bags']) ? $jsondata['party_bags'] : "";
	    	$party_address = !empty($jsondata['party_address']) ? $jsondata['party_address'] : "";
	    	$home_address = !empty($jsondata['home_address']) ? $jsondata['home_address'] : "";
	    	$parking_facility = !empty($jsondata['parking_facility']) ? $jsondata['parking_facility'] : "";
	    	$party_in_out = !empty($jsondata['party_in_out']) ? $jsondata['party_in_out'] : "";

	    	$rain_plan = !empty($jsondata['rain_plan']) ? $jsondata['rain_plan'] : "";
	    	$second_mobile = !empty($jsondata['second_mobile']) ? $jsondata['second_mobile'] : "";
	    	$child_name = !empty($jsondata['child_name']) ? $jsondata['child_name'] : "";
	    	$age = !empty($jsondata['age']) ? $jsondata['age'] : "";
	    	$dob = !empty($jsondata['dob']) ? $jsondata['dob'] : "";

	    	$children_party = !empty($jsondata['children_party']) ? $jsondata['children_party'] : "";
	    	$gender_party = !empty($jsondata['gender_party']) ? $jsondata['gender_party'] : "";
	    	$children_count = !empty($jsondata['children_count']) ? $jsondata['children_count'] : "";
	    	$party_seen = !empty($jsondata['party_seen']) ? $jsondata['party_seen'] : "";
	    	$show_fullname = !empty($jsondata['show_fullname']) ? $jsondata['show_fullname'] : "";

	    	$start_datetime = $event_date.' '.$show_time;

	    	if($duration == '1'){
	    		$end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime) + 60*60);
				$show_end_time = date('H:i:s', strtotime($start_datetime) + 60*60);
	    	}
	    	else if($duration == '1.5'){
	    		$end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime) + 60*60*1.5);
				$show_end_time = date('H:i:s', strtotime($start_datetime) + 60*60*1.5);
	    	}
	    	else if($duration == '2'){
	    		$end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime) + 60*60*2);
				$show_end_time = date('H:i:s', strtotime($start_datetime) + 60*60*2);
	    	}
	    	else if($duration == '2.5'){
	    		$end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime) + 60*60*2.5);
				$show_end_time = date('H:i:s', strtotime($start_datetime) + 60*60*2.5);
	    	}
	    	else{
	    		$end_datetime = "";
	    		$show_end_time = "";
	    	}
	    	//if(empty($remain_amount)){
	    		$remain_amount=$event_amount;
	    	//}
			if($paid_amount <= $remain_amount){
	    		$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id';")->result_array();
    			$paid = !empty($booking_details[0]['paid_amount']) ? $booking_details[0]['paid_amount'] : 0;

	    		
	    		$paid_amounts = $paid + $paid_amount;
	    		if(empty($paid_amount)){
	    		$paid_amount =$paid;
	    		$remain_amount = $remain_amount - $paid_amounts;
	    	}else{
	    		$paid_amount =  $paid_amount;
	    	