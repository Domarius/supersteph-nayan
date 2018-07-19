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

	    	$name = !empty($jsondata['name']) ? $jsondata['name'] : "";
	    	$email = !empty($jsondata['email']) ? $jsondata['email'] : "";	    	
	    	$event_amount = !empty($jsondata['event_amount']) ? $jsondata['event_amount'] : "";
	    	$party_address = !empty($jsondata['party_address']) ? $jsondata['party_address'] : "";
	    	$show_type_text = !empty($jsondata['show_type_text']) ? $jsondata['show_type_text'] : "";
	    	
	    	$mobile_number = !empty($jsondata['mobile_number']) ? $jsondata['mobile_number'] : "";
	    	$event_date = !empty($jsondata['event_date']) ? date('Y-m-d', strtotime($jsondata['event_date'])) : date('Y-m-d');
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
	        $show_time = !empty($jsondata['show_time']) ? date('g:i A', strtotime($jsondata['show_time'])) : "";
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
					
	        $data = array('LINK' => $url,'NAME' => $name,'FEE' => $event_amount,'DURATION' => $duration, 'SHOW_DATE' => $event_date, 'DAY' => $dayOfWeek, 'SHOW_START_TIME' => $show_time, 'SHOW_TYPE' => $showType,'FIRST_NAME'=>$name,'SHOW_TEXT'=>$show_type_text);
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
      $temp['booking_id']=$id;
      $response=$temp;
     //$message = $this->load->view('admin/ad_pages/send_email', '', true);  
     
     $this->load->library('email', $config);
	 	 	$result_mail = $this->sendMail($from_email, $email, $message, $subject);

    		$historyMessage = "Admin added ".$name." booking request details";
    		$resultHistory = $this->addHistory('Booking Request', 'Add', $historyMessage);

	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Request added successfully!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Booking Request not added!", 'response' =>$response)); die;
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
			      	$temp['remain_amount'] = !empty($booking_list['remain_amount']) ? $booking_list['remain_amount'] : "";			      	
		          	$temp['paid_amount'] = !empty($booking_list['paid_amount']) ? $booking_list['paid_amount'] : "";
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
			
	    	$id = !empty($jsondata['id']) ? $jsondata['id'] : "";
	    	$name = !empty($jsondata['name']) ? $jsondata['name'] : "";
	    	$email = !empty($jsondata['email']) ? $jsondata['email'] : "";

	    	$remain_amount = !empty($jsondata['remain_amount']) ? $jsondata['remain_amount'] : "";
	    	$pay_amount = !empty($jsondata['pay_amount']) ? $jsondata['pay_amount'] : "";	    	
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
	    	$email_confirm = !empty($jsondata['email_confirm']) ? $jsondata['email_confirm'] : "";


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
			//if($paid_amount <= $remain_amount){
	    		$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id';")->result_array();
    			$paid = !empty($booking_details[0]['paid_amount']) ? $booking_details[0]['paid_amount'] : 0;

	    		//$paid_amounts=$paid_amount;
	    		//$paid_amount = $paid + $paid_amount;
	    		if(empty($paid_amount)){
	    		//$paid_amount =$paid;
	    		$remain_amount = $remain_amount - $paid_amount;
	    		$paid_amounts=$paid_amount;
	    	}else{
	    		$paid_amounts=$paid_amount;
	    		$remain_amount = $remain_amount - $paid_amount;
	    	}
				//}	

			$postData = array(
				'name'=> $name,
				'email'=> $email,
				'mobile_number'=> $mobile_number,
				'paid_amount'=> $paid_amounts,
				'event_amount'=> $event_amount,
				'remain_amount'=> $remain_amount,								
				'event_date'=> $event_date,
				'show_type'=> $show_type,
				'show_type_text'=> $show_type_text,
				'show_time'=> $show_time,
				'duration'=> $duration,
				'show_end_time'=> $show_end_time,
				'start_datetime'=> $start_datetime,
				'end_datetime'=> $end_datetime,
				'hear_supersteph'=> $hear_supersteph,
				'party_bags'=> $party_bags,
				'party_address'=> $party_address,
				'home_address'=> $home_address,
				'parking_facility'=> $parking_facility,
				'party_in_out'=> $party_in_out,
				'rain_plan'=> $rain_plan,
				'second_mobile'=> $second_mobile,
				'child_name'=> $child_name,
				'age'=> $age,
				'dob'=> $dob,
				'children_party'=> $children_party,
				'gender_party'=> $gender_party,
				'children_count'=> $children_count,
				'party_seen'=> $party_seen,
				'show_fullname'=> $show_fullname,
				'updated_on'=>date('Y-m-d H:i:s')
			);
			$updatebookingassign = $this->db->query("UPDATE `assign_booking` SET `assign_date`='$event_date'  WHERE `booking_request_id`='$id';");
			
			$this->db->where('id',$id);
    		$result = $this->db->update('booking_request',$postData);
    		$updates = array(				
				'assign_date'=> $event_date,
				'assign_start_datetime'=>$show_time,
				'start_datetime'=>$start_datetime,		
				'updated_on'=>date('Y-m-d H:i:s')		
			);
    		$this->db->where('booking_request_id',$id);
    		$results = $this->db->update('assign_booking',$updates);
    		$updatdon=date('Y-m-d H:i:s');
				
    		$historyMessage = "Admin edited ".$name." booking request details";
    		$resultHistory = $this->addHistory('Booking Request', 'Edit', $historyMessage);

    		$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id';")->result_array();
    		$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
    		
    		$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";

			$showTime = !empty($booking_details[0]['show_time']) ? date('g:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
    		$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
    		$showArray = array();
    		if(!empty($categoryDetails)){
    			foreach ($categoryDetails as $categoryList) {

    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
    			}
    		}

    		$showType = !empty($showArray) ? implode(',', $showArray) : "";

    		$customerEmail = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
    		$customerEmail = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
    		$fee = !empty($booking_details[0]['fee']) ? $booking_details[0]['fee'] : "";

    		// Start Customer Email
    		if(!empty($customerEmail)){

    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='EDIT_BOOKING' ")->result_array();

		        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
		        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";

		        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";
 				
 				$data = array('HOST' =>$name, 'SHOW_DATE' => $showDate, 'EMAIL' => $customerEmail, 'SHOW_START_TIME' => $show_time, 'DURATION' => $duration, 'SHOW_TYPE' => $showType, 'PARTY_BAGS' => $party_bags, 'PARTY_ADDRESS' => $party_address, 'HOME_ADDRESS' => $home_address, 'PARKING_FACILITY' => $parking_facility, 'PARTY_IN_OUT' => $party_in_out, 'RAIN_PLAN' => $rain_plan, 'CHILD_NAME' => $child_name, 'AGE' => $age, 'DOB' => $dob, 'CHILDREN_COUNT' => $children_count, 'CHILDREN_PARTY' => $children_party, 'GENDER_PARTY' => $gender_party, 'PARTY_SEEN' => $party_seen, 'MOBILE_NUMBER' => $mobile_number,'SHOW_DETAILS'=>$show_type_text, 'SECOND_MOBILE' => $second_mobile, 'FEE' => $event_amount );
		       // $data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType); 
		        if(!empty($data)){
		        	foreach($data as $key => $value){ 

						$subject = str_replace('{{'.$key.'}}', $value, $subject);
						$message = str_replace('{{'.$key.'}}', $value, $message);
					}
		       	}
if(!empty($email_confirm)){
		        $result_mail = $this->sendMail($from_email, $customerEmail, $message, $subject);
    	}	
    	}
    		// End Customer Email

    		// Start Admin Email
    		$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
    		$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

    		if(!empty($admin_email)){
$fee = !empty($booking_details[0]['fee']) ? $booking_details[0]['fee'] : "";

    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='EDIT_BOOKING' ")->result_array();

		        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
		        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
		        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";
		      	$data = array('HOST' =>$name, 'SHOW_DATE' => $showDate, 'EMAIL' => $customerEmail, 'SHOW_START_TIME' => $show_time, 'DURATION' => $duration, 'SHOW_TYPE' => $showType, 'PARTY_BAGS' => $party_bags, 'PARTY_ADDRESS' => $party_address, 'HOME_ADDRESS' => $home_address, 'PARKING_FACILITY' => $parking_facility, 'PARTY_IN_OUT' => $party_in_out, 'RAIN_PLAN' => $rain_plan, 'CHILD_NAME' => $child_name, 'AGE' => $age, 'DOB' => $dob, 'CHILDREN_COUNT' => $children_count, 'CHILDREN_PARTY' => $children_party, 'GENDER_PARTY' => $gender_party,'SHOW_DETAILS'=>$show_type_text, 'PARTY_SEEN' => $party_seen, 'MOBILE_NUMBER' => $mobile_number, 'SECOND_MOBILE' => $second_mobile,'FEE'=>$event_amount);
		    
		       // $data = array('HOST' =>$name, 'SHOW_DATE' => $showDate, 'EMAIL' => $customerEmail, 'SHOW_START_TIME' => $show_time, 'DURATION' => $duration, 'SHOW_TYPE' => $showType, 'PARTY_BAGS' => $party_bags, 'PARTY_ADDRESS' => $party_address, 'HOME_ADDRESS' => $home_address, 'PARKING_FACILITY' => $parking_facility, 'PARTY_IN_OUT' => $party_in_out, 'RAIN_PLAN' => $rain_plan, 'CHILD_NAME' => $child_name, 'AGE' => $age, 'DOB' => $dob, 'CHILDREN_COUNT' => $children_count, 'CHILDREN_PARTY' => $children_party, 'GENDER_PARTY' => $gender_party, 'PARTY_SEEN' => $party_seen, 'MOBILE_NUMBER' => $mobile_number, 'SECOND_MOBILE' => $second_mobile);
		        //$data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType);
		        if(!empty($data)){
		        	foreach($data as $key => $value){

						$subject = str_replace('{{'.$key.'}}', $value, $subject);
						$message = str_replace('{{'.$key.'}}', $value, $message);
					}
		       	}

		       $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
    		}
    		// End Admin Email

    		// Start Performer Email
	    		$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id';")->result_array();

	    		if(!empty($assign_details)){
			        foreach($assign_details  as $assign_list){
			          
			          	$performer_id = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";
			          	$performer_id = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";
						$performer_id = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";
						$fee = !empty($assign_list['fee']) ? $assign_list['fee'] : "";

			          	$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();
	  		
				  		//$name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
						$to_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";

			          	$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='EDIT_BOOKING' ")->result_array();

				        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
				        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
				        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";
				       
				        $data = array('HOST' =>$name, 'SHOW_DATE' => $showDate, 'EMAIL' => $customerEmail, 'SHOW_START_TIME' => $show_time, 'DURATION' => $duration, 'SHOW_TYPE' => $showType, 'PARTY_BAGS' => $party_bags, 'PARTY_ADDRESS' => $party_address, 'HOME_ADDRESS' => $home_address, 'PARKING_FACILITY' => $parking_facility, 'PARTY_IN_OUT' => $party_in_out, 'RAIN_PLAN' => $rain_plan, 'CHILD_NAME' => $child_name, 'AGE' => $age, 'DOB' => $dob, 'CHILDREN_COUNT' => $children_count, 'CHILDREN_PARTY' => $children_party,'SHOW_DETAILS'=>$show_type_text, 'GENDER_PARTY' => $gender_party, 'PARTY_SEEN' => $party_seen, 'MOBILE_NUMBER' => $mobile_number, 'FEE' => $event_amount, 'SECOND_MOBILE' => $second_mobile);
		    
				       //   $data = array('HOST' =>$name, 'SHOW_DATE' => $showDate, 'EMAIL' => $customerEmail, 'SHOW_START_TIME' => $show_time, 'DURATION' => $duration, 'SHOW_TYPE' => $showType, 'PARTY_BAGS' => $party_bags, 'PARTY_ADDRESS' => $party_address, 'HOME_ADDRESS' => $home_address, 'PARKING_FACILITY' => $parking_facility, 'PARTY_IN_OUT' => $party_in_out, 'RAIN_PLAN' => $rain_plan, 'CHILD_NAME' => $child_name, 'AGE' => $age, 'DOB' => $dob, 'CHILDREN_COUNT' => $children_count, 'CHILDREN_PARTY' => $children_party, 'GENDER_PARTY' => $gender_party, 'PARTY_SEEN' => $party_seen, 'MOBILE_NUMBER' => $mobile_number, 'SECOND_MOBILE' => $second_mobile);
				        //$data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType);
				        if(!empty($data)){
				        	foreach($data as $key => $value){

								$subject = str_replace('{{'.$key.'}}', $value, $subject);
								$message = str_replace('{{'.$key.'}}', $value, $message);
							}
				       	}

				       // $result_mail = $this->sendMail($from_email, $to_email, $message, $subject);
			        }
		      	}
    		
	      	if(!empty($result)){
	      		if(!empty($email_confirm)){
					echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Mail Sent Successfully!")); die;	      
	      		}else{	      			
	      			echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking edited successfully!")); die;
	      					}
	        	
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Booking not edited!")); die;
	      	}
    	}
  	}



  	public function viewbookingRequest(){

	    $this->load->view('header');
	    $this->load->view('viewbookingRequest');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array(); 

    	if(!empty($jsondata)){

    		$id = !empty($jsondata['id']) ? $jsondata['id'] : "";

	      	$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id';")->result_array();
	      
	      	if(!empty($booking_details)){
	        	foreach($booking_details  as $booking_list){
	          
		          	$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
		          	$temp['performer_id'] = !empty($booking_list['performer_id']) ? $booking_list['performer_id'] : "";
		          	$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
		          	$temp['email'] = !empty($booking_list['email']) ? $booking_list['email'] : "";
		          	$temp['mobile_number'] = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		          	$temp['event_date'] = !empty($booking_list['event_date']) ? $booking_list['event_date'] : "";
		          	$temp['show_type'] = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";
		          	$temp['show_time'] = !empty($booking_list['show_time']) ? $booking_list['show_time'] : "";
		          	$temp['duration'] = !empty($booking_list['duration']) ? $booking_list['duration'] : "";
		          	$temp['booking_status'] = !empty($booking_list['booking_status']) ? $booking_list['booking_status'] : "";

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
		          	$temp['dob'] = !empty($booking_list['dob']) ? $booking_list['dob'] : "";
		          	$temp['children_party'] = !empty($booking_list['children_party']) ? $booking_list['children_party'] : "";
		          	$temp['gender_party'] = !empty($booking_list['gender_party']) ? $booking_list['gender_party'] : "";
		          	$temp['children_count'] = !empty($booking_list['children_count']) ? $booking_list['children_count'] : "";
		          	$temp['party_seen'] = !empty($booking_list['party_seen']) ? $booking_list['party_seen'] : "";
		          	$temp['show_fullname'] = !empty($booking_list['show_fullname']) ? $booking_list['show_fullname'] : "";
		          	
		          	$response[] =$temp;
	        	}
	      	}
      
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking List!", 'response' =>$response)); die;
	      	}
	    }
  	}
  	
  	public function cancelbookingRequest(){

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$booking_request_id = !empty($jsondata['booking_request_id']) ? $jsondata['booking_request_id'] : "";
    		$reason = !empty($jsondata['reason']) ? $jsondata['reason'] : "";

    		$cancel_details = $this->db->query("SELECT * FROM cancel_booking where booking_request_id='$booking_request_id';")->result_array();

    		if(empty($cancel_details)){

    			$postData = array(
					'booking_request_id'=> $booking_request_id,
					'cancel_reason'=> $reason,
					'created_on'=>date('Y-m-d H:i:s'),
					'updated_on'=>date('Y-m-d H:i:s')
				);

				$result = $this->db->insert('cancel_booking',$postData);

				$statusData = array(
					'booking_status'=> 'Cancelled',
					'updated_on'=>date('Y-m-d H:i:s')
				);

				$this->db->where('id',$booking_request_id);
	    		$status_result = $this->db->update('booking_request',$statusData);
	    		$this->db->where('booking_request_id',$booking_request_id);
	    		$status_result = $this->db->update('assign_booking',$statusData);

	    		$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id';")->result_array();

	    		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";

	    		// Start Admin History
	    		$historyMessage = "Admin cancel ".$name." booking request details";
    			$resultHistory = $this->addHistory('Booking Request', 'Cancel', $historyMessage);
    			// End Admin History

	    		$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
	    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";

			$showTime = !empty($booking_details[0]['show_time']) ? date('g:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
	    		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
	    		$mobile_a = !empty($booking_details[0]['mobile_a']) ? $booking_details[0]['mobile_a'] : "";
	    		$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
	    		$child_name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
	    		$child_age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";
	    		$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
	    		//$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
	    		
	    		$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

	    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
	    		$showArray = array();
	    		if(!empty($categoryDetails)){
	    			foreach ($categoryDetails as $categoryList) {

	    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
	    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
	    			}
	    		}

	    		$showType = !empty($showArray) ? implode(',', $showArray) : "";

	    		$customerEmail = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
	    		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";


				$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_request_id';")->result_array();

	    		if(!empty($assign_details)){
			        foreach($assign_details  as $assign_list){
			          
			          	$performer_id = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";

			          	$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();
	  		
				  		$name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
				  	}
				  }
	    		// Start Customer Email
	    		if(!empty($customerEmail)){

	    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='CANCEL_EVENT_CLIENT' ")->result_array();

			        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
			        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
			        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

			        $data = array('DATE' => $showDate, 'TIME' => $showTime, 'ROLE' => $showType,'SHOW_NAME' => $name,'SHOW_EMAIL' => $customerEmail,'PHONE' => $mobile_a,'ADDRESS' => $party_address,'HOST' => $name,'CHILD_NAME' => $child_name,'CHILD_AGE' => $child_age,'SHOW_DETAILS'=>$show_type_text);
			        if(!empty($data)){
			        	foreach($data as $key => $value){

							$subject = str_replace('{{'.$key.'}}', $value, $subject);
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			        $result_mail = $this->sendMail($from_email, $customerEmail, $message, $subject);
	    		}
	    		// End Customer Email

	    		// Start Admin Email
	    		$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
	    		$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

	    		if(!empty($admin_email)){

	    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='CANCEL_EVENT_PERFORMER' ")->result_array();

			        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
			        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
			        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

			        $data = array('DATE' => $showDate, 'TIME' => $showTime,'HOST' => $name, 'ROLE' => $showType,'SHOW_NAME' => $name,'SHOW_EMAIL' => $customerEmail,'PHONE' => $mobile_a,'ADDRESS' => $party_address,'CHILD_NAME' => $child_name,'CHILD_AGE' => $child_age,'SHOW_DETAILS'=>$show_type_text);
			        if(!empty($data)){
			        	foreach($data as $key => $value){

							$subject = str_replace('{{'.$key.'}}', $value, $subject);
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
	    		}
	    		// End Admin Email

	    		// Start Performer Email
	    		$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_request_id';")->result_array();

	    		if(!empty($assign_details)){
			        foreach($assign_details  as $assign_list){
			          
			          	$performer_id = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";

			          	$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();
	  		
				  		$name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
						$to_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";

			          	$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='CANCEL_EVENT_PERFORMER' ")->result_array();

				        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
				        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
				        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

				        $data = array('DATE' => $showDate,'HOST' => $name, 'TIME' => $showTime, 'ROLE' => $showType,'SHOW_NAME' => $name,'SHOW_EMAIL' => $customerEmail,'PHONE' => $mobile_a,'ADDRESS' => $party_address,'CHILD_NAME' => $child_name,'CHILD_AGE' => $child_age,'SHOW_DETAILS'=>$show_type_text);
				        if(!empty($data)){
				        	foreach($data as $key => $value){

								$subject = str_replace('{{'.$key.'}}', $value, $subject);
								$message = str_replace('{{'.$key.'}}', $value, $message);
							}
				       	}

				        $result_mail = $this->sendMail($from_email, $to_email, $message, $subject);
			        }
		      	}

	    		// $assign_details = $this->db->query("delete FROM assign_booking where booking_request_id='$booking_request_id';");
	    		// End Performer Email
		      	
		      	if(!empty($result)){
		        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking deleted successfully!", 'response' =>$response)); die;
		      	}
		      	else{
		        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Booking not deleted!", 'response' =>$response)); die;
		      	}
    		}
    		else{
    			echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Booking already deleted!")); die;
    		}
	    }
  	}

  	public function reactivebookingRequest(){

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$booking_request_id = !empty($jsondata['id']) ? $jsondata['id'] : "";

    		$statusData = array(
				'booking_status'=> 'Ready Print',
				'updated_on'=>date('Y-m-d H:i:s')
			);

			$this->db->where('id',$booking_request_id);
    		$status_result = $this->db->update('booking_request',$statusData);

    		$this -> db -> where('booking_request_id', $booking_request_id);
  			$result = $this -> db -> delete('cancel_booking');

  			$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id';")->result_array();
    		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";

    		// Start Admin History
    		$historyMessage = "Admin Re-Active ".$name." booking request details";
			$resultHistory = $this->addHistory('Booking Request', 'Cancel', $historyMessage);
			// End Admin History

			$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";

			$showTime = !empty($booking_details[0]['show_time']) ? date('g:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
    		$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";

    		$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
    		$showArray = array();
    		if(!empty($categoryDetails)){
    			foreach ($categoryDetails as $categoryList) {

    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
    			}
    		}

    		$showType = !empty($showArray) ? implode(',', $showArray) : "";

    		$customerEmail = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
    		
    		// Start Customer Email
    		if(!empty($customerEmail)){

    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='RE-ACTIVE_BOOKING' ")->result_array();

		        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
		        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
		        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

		        $data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType,'SHOW_DETAILS'=>$show_type_text);
		        if(!empty($data)){
		        	foreach($data as $key => $value){

						$subject = str_replace('{{'.$key.'}}', $value, $subject);
						$message = str_replace('{{'.$key.'}}', $value, $message);
					}
		       	}

		     //   $result_mail = $this->sendMail($from_email, $customerEmail, $message, $subject);
    		}
    		// End Customer Email

    		// Start Admin Email
    		$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
    		$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

    		if(!empty($admin_email)){

    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='RE-ACTIVE_BOOKING' ")->result_array();

		        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
		        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
		        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

		        $data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType,'SHOW_DETAILS'=>$show_type_text);
		        if(!empty($data)){
		        	foreach($data as $key => $value){

						$subject = str_replace('{{'.$key.'}}', $value, $subject);
						$message = str_replace('{{'.$key.'}}', $value, $message);
					}
		       	}

		      //  $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
    		}
    		// End Admin Email

    		// Start Performer Email
    		$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_request_id';")->result_array();

    		if(!empty($assign_details)){
		        foreach($assign_details  as $assign_list){
		          
		          	$performer_id = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";

		          	$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();
  		
			  		$name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
					$to_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";

		          	$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='RE-ACTIVE_BOOKING' ")->result_array();

			        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
			        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
			        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

			        $data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType,'SHOW_DETAILS'=>$show_type_text);
			        if(!empty($data)){
			        	foreach($data as $key => $value){

							$subject = str_replace('{{'.$key.'}}', $value, $subject);
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			     //   $result_mail = $this->sendMail($from_email, $to_email, $message, $subject);
		        }
	      	}

    		// End Performer Email
	      	
	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Re-Active successfully!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Booking not Re-Active!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function bookingStatus(){

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";
    		$booking_status = !empty($jsondata['booking_status']) ? $jsondata['booking_status'] : "";

			$statusData = array(
				'booking_status'=> $booking_status,
				//'print_count'=> '1',
				'updated_on'=>date('Y-m-d H:i:s')
			);

			$this->db->where('id',$booking_id);
    		$result = $this->db->update('booking_request',$statusData);
    		$statusDatas = array(
				'booking_status'=> $booking_status,
				);
			$this->db->where('booking_request_id',$booking_id);
    		$result = $this->db->update('assign_booking',$statusDatas);


    		if($booking_status == 'Completed'){

    			$booking_details = $this->db->query("SELECT * FROM booking_request  where id='$booking_id';")->result_array();
		        $name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : 0;
		        $customerEmail = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

		        // Start Customer Email
	    		if(!empty($customerEmail)){

	    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='COMPLETE_BOOKING' ")->result_array();

			        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
			        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
			        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

			      //  $result_mail = $this->sendMail($from_email, $customerEmail, $message, $subject);
	    		}
	    		// End Customer Email

	    		$assign_details = $this->db->query("SELECT * FROM assign_booking  where booking_request_id='$booking_id';")->result_array();

	    		if(!empty($assign_details)){
	    			foreach($assign_details as $assign_list){

	    				$performer_id = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";

	    				$performer_details = $this->db->query("SELECT * FROM performers  where id='$performer_id';")->result_array();
		        		$performerEmail = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";

		        		// Start Performer Email
			    		if(!empty($customerEmail)){

			    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='COMPLETE_BOOKING' ")->result_array();

					        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
					        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
					        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

					      //  $result_mail = $this->sendMail($from_email, $performerEmail, $message, $subject);
			    		}
			    		// End Performer Email

		    		}
		    	}
	    	}

	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking status changed!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Booking not changed!", 'response' =>$response)); die;
	      	}
    		
	    }
  	}

  	public function cancelPerformer(){
  		 $this->checkLogin();  
	   
    		$id = !empty($_GET['id']) ? $_GET['id'] : "";
    		$parameter = !empty($_GET['parameter']) ? $_GET['parameter'] : "";
    		$booking = !empty($_GET['booking']) ? $_GET['booking'] : "";

    		//echo "DELETE FROM `assign_booking` WHERE id='$id'";
    		$assign_details = $this->db->query("SELECT * FROM assign_booking where id='$id';")->result_array();

	    		if(!empty($assign_details)){
			        foreach($assign_details  as $assign_list){
			          
			          	$performer_id = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";
			          	$assign_date = !empty($assign_list['assign_date']) ? $assign_list['assign_date'] : "";
			          	$assign_start_datetime = !empty($assign_list['assign_start_datetime']) ? $assign_list['assign_start_datetime'] : "";
			          	$duration = !empty($assign_list['duration']) ? $assign_list['duration'] : "";
			          	$assign_start_datetime = !empty($assign_start_datetime) ? date('g:i A', strtotime($assign_start_datetime)) : "";
	    	
			          	$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking';")->result_array();
			          	$host = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
						$email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
						$mobile = !empty($booking_details[0]['mobile_number']) ? $booking_details[0]['mobile_number'] : "";
						$child_name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
						$age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";
						$address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
						$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
			    		
						$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";
			    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
			    		$showArray = array();
			    		if(!empty($categoryDetails)){
			    			foreach ($categoryDetails as $categoryList) {

			    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
			    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
			    			}
			    		}

			    		$role = !empty($showArray) ? implode(',', $showArray) : "";
									
			          	$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();
	  		
				  		$name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
						$to_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";

			          	$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='CANCEL_EVENT_PERFORMER' ")->result_array();

				        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
				        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
				        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";
						$assign_id = !empty($assign_list['id']) ? base64_encode($assign_list['id']) : "";
						$booking_id = !empty($booking) ? base64_encode($booking) : "";

				        $reject = HTTP_CONTROLLER."assignRejectPerformer/?".$assign_id."/?".$booking_id;

				        $data = array('ADDRESS' => $address,'HOST' => $host,'ROLE' => $role,'SHOW_NAME' => $host,'SHOW_EMAIL' => $email,'PHONE' => $mobile,'CHILD_NAME' => $child_name,'CHILD_AGE' => $age,'DATE' => $assign_date, 'TIME' => $assign_start_datetime, 'SHOW_TYPE' => $showType,'SHOW_DETAILS'=>$show_type_text,'URL'=>$reject,'DURATION'=>$duration,'NAME'=>$host);
				        if(!empty($data)){
				        	foreach($data as $key => $value){

								$subject = str_replace('{{'.$key.'}}', $value, $subject);
								$message = str_replace('{{'.$key.'}}', $value, $message);
							}
				       	}

				        $result_mail = $this->sendMail($from_email, $to_email, $message, $subject);
			        }
		      	}

		    
    		$result = $this->db->query("DELETE FROM `assign_booking` WHERE id='$id';");
			redirect("http://supersteph.com/supersteph/welcome/editBookAssignPerformer/?$booking/?$parameter");
    	
  		

  	}

  	public function addamountBooking(){

	    $this->load->view('header');
	    $this->load->view('bookingRequest');
	    $this->load->view('footer');

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$id = !empty($jsondata['booking_request_id']) ? $jsondata['booking_request_id'] : "";
	    	$event_amount = !empty($jsondata['event_amount']) ? $jsondata['event_amount'] : "";
	    	
			$postData = array(
				'event_amount'=> $event_amount,
				'remain_amount'=> $event_amount,
				'add_amount_count'=> '1'
			);

			$this->db->where('id',$id);
    		$result = $this->db->update('booking_request',$postData);

    		$booking_details = $this->db->query("SELECT * FROM booking_request  where id='$id';")->result_array();
	        $name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : 0;
	        $customerEmail = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

    		$historyMessage = "Admin added amount ".$event_amount." ".$name." booking request details";
    		$resultHistory = $this->addHistory('Booking Request', 'Event Amount', $historyMessage);

    		// Start Customer Email
    		if(!empty($customerEmail)){

    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='ADD_AMOUNT_BOOKING' ")->result_array();

		        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
		        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
		        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

		        $data = array('HOST' => $name, 'AMOUNT' => $event_amount);
		        if(!empty($data)){
		        	foreach($data as $key => $value){

						$message = str_replace('{{'.$key.'}}', $value, $message);
					}
		       	}

		      //  $result_mail = $this->sendMail($from_email, $customerEmail, $message, $subject);
    		}
    		// End Customer Email


	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Amount added successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Amount not added!")); die;
	      	}
    	}
  	}

  	//Rakesh add amount
  	public function addamounteditBooking(){

	    if(!empty($_REQUEST['booking_request_id'])){

	    	$id = !empty($_REQUEST['booking_request_id']) ? $_REQUEST['booking_request_id'] : "";
	    	$event_amount = !empty($_REQUEST['event_amount']) ? $_REQUEST['event_amount'] : "";
	    	
			$postData = array(
				'event_amount'=> $event_amount,
				'remain_amount'=> $event_amount,
				'add_amount_count'=> '1'
			);

			$this->db->where('id',$id);
    		$result = $this->db->update('booking_request',$postData);

    		$booking_details = $this->db->query("SELECT * FROM booking_request  where id='$id';")->result_array();
	        $name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : 0;
	        $customerEmail = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

    		$historyMessage = "Admin added amount ".$event_amount." ".$name." booking request details";
    		$resultHistory = $this->addHistory('Booking Request', 'Event Amount', $historyMessage);

    		// Start Customer Email
    		if(!empty($customerEmail)){

    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='ADD_AMOUNT_BOOKING' ")->result_array();

		        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
		        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
		        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

		        $data = array('HOST' => $name, 'AMOUNT' => $event_amount);
		        if(!empty($data)){
		        	foreach($data as $key => $value){

						$message = str_replace('{{'.$key.'}}', $value, $message);
					}
		       	}

		      //  $result_mail = $this->sendMail($from_email, $customerEmail, $message, $subject);
    		}
    		// End Customer Email


	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Amount added successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Amount not added!")); die;
	      	}
    	}
  	}


  	//End Rakesh chaturvedi

  	public function paidamountBooking(){

	    $this->load->view('header');
	    $this->load->view('bookingRequest');
	    $this->load->view('footer');

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$id = !empty($jsondata['booking_request_id']) ? $jsondata['booking_request_id'] : "";
	    	$event_amount = !empty($jsondata['event_amount']) ? $jsondata['event_amount'] : "";
	    	$paid_amount = !empty($jsondata['paid_amount']) ? $jsondata['paid_amount'] : "";
	    	$remain_amount = !empty($jsondata['remain_amount']) ? $jsondata['remain_amount'] : "";

	    	if($paid_amount <= $remain_amount){

	    		$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id';")->result_array();
    			$paid = !empty($booking_details[0]['paid_amount']) ? $booking_details[0]['paid_amount'] : 0;

	    		$remain_amount = $remain_amount - $paid_amount;
	    		$paid_amount = $paid + $paid_amount;

	    		$postData = array(
					'paid_amount'=> $paid_amount,
					'remain_amount'=> $remain_amount
				);

				$this->db->where('id',$id);
	    		$result = $this->db->update('booking_request',$postData);

	    		$booking_details = $this->db->query("SELECT * FROM booking_request  where id='$id';")->result_array();
		        $name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : 0;
		        $customerEmail = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

	    		$historyMessage = "Admin paid amount ".$paid_amount." ".$name." booking request details";
	    		$resultHistory = $this->addHistory('Booking Request', 'Paid Amount', $historyMessage);

	    		// Start Customer Email
	    		if(!empty($customerEmail)){

	    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='PAID_AMOUNT_BOOKING' ")->result_array();

			        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
			        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
			        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

			        $data = array('HOST' => $name, 'SHOW_AMOUNT' => $event_amount, 'PAID_AMOUNT' => $paid_amount, 'REMAIN_AMOUNT' => $remain_amount);
			        if(!empty($data)){
			        	foreach($data as $key => $value){

							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			      //  $result_mail = $this->sendMail($from_email, $customerEmail, $message, $subject);
	    		}
	    		// End Customer Email

		      	if(!empty($result)){
		        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Amount paid successfully!")); die;
		      	}
		      	else{
		        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Amount not paid!")); die;
		      	}
	    	}
	    	else{
	    		echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Your amount is maximum for Paid Amount!")); die;
	    	}
    	}
  	}

  	public function editshowamountBooking(){

	    $this->load->view('header');
	    $this->load->view('bookingRequest');
	    $this->load->view('footer');

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$id = !empty($jsondata['booking_request_id']) ? $jsondata['booking_request_id'] : "";
	    	$event_amount = !empty($jsondata['event_amount']) ? $jsondata['event_amount'] : "";

	    	$booking_details = $this->db->query("SELECT * FROM booking_request  where id='$id';")->result_array();
	        $name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : 0;
	        $customerEmail = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
	        $paid_amount = !empty($booking_details[0]['paid_amount']) ? $booking_details[0]['paid_amount'] : "";

	        $remain_amount = $event_amount - $paid_amount;
	    	
			$postData = array(
				'event_amount'=> $event_amount,
				'remain_amount'=> $remain_amount,
				'add_amount_count'=> '1'
			);

			$this->db->where('id',$id);
    		$result = $this->db->update('booking_request',$postData);

    		

    		$historyMessage = "Admin edited amount ".$event_amount." ".$name." booking request details";
    		$resultHistory = $this->addHistory('Booking Request', 'Event Amount', $historyMessage);

    		// Start Customer Email
    		if(!empty($customerEmail)){

    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='EDIT_AMOUNT_BOOKING' ")->result_array();

		        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
		        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
		        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

		        $data = array('HOST' => $name, 'AMOUNT' => $event_amount);
		        if(!empty($data)){
		        	foreach($data as $key => $value){

						$message = str_replace('{{'.$key.'}}', $value, $message);
					}
		       	}

		      //  $result_mail = $this->sendMail($from_email, $customerEmail, $message, $subject);
    		}
    		// End Customer Email


	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Amount added successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Amount not added!")); die;
	      	}
    	}
  	}





  	public function assignPerformerName(){

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$performer_id = !empty($jsondata['performer_id']) ? implode(',', $jsondata['performer_id']) : "";
    		$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";

    		$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_id';")->result_array();
    		$show_time	 = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";


	      	$performer_details = $this->db->query("SELECT * FROM performers where id in ($performer_id);")->result_array();
	      	
	      	if(!empty($performer_details)){
	        	foreach($performer_details  as $performer_list){

	        		$temp['id'] = !empty($performer_list['id']) ? $performer_list['id'] : "";
	        		$temp['name'] = !empty($performer_list['name']) ? $performer_list['name'] : "";
	        		$temp['start_time'] = !empty($show_time) ? gmdate('Y-m-d\TH:i:s.000\Z', strtotime($show_time)) : "";
	        		$temp['end_time'] = gmdate("Y-m-d\TH:i:s.000\Z");
	        		
	        		$response[] = $temp;
	        	}
	        }
	      	
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Performer name List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Performer name List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function assignBookingName(){

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";

	      	$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_id';")->result_array();
	      	
	      	if(!empty($booking_details)){
	        	foreach($booking_details  as $booking_list){

	        		$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
	        		$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
	        		$temp['event_date'] = !empty($booking_list['event_date']) ? $booking_list['event_date'] : "";
	        		$temp['show_time'] = !empty($booking_list['show_time']) ? date("g:i A", strtotime($booking_list['show_time'])) : "";
	        		$temp['show_end_time'] = !empty($booking_list['show_end_time']) ? date("g:i A", strtotime($booking_list['show_end_time'])) : "";

	        		$temp['duration'] = !empty($booking_list['duration']) ? $booking_list['duration'].' Hours' : "";

	        		$response[] = $temp;
	        	}
	        }
	      	
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function bookPerformer(){

	    $this->load->view('header');
	    $this->load->view('bookPerformer');
	    $this->load->view('footer');

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){
	    	 
            // var_dump(implode(",",$role)); die;
	    	$booking_request_id = !empty($jsondata['booking_request_id']) ? $jsondata['booking_request_id'] : "";
	    	$performer_details = !empty($jsondata['performer_id']) ? $jsondata['performer_id'] : "";

	    	$count = 0;
	    	if(!empty($performer_details)){
	    		
	    		foreach ($performer_details as $performer_list) {
	    			$i = 0;
		             foreach($performer_list['role'] as $roles) {
		             	$role[$i]=$roles["id"];
		             	 $i++;
		             }

	    			$performer_id = !empty($performer_list['id']) ? $performer_list['id'] : "";
	    			$performer_name = !empty($performer_list['name']) ? $performer_list['name'] : "";
	    			$assign_start_datetime = !empty($performer_list['start_time']) ? date("H:i ", strtotime($performer_list['start_time'])) : "";
	    			$fee = !empty($performer_list['fee']) ? $performer_list['fee'] : "";
	    			//$role = !empty($performer_list['role']) ? $performer_list['role'] : "";
	    			
	    			$show_time = date('h:i A', strtotime($performer_list['start_time']));
					
	    			// $assign_end_datetime = !empty($performer_list['end_time']) ? date("H:i", strtotime($performer_list['end_time'])) : "";

	    			$book_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id';")->result_array();
					$event_date = !empty($book_details[0]['event_date']) ? $book_details[0]['event_date'] : 0;
					$mobile = !empty($book_details[0]['mobile_number']) ? $book_details[0]['mobile_number'] : 0;
					$email = !empty($book_details[0]['email']) ? $book_details[0]['email'] : 0;
					$child_name = !empty($book_details[0]['child_name']) ? $book_details[0]['child_name'] : 0;
					$age = !empty($book_details[0]['age']) ? $book_details[0]['age'] : 0;
					$name = !empty($book_details[0]['name']) ? $book_details[0]['name'] : 0;
					$show_type_text = !empty($book_details[0]['show_type_text']) ? $book_details[0]['show_type_text'] : 0;
					$duration = !empty($book_details[0]['duration']) ? $book_details[0]['duration'] : 0;
					$party_address = !empty($book_details[0]['party_address']) ? $book_details[0]['party_address'] : 0;
					
					$duration = !empty($performer_list['duration']) ? $performer_list['duration'] : "";

					$start_datetime = $event_date.' '.$assign_start_datetime;

					if($duration == '1'){
						$assign_end_datetime = date('H:i:s', strtotime($start_datetime) + 60*60);
			    	}
			    	else if($duration == '1.5'){
						$assign_end_datetime = date('H:i:s', strtotime($start_datetime) + 60*60*1.5);
			    	}
			    	else if($duration == '2'){
						$assign_end_datetime = date('H:i:s', strtotime($start_datetime) + 60*60*2);
			    	}
			    	else if($duration == '2.5'){
						$assign_end_datetime = date('H:i:s', strtotime($start_datetime) + 60*60*2.5);
			    	}
			    	else{
			    		$assign_end_datetime = "";
			    	}

			    	$start_datetime = $event_date.' '.$assign_start_datetime;
			    	$end_datetime = $event_date.' '.$assign_end_datetime;

	    			$postData = array(
						'booking_request_id'=> $booking_request_id,
						'performer_id'=> $performer_id,
						'fee'=>$fee,
						'role'=>implode(",",$role),
						'assign_date'=>$event_date,
						'assign_start_datetime'=>$assign_start_datetime,
						'assign_end_datetime'=>$assign_end_datetime,
						'start_datetime'=>$start_datetime,
						'end_datetime'=>$end_datetime,
						'duration'=>$duration,
						'created_on'=>date('Y-m-d H:i:s'),
						'updated_on'=>date('Y-m-d H:i:s')
					);

					//$performer_total = $this->db->query("SELECT count(*) as total FROM assign_booking where performer_id='$performer_id' and (start_datetime >='$start_datetime' and end_datetime <='$end_datetime');")->result_array();
					$performer_total = $this->db->query("SELECT count(*) as total FROM assign_booking where performer_id='$performer_id' and booking_request_id=$booking_request_id;")->result_array();

					$total = !empty($performer_total[0]['total']) ? $performer_total[0]['total'] : 0;

					if($total =='0'){

						$count = $count+1;
						$result = $this->db->insert('assign_booking',$postData);

						$postData1 = array(
							'performer_count'=> '1'
						);
						$postWhere1 = array(
							'id=' => $booking_request_id
						);
						$this->db->where($postWhere1);
						$result1 = $this->db->update('booking_request',$postData1);

						$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id' ")->result_array();
						$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
			    		$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
			    		$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";    		
			    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
			    		
						$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    					    		
			    		$child_name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
			    		$address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
						$age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";
						$mobile_number = !empty($booking_details[0]['mobile_number']) ? $booking_details[0]['mobile_number'] : "";
						$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
						$assigns = $this->db->query("SELECT * FROM assign_booking where performer_id='$performer_id' and booking_request_id=$booking_request_id;")->result_array();

						$categoryID = !empty($assigns[0]['role']) ? $assigns[0]['role'] : "";
						$fee = !empty($assigns[0]['fee']) ? $assigns[0]['fee'] : "";

			    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
			    		$showArray = array();
			    		if(!empty($categoryDetails)){
			    			foreach ($categoryDetails as $categoryList) {

			    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
			    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
			    			}
			    		}

			    		$showType = !empty($showArray) ? implode(',', $showArray) : "";

			    		// Start Performer History
			    		$historyMessage = "Admin assign performers for ".$name." booking request details";
    					$resultHistory = $this->addHistory('Booking Request', 'Assign', $historyMessage);
    					// End Performer History

			    		// Start Performer Email
			    		$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();
			    		$to_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
			    		$assign_id = !empty($performer_details[0]['id']) ? base64_encode($performer_details[0]['id']) : "";
			    		$booking_id = !empty($booking_request_id) ? base64_encode($booking_request_id) : "";
			    		$show_time = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
						$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
						$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
						$child_name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
						//$fee = !empty($booking_details[0]['fee']) ? $booking_details[0]['fee'] : "";
						$show_time = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
						//$show_time = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
						
	    				$show_time = date('g:i A', strtotime($performer_list['start_time']));
					
			    		
						$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='ADMIN_ASSIGN_PERFORMER' ")->result_array();

				        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
				        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
				        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

				        $accept = HTTP_CONTROLLER."assignAcceptPerformer/?".$assign_id."/?".$booking_id;
				        // $reject = HTTP_CONTROLLER."assignRejectPerformer/?".$assign_id."/?".$booking_id;

				        $data = array('SHOW_DATE' => $event_date, 'SHOW_TIME' => $show_time, 'SHOW_START_TIME' => $show_time, 'SHOW_DETAILS'=>$show_type_text, 'SHOW_TYPE' => $show_type_text, 'PARTY_ADDRESS' => $party_address,'CHILD_NAME' => $child_name,'CHARACTER' => $showType,'FEE' => $fee,'EMAIL' => $email,'MOBILE' => $mobile,'AGE' => $age,'HOST' => $name, 'DURATION' => $duration , 'ACCEPT' => $accept);
				        if(!empty($data)){
				        	foreach($data as $key => $value){

								$subject = str_replace('{{'.$key.'}}', $value, $subject);
								$message = str_replace('{{'.$key.'}}', $value, $message);
							}
				       	}

				 	 	$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);
				 	 	// End Performer Email

				 	 	// Start Admin Email
			    		$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
			    		$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

			    		if(!empty($admin_email)){

			    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='ADMIN_ASSIGN_PERFORMER' ")->result_array();

					        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
					        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
					        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

				        $data = array('SHOW_DATE' => $event_date,'SHOW_TIME' => $show_time, 'SHOW_START_TIME' => $show_time, 'SHOW_TYPE' => $role, 'PARTY_ADDRESS' => $party_address,'CHILD_NAME' => $child_name,'CHARACTER' => $showType,'SHOW_DETAILS'=>$show_type_text,'FEE' => $fee,'EMAIL' => $email,'MOBILE' => $mobile,'AGE' => $age,'HOST' => $name,'DURATION' => $duration , 'ACCEPT' => $accept);
					       // $data = array('SHOW_DATE' => $event_date, 'SHOW_START_TIME' => $assign_start_datetime, 'SHOW_END_TIME' => $assign_end_datetime, 'SHOW_TYPE' => $showType, 'ADDRESS' => $party_address, 'ACCEPT' => $accept);
					        if(!empty($data)){
					        	foreach($data as $key => $value){

									$subject = str_replace('{{'.$key.'}}', $value, $subject);
									$message = str_replace('{{'.$key.'}}', $value, $message);
								}
					       	}

					        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
			    		}
			    		// End Admin Email
					}
    				
	    		}
	    	}

	      	if(!empty($count)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Performer assign successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "You have already assigned this performer!")); die;
	      	}
    	}
  	}





  	public function editBookAssignBookingName(){

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";

	      	$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_id';")->result_array();
	      	
	      	if(!empty($booking_details)){
	        	foreach($booking_details  as $booking_list){

	        		$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
	        		$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
	        		$temp['event_date'] = !empty($booking_list['event_date']) ? $booking_list['event_date'] : "";
	        		$temp['show_time'] = !empty($booking_list['show_time']) ? date("g:i A", strtotime($booking_list['show_time'])) : "";
	        		$temp['show_end_time'] = !empty($booking_list['show_end_time']) ? date("g:i A", strtotime($booking_list['show_end_time'])) : "";

	        		$temp['duration'] = !empty($booking_list['duration']) ? $booking_list['duration'].' Hours' : "";

	        		$response[] = $temp;
	        	}
	        }
	      	
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function editBookAssignPerformer(){

  		$this->load->view('header');
	    $this->load->view('editBookAssignPerformer');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";

    		$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_id';")->result_array();

		  	if(!empty($assign_details)){
	        	foreach($assign_details  as $assign_list){

	        		$temp['assign_id'] = !empty($assign_list['id']) ? $assign_list['id'] : "";

	        		$temp['booking_id'] = !empty($assign_list['booking_request_id']) ? $assign_list['booking_request_id'] : "";

	        		$temp['performer_id'] = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";
	        		$temp['role'] = !empty($assign_list['role']) ? $assign_list['role'] : "";

	        		$performer_id = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";
	        		$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id';")->result_array();
    				$temp['profile_image'] = !empty($performer_details[0]['profile_image']) ? 'http://supersteph.com/supersteph/image/performer/'.$performer_details[0]['profile_image'] : "";
    				$temp['name'] = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
					$temp['mobile_a'] = !empty($performer_details[0]['mobile_a']) ? $performer_details[0]['mobile_a'] : "";

    				if(empty($response)){

    					$booking_id = !empty($assign_list['booking_request_id']) ? $assign_list['booking_request_id'] : "";

    					$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_id';")->result_array();
    					$show_time = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
    					$show_type = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";
    					$temp['show_type'] = !empty($performer_details[0]['show_type']) ? $performer_details[0]['show_type'] : "";

    					$temp['start_time'] = gmdate('Y-m-d\TH:i:s.000\Z', strtotime($show_time));
    				}
    				else{
    					$temp['start_time'] = !empty($assign_list['assign_start_datetime']) ? gmdate('Y-m-d\TH:i:s.000\Z', strtotime($assign_list['assign_start_datetime'])) : "";
    				}
    				
    				$temp['end_time'] = !empty($assign_list['assign_end_datetime']) ? gmdate('Y-m-d\TH:i:s.000\Z', strtotime($assign_list['assign_end_datetime'])) : "";

    				$temp['duration'] = !empty($assign_list['duration']) ? $assign_list['duration'] : "";

    					$category = !empty($assign_list['role']) ? $assign_list['role'] : "";
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
		          	

	        		
	        		$response[] = $temp;
	        	}
	        }
	      	
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Edit Book Performer name List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Edit Book Performer name List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function editBookPerformer(){

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array(); 
	    $temp = array();

	    if(!empty($jsondata)){
	    	
	    	//var_dump($jsondata); die;
	    	$booking_request_id = !empty($jsondata['booking_request_id']) ? $jsondata['booking_request_id'] : "";
	    	$performer_details = !empty($jsondata['performer_id']) ? $jsondata['performer_id'] : "";

	    	$count = 0;
	    	if(!empty($performer_details)){
	    		
	    		foreach ($performer_details as $performer_list) {

	    			$assign_id = !empty($performer_list['assign_id']) ? $performer_list['assign_id'] : "";
	    			$performer_id = !empty($performer_list['performer_id']) ? $performer_list['performer_id'] : "";
	    			$performer_name = !empty($performer_list['name']) ? $performer_list['name'] : "";
	    			$assign_start_datetime = !empty($performer_list['start_time']) ? date("H:i", strtotime($performer_list['start_time'])) : "";

	    			$book_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id';")->result_array();
					$event_date = !empty($book_details[0]['event_date']) ? $book_details[0]['event_date'] : 0;

					$duration = !empty($performer_list['duration']) ? $performer_list['duration'] : "";
					//$role = !empty($performer_list['role']) ? $performer_list['role'] : "";


					$start_datetime = $event_date.' '.$assign_start_datetime;

					if($duration == '1'){
						$assign_end_datetime = date('H:i:s', strtotime($start_datetime) + 60*60);
			    	}
			    	else if($duration == '1.5'){
						$assign_end_datetime = date('H:i:s', strtotime($start_datetime) + 60*60*1.5);
			    	}
			    	else if($duration == '2'){
						$assign_end_datetime = date('H:i:s', strtotime($start_datetime) + 60*60*2);
			    	}
			    	else if($duration == '2.5'){
						$assign_end_datetime = date('H:i:s', strtotime($start_datetime) + 60*60*2.5);
			    	}
			    	else{
			    		$assign_end_datetime = "";
			    	}

			    	$start_datetime = $event_date.' '.$assign_start_datetime;
			    	$end_datetime = $event_date.' '.$assign_end_datetime;
			    	$show_typeDetails = !empty($performer_list['role']) ? $performer_list['role'] : "";
	    	$categoryArray = array();
	    	if(!empty($show_typeDetails)){
	    		foreach ($show_typeDetails as $show_typeList) {
	    			$categoryArray[] = !empty($show_typeList['id']) ? $show_typeList['id'] : "";
	    		}
	    	}
	    	$counts=count($categoryArray);
	    	$category_id = !empty($categoryArray) ? implode(',', $categoryArray) : "";
			

			    	$role = implode(",",array_unique($performer_list['role']));

	    			$postData = array(
						'booking_request_id'=> $booking_request_id,
						'performer_id'=> $performer_id,
						'assign_date'=>$event_date,
						//'role'=>$counts,
						'role'=>$role,
						'assign_start_datetime'=>$assign_start_datetime,
						'assign_end_datetime'=>$assign_end_datetime,
						'start_datetime'=>$start_datetime,
						'end_datetime'=>$end_datetime,
						'duration'=>$duration,
						'created_on'=>date('Y-m-d H:i:s'),
						'updated_on'=>date('Y-m-d H:i:s')
					);

					$count = $count+1;

					$this->db->where('id',$assign_id);
    				$result = $this->db->update('assign_booking',$postData);

					$assigns_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_request_id' and performer_id='$performer_id';")->result_array();
					
					$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id' ")->result_array();
					$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
		    		$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
		    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";

			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
		    		$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
		    		$categoryID = !empty($assigns_details[0]['role']) ? $assigns_details[0]['role'] : "";
		    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
		    		$showArray = array();
		    		if(!empty($categoryDetails)){
		    			foreach ($categoryDetails as $categoryList) {

		    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
		    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
		    			}
		    		}

		    		$showType = !empty($showArray) ? implode(', ', $showArray) : " ";

		    		// Start Performer History
		    		$historyMessage = "Admin edited assign performers for ".$name." booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'Assign', $historyMessage);
					// End Performer History

		    		// Start Performer Email
		    		$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();
		    		$to_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
		    		$assign_id = !empty($performer_details[0]['id']) ? base64_encode($performer_details[0]['id']) : "";
		    		$booking_id = !empty($booking_request_id) ? base64_encode($booking_request_id) : "";
		    		$event_date = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
		    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
		    		$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
		    		$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
		    		$child_name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
		    		$email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
		    		$mobile = !empty($booking_details[0]['mobile']) ? $booking_details[0]['mobile'] : "";
		    		$age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";
		    		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
		    		$duration = !empty($booking_details[0]['duration']) ? $booking_details[0]['duration'] : "";
		    		$fee = !empty($assigns_details[0]['fee']) ? $assigns_details[0]['fee'] : "";
		    		$assign_start_datetime = !empty($assigns_details[0]['assign_start_datetime']) ? $assigns_details[0]['assign_start_datetime'] : "";
		    		$start_datetime = !empty($assigns_details[0]['start_datetime']) ? $assigns_details[0]['start_datetime'] : "";
		    		//$categoryID = !empty($assigns_details[0]['role']) ? $assigns_details[0]['role'] : "";
		    		

					$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
		    		// $name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
		    		// $name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
		    			

			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
					$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='RE-SCHEDULE_BOOKING' ")->result_array();

			        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
			        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
			        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

			        $accept = HTTP_CONTROLLER."assignAcceptPerformer/?".$assign_id."/?".$booking_id;
			        // $reject = HTTP_CONTROLLER."assignRejectPerformer/?".$assign_id."/?".$booking_id;
			         $data = array('SHOW_DATE' => $event_date, 'SHOW_TIME' => $assign_start_datetime, 'SHOW_START_TIME' => $start_datetime, 'SHOW_DETAILS'=>$show_type_text, 'SHOW_TYPE' => $show_type_text, 'PARTY_ADDRESS' => $party_address,'CHILD_NAME' => $child_name,'CHARACTER' => $showType,'FEE' => $fee,'EMAIL' => $email,'MOBILE' => $mobile,'AGE' => $age,'HOST' => $name, 'DURATION' => $duration , 'ACCEPT' => $accept);
			        //$data = array('SHOW_DATE' => $event_date, 'SHOW_START_TIME' => $assign_start_datetime, 'SHOW_END_TIME' => $assign_end_datetime, 'SHOW_TYPE' => $showType,'SHOW_DETAILS'=>$show_type_text, 'ACCEPT' => $accept);
			        if(!empty($data)){
			        	foreach($data as $key => $value){

							$subject = str_replace('{{'.$key.'}}', $value, $subject);
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			 	 	$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);
			 	 	// End Performer Email

			 	 	// Start Admin Email
		    		$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
		    		$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

		    		if(!empty($admin_email)){

		    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='RE-SCHEDULE_BOOKING' ")->result_array();

				        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
				        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
				        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";
				         $data = array('SHOW_DATE' => $event_date, 'SHOW_TIME' => $show_time, 'SHOW_START_TIME' => $show_time, 'SHOW_DETAILS'=>$show_type_text, 'SHOW_TYPE' => $show_type_text, 'PARTY_ADDRESS' => $party_address,'CHILD_NAME' => $child_name,'CHARACTER' => $showType,'FEE' => $fee,'EMAIL' => $email,'MOBILE' => $mobile,'AGE' => $age,'HOST' => $name, 'DURATION' => $duration , 'ACCEPT' => $accept);
				        //$data = array('SHOW_DATE' => $event_date, 'SHOW_START_TIME' => $assign_start_datetime, 'SHOW_END_TIME' => $assign_end_datetime, 'SHOW_TYPE' => $showType,'SHOW_DETAILS'=>$show_type_text, 'ACCEPT' => $accept);
				        if(!empty($data)){
				        	foreach($data as $key => $value){

								$subject = str_replace('{{'.$key.'}}', $value, $subject);
								$message = str_replace('{{'.$key.'}}', $value, $message);
							}
				       	}

				        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
		    		}
		    		// End Admin Email
    				
	    		}
	    	}

	      	if(!empty($count)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Edit Performer assign successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Edit Performer does not assign!")); die;
	      	}
    	}
  	}


  	public function viewperformer(){

	    $this->load->view('header');
	    $this->load->view('viewperformer');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$booking_id = !empty($jsondata['id']) ? $jsondata['id'] : "";
    		
	      	$performer_total = $this->db->query("SELECT count(*) as total FROM assign_booking  where booking_request_id='$booking_id';")->result_array();
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
      
	      	$performer_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_id' order by id desc $limitto;")->result_array();
	      
	      	if(!empty($performer_details)){
		        foreach($performer_details  as $performer_list){
		          
		          	$temp['id'] = !empty($performer_list['id']) ? $performer_list['id'] : "";
		          	$booking_request_id = !empty($performer_list['booking_request_id']) ? $performer_list['booking_request_id'] : "";
		          	$performer_id = !empty($performer_list['performer_id']) ? $performer_list['performer_id'] : "";


		          	$perform_details = $this->db->query("SELECT * FROM performers where id='$performer_id';")->result_array();

		        	$temp['name'] = !empty($perform_details[0]['name']) ? $perform_details[0]['name'] : 0;
		        	$temp['email'] = !empty($perform_details[0]['email']) ? $perform_details[0]['email'] : 0;
		        	$temp['mobile'] = !empty($perform_details[0]['mobile_a']) ? $perform_details[0]['mobile_a'] : 0;
		        	$temp['profile_image'] = !empty($perform_details[0]['profile_image']) ? GET_PERFORMER_IMAGE_PATH.$perform_details[0]['profile_image'] : GET_DUMMY_IMAGE_PATH.'dummy.png';

		        	$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_request_id' and performer_id='$performer_id';")->result_array();
		        	$temp['status'] = !empty($assign_details[0]['status']) ? $assign_details[0]['status'] : "";
		        	$temp['reason'] = !empty($assign_details[0]['reason']) ? $assign_details[0]['reason'] : "-";
		        	$temp['date'] = !empty($assign_details[0]['assign_date']) ? date('d-m-Y', strtotime($assign_details[0]['assign_date'])) : "";
		        	$temp['start_time'] = !empty($assign_details[0]['assign_start_datetime']) ? date('h:i A', strtotime($assign_details[0]['assign_start_datetime'])) : "";
		        	$temp['end_time'] = !empty($assign_details[0]['assign_end_datetime']) ? date('h:i A', strtotime($assign_details[0]['assign_end_datetime'])) : "";

		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "view Performer List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty view Performer List!", 'response' =>$response)); die;
	      	}
	    }
  	}
    
    public function editViewperformer(){

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$booking_id = !empty($jsondata['id']) ? $jsondata['id'] : "";
    	
	      	$performer_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_id' order by assign_start_datetime ASC;")->result_array();
	      
	      	if(!empty($performer_details)){
		        foreach($performer_details  as $performer_list){

		          
		          	$temp['id'] = !empty($performer_list['id']) ? $performer_list['id'] : "";
		          	$booking_request_id = !empty($performer_list['booking_request_id']) ? $performer_list['booking_request_id'] : "";
		          	$performer_id = !empty($performer_list['performer_id']) ? $performer_list['performer_id'] : "";
		          	$role = !empty($performer_list['role']) ? $performer_list['role'] : "";


		          	$perform_details = $this->db->query("SELECT * FROM performers where id='$performer_id';")->result_array();

		        	$temp['name'] = !empty($perform_details[0]['name']) ? $perform_details[0]['name'] : 0;
		        	$temp['email'] = !empty($perform_details[0]['email']) ? $perform_details[0]['email'] : 0;
		        	$temp['mobile'] = !empty($perform_details[0]['mobile_a']) ? $perform_details[0]['mobile_a'] : 0;
		        	$temp['role'] = !empty($perform_details[0]['role']) ? $perform_details[0]['role'] : 0;
		        	
		        	$temp['profile_image'] = !empty($perform_details[0]['profile_image']) ? GET_PERFORMER_IMAGE_PATH.$perform_details[0]['profile_image'] : GET_DUMMY_IMAGE_PATH.'dummy.png';

		        	$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_request_id' and performer_id='$performer_id';")->result_array();
		        	$roles = $this->db->query("SELECT category FROM categories where id IN (".$assign_details[0]['role'].")")->result_array();
		        	$i=0;
		        	foreach ($roles as $roles_val) {
		        		$rval[$i]=$roles_val['category'];
		        		$i++;
		        	}
		        	$temp['status'] = !empty($assign_details[0]['status']) ? $assign_details[0]['status'] : "";		        	
		        	$temp['duration'] = !empty($assign_details[0]['duration']) ? $assign_details[0]['duration'] : "";
		        	$temp['role'] = implode(",",$rval);	        	
		        	$temp['reason'] = !empty($assign_details[0]['reason']) ? $assign_details[0]['reason'] : "-";
		        	$temp['date'] = !empty($assign_details[0]['assign_date']) ? date('d-m-Y', strtotime($assign_details[0]['assign_date'])) : "";
		        	$temp['start_time'] = !empty($assign_details[0]['assign_start_datetime']) ? date('h:i A', strtotime($assign_details[0]['assign_start_datetime'])) : "";
		        	$temp['end_time'] = !empty($assign_details[0]['assign_end_datetime']) ? date('h:i A', strtotime($assign_details[0]['assign_end_datetime'])) : "";

		          	$response[] =$temp;
		        }
	      	}
      
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "view Performer List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty view Performer List!", 'response' =>$response)); die;
	      	}
	    }
  	}
    
  	public function editAssignPerformer(){
	    $this->load->view('header');
	    $this->load->view('editAssignPerformer');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);
	    $response = array();
	    $temp = array();	    
    	if(!empty($jsondata)){
    		$id = !empty($jsondata['id']) ? $jsondata['id'] : "";
	      	$assign_booking_details = $this->db->query("SELECT * FROM assign_booking where id='$id';")->result_array();	      
	      	if(!empty($assign_booking_details)){
	        	foreach($assign_booking_details  as $assign_booking_list){	          
		          	$temp['id'] = !empty($assign_booking_list['id']) ? $assign_booking_list['id'] : "";
		          	$temp['booking_id'] = !empty($assign_booking_list['booking_request_id']) ? $assign_booking_list['booking_request_id'] : "";
		          	$temp['performer_id'] = !empty($assign_booking_list['performer_id']) ? $assign_booking_list['performer_id'] : "";
		          	$temp['duration'] = !empty($assign_booking_list['duration']) ? $assign_booking_list['duration'] : "";		          
		        	$temp['start_time'] = !empty($assign_booking_list['start_datetime']) ? gmdate('Y-m-d\TH:i:s.000\Z', strtotime($assign_booking_list['start_datetime'])) : "";
		          	$temp['end_time'] = !empty($assign_booking_list['end_datetime']) ? gmdate('Y-m-d\TH:i:s.000\Z', strtotime($assign_booking_list['end_datetime'])) : "";		          
		          	$response[] =$temp;
	        	}
	      	}
      
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Assign Performer List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Assign Performer List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function saveEditAssignPerformer(){

  		// $this->load->view('saveEditAssignPerformer');

	    $this->checkLogin();  
	    
	    $jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	 $id = !empty($jsondata['assign_id']) ? $jsondata['assign_id'] : "";
	    	 $start_time = !empty($jsondata['start_time']) ? date('H:i', strtotime($jsondata['start_time'])) : "";
	    	 $end_time = !empty($jsondata['end_time']) ? date('H:i', strtotime($jsondata['end_time'])) : "";
	        //$assign_id = !empty($performer_list['assign_id']) ? $performer_list['assign_id'] : "";
	    	 		$booking_request_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";
	    			$performer_id = !empty($jsondata['performer_id']) ? $jsondata['performer_id'] : "";
	    			$performer_name = !empty($jsondata['name']) ? $jsondata['name'] : "";
	    			$assign_start_datetime = !empty($jsondata['start_time']) ? date("H:i", strtotime($jsondata['start_time'])) : "";

	    			$book_details = $this->db->query("SELECT * FROM booking_request where id=".$jsondata['booking_id']."")->result_array();
					$event_date = !empty($book_details[0]['event_date']) ? $book_details[0]['event_date'] : 0;

					$duration = !empty($jsondata['duration']) ? $jsondata['duration'] : "";
					$confirmemail = !empty($jsondata['confirmemail']) ? $jsondata['confirmemail'] : "";
					//$role = !empty($performer_list['role']) ? $performer_list['role'] : "";
 

					$start_datetime = $event_date.' '.$assign_start_datetime;

					if($duration == '1'){
						$assign_end_datetime = date('H:i:s', strtotime($start_datetime) + 60*60);
			    	}
			    	else if($duration == '1.5'){
						$assign_end_datetime = date('H:i:s', strtotime($start_datetime) + 60*60*1.5);
			    	}
			    	else if($duration == '2'){
						$assign_end_datetime = date('H:i:s', strtotime($start_datetime) + 60*60*2);
			    	}
			    	else if($duration == '2.5'){
						$assign_end_datetime = date('H:i:s', strtotime($start_datetime) + 60*60*2.5);
			    	}
			    	else{
			    		$assign_end_datetime = "";
			    	}

			    	$start_datetime = $event_date.' '.$assign_start_datetime;
			    	$end_datetime = $event_date.' '.$assign_end_datetime;
			$role = implode(",",array_unique($jsondata['role']));

	    			$postData = array(
						'booking_request_id'=> $booking_request_id,
						'performer_id'=> $performer_id,
						'assign_date'=>$event_date,
						//'role'=>$counts,
						'role'=>$role,
						'assign_start_datetime'=>$assign_start_datetime,
						'assign_end_datetime'=>$assign_end_datetime,
						'start_datetime'=>$start_datetime,
						'end_datetime'=>$end_datetime,
						'duration'=>$duration,
						'created_on'=>date('Y-m-d H:i:s'),
						'updated_on'=>date('Y-m-d H:i:s')
					);

           //   var_dump($postData); die;
			$this->db->where('id',$id);
    		$result = $this->db->update('assign_booking',$postData);

    		$historyMessage = "Admin edited assign performer details";
    		$resultHistory = $this->addHistory('Performer', 'Edit Assign', $historyMessage);

    		$assign_details = $this->db->query("SELECT * FROM assign_booking where id='$id'")->result_array();
			$booking_request_id = !empty($assign_details[0]['booking_request_id']) ? $assign_details[0]['booking_request_id'] : "";
			$performer_id = !empty($assign_details[0]['performer_id']) ? $assign_details[0]['performer_id'] : "";


			$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id' ")->result_array();
			$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
    		$chile_name = !empty($booking_details[0]['chile_name']) ? $booking_details[0]['chile_name'] : "";
    		$age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";
    		$mobile = !empty($booking_details[0]['mobile']) ? $booking_details[0]['mobile'] : "";
    		$email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
    		
    		$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
    		$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";

			$showTime = !empty($booking_details[0]['show_time']) ? date('g:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
    		$categoryID = !empty($assign_details[0]['role']) ? $assign_details[0]['role'] : "";

    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
    		$showArray = array();
    		if(!empty($categoryDetails)){
    			foreach ($categoryDetails as $categoryList) {

    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
    			}
    		}

    		$showType = !empty($showArray) ? implode(',', $showArray) : "";

    		// Start Performer Email
    		$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();
    		$to_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
    		$booking_id = !empty($booking_request_id) ? base64_encode($booking_request_id) : "";
			    		
			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='RE-SCHEDULE_BOOKING' ")->result_array();

	        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
	        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
	        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";
	        $data = array('SHOW_DATE' => $event_date, 'SHOW_TIME' => $assign_start_datetime, 'SHOW_START_TIME' => $start_datetime, 'SHOW_DETAILS'=>$show_type_text, 'SHOW_TYPE' => $show_type_text, 'PARTY_ADDRESS' => $party_address,'CHILD_NAME' => $child_name,'CHARACTER' => $showType,'FEE' => $fee,'EMAIL' => $email,'MOBILE' => $mobile,'AGE' => $age,'HOST' => $name, 'DURATION' => $duration , 'ACCEPT' => $accept);
	        //$data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $start_time, 'SHOW_END_TIME' => $end_time,'SHOW_DETAILS'=>$show_type_text, 'SHOW_TYPE' => $showType);
	        if(!empty($data)){
	        	foreach($data as $key => $value){

					$subject = str_replace('{{'.$key.'}}', $value, $subject);
					$message = str_replace('{{'.$key.'}}', $value, $message);
				}
	       	}
if(!empty($confirmemail)){
	 	 	//$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);	
}
	 	 	// End Performer Email 

	      	if(!empty($result)){
	      		 
		      			echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Perfromer schedule updated successfully!")); die;
	      	
		      		
	        	}   
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Assign Performer not edited!")); die;
	      	}
    	}else{
    		echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Assign Performer edited Failed!")); die;
	      	
    	}
  	}


// Start only email 
 	public function emailEditAssignPerformer(){

  		// $this->load->view('saveEditAssignPerformer');

	    $this->checkLogin();  
	    
	    $jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	 $id = !empty($jsondata['assign_id']) ? $jsondata['assign_id'] : "";
	    	 $start_time = !empty($jsondata['start_time']) ? date('H:i', strtotime($jsondata['start_time'])) : "";
	    	 $end_time = !empty($jsondata['end_time']) ? date('H:i', strtotime($jsondata['end_time'])) : "";
	        //$assign_id = !empty($performer_list['assign_id']) ? $performer_list['assign_id'] : "";
	    	 		$booking_request_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";
	    			$performer_id = !empty($jsondata['performer_id']) ? $jsondata['performer_id'] : "";
	    			$performer_name = !empty($jsondata['name']) ? $jsondata['name'] : "";
	    			$assign_start_datetime = !empty($jsondata['start_time']) ? date("H:i", strtotime($jsondata['start_time'])) : "";

	    			$book_details = $this->db->query("SELECT * FROM booking_request where id=".$jsondata['booking_id']."")->result_array();
					$event_date = !empty($book_details[0]['event_date']) ? $book_details[0]['event_date'] : 0;

					$duration = !empty($jsondata['duration']) ? $jsondata['duration'] : "";
					$confirmemail = !empty($jsondata['confirmemail']) ? $jsondata['confirmemail'] : "";
					//$role = !empty($performer_list['role']) ? $performer_list['role'] : "";
 

					$start_datetime = $event_date.' '.$assign_start_datetime;

					if($duration == '1'){
						$assign_end_datetime = date('g:i A', strtotime($start_datetime) + 60*60);
			    	}
			    	else if($duration == '1.5'){
						$assign_end_datetime = date('g:i A', strtotime($start_datetime) + 60*60*1.5);
			    	}
			    	else if($duration == '2'){
						$assign_end_datetime = date('g:i A', strtotime($start_datetime) + 60*60*2);
			    	}
			    	else if($duration == '2.5'){
						$assign_end_datetime = date('g:i A', strtotime($start_datetime) + 60*60*2.5);
			    	}
			    	else{
			    		$assign_end_datetime = "";
			    	}

			    	$start_datetime = $event_date.' '.$assign_start_datetime;
			    	$end_datetime = $event_date.' '.$assign_end_datetime;
			$role = implode(",",array_unique($jsondata['role']));

	    			$postData = array(
						'booking_request_id'=> $booking_request_id,
						'performer_id'=> $performer_id,
						'assign_date'=>$event_date,
						//'role'=>$counts,
						'role'=>$role,
						'assign_start_datetime'=>$assign_start_datetime,
						'assign_end_datetime'=>$assign_end_datetime,
						'start_datetime'=>$start_datetime,
						'end_datetime'=>$end_datetime,
						'duration'=>$duration,
						'created_on'=>date('Y-m-d H:i:s'),
						'updated_on'=>date('Y-m-d H:i:s')
					);

           //   var_dump($postData); die;
			// $this->db->where('id',$id);
   //  		$result = $this->db->update('assign_booking',$postData);

    		$historyMessage = "Admin edited assign performer details";
    		$resultHistory = $this->addHistory('Performer', 'Edit Assign', $historyMessage);

    		$assign_details = $this->db->query("SELECT * FROM assign_booking where id='$id'")->result_array();
			$booking_request_id = !empty($assign_details[0]['booking_request_id']) ? $assign_details[0]['booking_request_id'] : "";
			$performer_id = !empty($assign_details[0]['performer_id']) ? $assign_details[0]['performer_id'] : "";
			$start_datetime = !empty($assign_details[0]['start_datetime']) ? $assign_details[0]['start_datetime'] : "";


			$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id' ")->result_array();
			$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
    		$chile_name = !empty($booking_details[0]['chile_name']) ? $booking_details[0]['chile_name'] : "";
    		$age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";
    		$mobile = !empty($booking_details[0]['mobile']) ? $booking_details[0]['mobile'] : "";
    		$email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
    		
    		$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
    		$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";

			$showTime = !empty($booking_details[0]['show_time']) ? date('g:i A', strtotime($booking_details[0]['show_time'])) : "";
	    	$showTimes = !empty($assign_details[0]['start_datetime']) ? date('g:i A', strtotime($assign_details[0]['start_datetime'])) : "";
	    		
    		$categoryID = !empty($assign_details[0]['role']) ? $assign_details[0]['role'] : "";

    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
    		$showArray = array();
    		if(!empty($categoryDetails)){
    			foreach ($categoryDetails as $categoryList) {

    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
    			}
    		}

    		$showType = !empty($showArray) ? implode(',', $showArray) : "";

    		// Start Performer Email
    		$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();
    		$to_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
    		$booking_id = !empty($booking_request_id) ? base64_encode($booking_request_id) : "";
			    		
			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='RE-SCHEDULE_BOOKING' ")->result_array();

	        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
	        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
	        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";
	        $data = array('SHOW_DATE' => $event_date, 'SHOW_TIME' => $showTimes, 'SHOW_START_TIME' => $showTimes, 'SHOW_DETAILS'=>$show_type_text, 'SHOW_TYPE' => $show_type_text, 'PARTY_ADDRESS' => $party_address,'CHILD_NAME' => $child_name,'CHARACTER' => $showType,'FEE' => $fee,'EMAIL' => $email,'MOBILE' => $mobile,'AGE' => $age,'HOST' => $name, 'DURATION' => $duration , 'ACCEPT' => $accept);
	        //$data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $start_time, 'SHOW_END_TIME' => $end_time,'SHOW_DETAILS'=>$show_type_text, 'SHOW_TYPE' => $showType);
	        if(!empty($data)){
	        	foreach($data as $key => $value){

					$subject = str_replace('{{'.$key.'}}', $value, $subject);
					$message = str_replace('{{'.$key.'}}', $value, $message);
				}
	       	}
//if(!empty($confirmemail)){
	 	 	$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);	
//}
	 	 	// End Performer Email 

	      	if(!empty($result_mail)){
					echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Email Sent successfully!")); die;
	      		      		
	        	}   
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Assign Performer not edited!")); die;
	      	}
    	}else{
    		echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Assign Performer edited Failed!")); die;
	      	
    	}
  	}






//End only email
  	public function deleteAssignPerformer(){

  		$this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

    	if(!empty($jsondata)){

    		$assign_id = !empty($jsondata['assign_id']) ? $jsondata['assign_id'] : "";

    		$assign_details = $this->db->query("SELECT * FROM assign_booking where id='$assign_id';")->result_array();
	        $performer_id = !empty($assign_details[0]['performer_id']) ? $assign_details[0]['performer_id'] : "";
	        $booking_request_id = !empty($assign_details[0]['booking_request_id']) ? $assign_details[0]['booking_request_id'] : "";

	        $showDate = !empty($assign_details[0]['assign_date']) ? $assign_details[0]['assign_date'] : "";
	        $start_time = !empty($assign_details[0]['assign_start_datetime']) ? $assign_details[0]['assign_start_datetime'] : "";
	        $end_time = !empty($assign_details[0]['assign_end_datetime']) ? $assign_details[0]['assign_end_datetime'] : "";


	        $booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id' ")->result_array();
			$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
    		$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
    		$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
    		$showArray = array();
    		if(!empty($categoryDetails)){
    			foreach ($categoryDetails as $categoryList) {

    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
    			}
    		}

    		$showType = !empty($showArray) ? implode(',', $showArray) : "";

    		// Start Performer Email
    		$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();
    		$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
    		  		
			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='ADMIN_DELETE_ASSIGN_PERFORMER' ")->result_array();

	        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
	        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
	        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

	        $data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $start_time, 'SHOW_END_TIME' => $end_time,'SHOW_DETAILS'=>$show_type_text, 'SHOW_TYPE' => $showType);
	        if(!empty($data)){
	        	foreach($data as $key => $value){

					$subject = str_replace('{{'.$key.'}}', $value, $subject);
					$message = str_replace('{{'.$key.'}}', $value, $message);
				}
	       	}

	 	 	$result_mail = $this->sendMail($from_email, $performer_email, $message, $subject);
	 	 	// End Performer Email

    		$this->db->where('id', $assign_id);
  			$result = $this->db->delete('assign_booking');

	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Assign Performer deleted successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Assign Performer does not deleted!")); die;
	      	}
    	}
  	}


  	public function assignAcceptPerformer(){

  		$this->load->view('assignAcceptPerformer');

		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$performer_id = !empty($jsondata['performer_id']) ? base64_decode($jsondata['performer_id']) : "";
	    	$booking_id = !empty($jsondata['booking_id']) ? base64_decode($jsondata['booking_id']) : "";

	    	$cancel_assign_total = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_id' and performer_id='$performer_id' and status='Reject';")->result_array();
			$cancel_status_count = !empty($cancel_assign_total[0]['status_count']) ? $cancel_assign_total[0]['status_count'] : 0;

			if($cancel_status_count =='0'){

				$assign_total = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_id' and performer_id='$performer_id' and status='Accept';")->result_array();
				$status_count = !empty($assign_total[0]['status_count']) ? $assign_total[0]['status_count'] : 0;

				//if($status_count =='0'){
		    	
			    	$postData = array(
						'status'=> 'Accept',
						'status_count'=> '1',
						'updated_on'=> date("Y-m-d H:i:s")
					);
					$postWhere = array(
						'booking_request_id' => $booking_id,
						'performer_id' => $performer_id
					);
					$this->db->where($postWhere);
					//	$assign_result = $this->db->insert('assign_booking', $postData);
					$assign_result = $this->db->update('assign_booking', $postData);


					$postData1 = array(
						'assign_status'=> 'Assign'
					);
					$postWhere1 = array(
						'id=' => $booking_id
					);
					$this->db->where($postWhere1);
					$result = $this->db->update('booking_request',$postData1);


					$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_id';")->result_array();

		    		$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
		    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
		    		$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
		    		$child_name= !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";		    		
		    		$fee= !empty($booking_details[0]['fee']) ? $booking_details[0]['fee'] : "";

			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
		    		$email= !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

		    		$age= !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";

		    		$name= !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
		    		$duration= !empty($booking_details[0]['duration']) ? $booking_details[0]['duration'] : "";
		    		$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
		    		$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

		    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
		    		$showArray = array();
		    		if(!empty($categoryDetails)){
		    			foreach ($categoryDetails as $categoryList) {

		    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
		    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
		    			}
		    		}

		    		$showType = !empty($showArray) ? implode(',', $showArray) : "";

		    		// Start Admin History
		    		$historyMessage = "performers accept request ".$name." booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'Performer Accept', $historyMessage);
					// End Admin History

		    		// Start Admin Email
		    		$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
		    		$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

		    		if(!empty($admin_email)){

		    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='ADMIN_ASSIGN_PERFORMER' ")->result_array();

				        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
				        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
				        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

				        // $data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'ADDRESS' => $party_address,'SHOW_DETAILS'=>$show_type_text,'SHOW_TYPE' => $showType);
				        $data = array('SHOW_DATE' => $event_date,'SHOW_TIME' => $show_time, 'SHOW_START_TIME' => $show_time, 'SHOW_TYPE' => $role, 'PARTY_ADDRESS' => $party_address,'CHILD_NAME' => $child_name,'CHARACTER' => $showType,'SHOW_DETAILS'=>$show_type_text,'FEE' => $fee,'EMAIL' => $email,'MOBILE' => $mobile,'AGE' => $age,'HOST' => $name,'DURATION' => $duration , 'ACCEPT' => $accept);
				        if(!empty($data)){
				        	foreach($data as $key => $value){

								$subject = str_replace('{{'.$key.'}}', $value, $subject);
								$message = str_replace('{{'.$key.'}}', $value, $message);
							}
				       	}

				        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
		    		}
		    		// End Admin Email

			      	if(!empty($result)){
			        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Performer accept successfully!")); die;
			      	}
			      	else{
			        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Performer does not accept!")); die;
			      	}
			    // }
			    // else{
		     //    	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "You have already accepted gig!")); die;
		     //  	}  	
			}
			else{
				echo json_encode(array('status'=>2, 'type'=>'error', "message" => "Performer already cancelled gig!")); die;
			}
    	}
  	}

  	public function cancelassignPerformer(){

  		$this->load->view('assignAcceptPerformer');

		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$performer_id = !empty($jsondata['performer_id']) ? base64_decode($jsondata['performer_id']) : "";
	    	$booking_id = !empty($jsondata['booking_id']) ? base64_decode($jsondata['booking_id']) : "";

	    	$cancel_assign_total = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_id' and performer_id='$performer_id' and status='Reject';")->result_array();
			$cancel_status_count = !empty($cancel_assign_total[0]['status_count']) ? $cancel_assign_total[0]['status_count'] : 0;

			if($cancel_status_count =='0'){

				$assign_total = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_id' and performer_id='$performer_id' and status='Accept';")->result_array();
				$status_count = !empty($assign_total[0]['status_count']) ? $assign_total[0]['status_count'] : 0;

				//if($status_count =='0'){
		    	
			    	$postData = array(
						'status'=> 'Accept',
						'status_count'=> '1',
						'updated_on'=> date("Y-m-d H:i:s")
					);
					$postWhere = array(
						'booking_request_id' => $booking_id,
						'performer_id' => $performer_id
					);
					$this->db->where($postWhere);
					//	$assign_result = $this->db->insert('assign_booking', $postData);
					$assign_result = $this->db->update('assign_booking', $postData);


					$postData1 = array(
						'assign_status'=> 'Assign'
					);
					$postWhere1 = array(
						'id=' => $booking_id
					);
					$this->db->where($postWhere1);
					$result = $this->db->update('booking_request',$postData1);


					$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_id';")->result_array();

		    		$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
		    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
		    		$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
		    		
			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
		    		$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
		    		$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

		    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
		    		$showArray = array();
		    		if(!empty($categoryDetails)){
		    			foreach ($categoryDetails as $categoryList) {

		    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
		    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
		    			}
		    		}

		    		$showType = !empty($showArray) ? implode(',', $showArray) : "";

		    		// Start Admin History
		    		$historyMessage = "performers accept request ".$name." booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'Performer Accept', $historyMessage);
					// End Admin History

		    		// Start Admin Email
		    		$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
		    		$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

		    		if(!empty($admin_email)){

		    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='ACCEPT_PERFORMER' ")->result_array();

				        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
				        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
				        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

				        // $data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime,'SHOW_DETAILS'=>$show_type_text, 'ADDRESS' => $party_address,'SHOW_TYPE' => $showType);
				        $data = array('SHOW_DATE' => $event_date,'SHOW_TIME' => $show_time, 'SHOW_START_TIME' => $show_time, 'SHOW_TYPE' => $role, 'PARTY_ADDRESS' => $party_address,'CHILD_NAME' => $child_name,'CHARACTER' => $showType,'SHOW_DETAILS'=>$show_type_text,'FEE' => $fee,'EMAIL' => $email,'MOBILE' => $mobile,'AGE' => $age,'HOST' => $name,'DURATION' => $duration , 'ACCEPT' => $accept)	;
				        if(!empty($data)){
				        	foreach($data as $key => $value){

								$subject = str_replace('{{'.$key.'}}', $value, $subject);
								$message = str_replace('{{'.$key.'}}', $value, $message);
							}
				       	}

				        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
		    		}
		    		// End Admin Email

			      	if(!empty($result)){
			        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Performer accept successfully!")); die;
			      	}
			      	else{
			        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Performer does not accept!")); die;
			      	}
			    // }
			    // else{
		     //    	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "You have already accepted gig!")); die;
		     //  	}  	
			}
			else{
				echo json_encode(array('status'=>2, 'type'=>'error', "message" => "Performer already cancelled gig!")); die;
			}
    	}
  	}

  	public function assignRejectPerformer(){

  		$this->load->view('assignRejectPerformer');

		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$performer_id = !empty($jsondata['performer_id']) ? base64_decode($jsondata['performer_id']) : "";
	    	$booking_id = !empty($jsondata['booking_id']) ? base64_decode($jsondata['booking_id']) : "";
	    	$reason = !empty($jsondata['reason']) ? $jsondata['reason'] : "";

	    	$accept_assign_total = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_id' and performer_id='$performer_id' and status='Accept';")->result_array();
			$accept_status_count = !empty($accept_assign_total[0]['status_count']) ? $accept_assign_total[0]['status_count'] : 0;

			if($accept_status_count =='0'){

				$assign_total = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$booking_id' and performer_id='$performer_id' and status='Reject';")->result_array();
				$status_count = !empty($assign_total[0]['status_count']) ? $assign_total[0]['status_count'] : 0;

				if($status_count =='0'){

			    	$postData = array(
						'status'=> 'Reject',
						'reason'=> $reason,
						'status_count'=> '1',
						'updated_on'=> date("Y-m-d H:i:s")
					);
					$postWhere = array(
						'booking_request_id' => $booking_id,
						'performer_id' => $performer_id
					);
					$this->db->where($postWhere);
					$assign_result = $this->db->insert('assign_booking', $postData);

					$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_id';")->result_array();

		    		$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
		    		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
		    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
		    		
			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
		    		$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
		    		$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

		    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
		    		$showArray = array();
		    		if(!empty($categoryDetails)){
		    			foreach ($categoryDetails as $categoryList) {

		    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
		    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
		    			}
		    		}

		    		$showType = !empty($showArray) ? implode(',', $showArray) : "";

		    		// Start Admin History
		    		$historyMessage = "performers reject request ".$name." booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'Performer Reject', $historyMessage);
					// End Admin History

		    		// Start Admin Email
		    		$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
		    		$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

		    		if(!empty($admin_email)){

		    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='REJECT_PERFORMER' ")->result_array();

				        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
				        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
				        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

				        $data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType,'SHOW_DETAILS'=>$show_type_text);
				        if(!empty($data)){
				        	foreach($data as $key => $value){

								$subject = str_replace('{{'.$key.'}}', $value, $subject);
								$message = str_replace('{{'.$key.'}}', $value, $message);
							}
				       	}

				        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
		    		}
		    		// End Admin Email

			      	if(!empty($result)){
			        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Performer reject successfully!")); die;
			      	}
			      	else{
			        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Performer does not reject!")); die;
			      	}
		      	}
			    else{
		        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "You already cancelled gig!")); die;
		      	}  	
			}
			else{
				echo json_encode(array('status'=>2, 'type'=>'error', "message" => "You already accepted gig!")); die;
			}
    	}
  	}

  	public function sessionExpire(){
  		$this->load->view('sessionExpire');
  	}

  	public function cancelEvent(){
  		$this->load->view('cancelEvent');
  	}


  	public function thankYou(){
  		$this->load->view('thankYou');
  	}

  	public function thankEvent(){
  		$this->load->view('thankEvent');
  	}


  	public function cronAssignPerformer(){

  		$assign_details = $this->db->query("SELECT * FROM assign_booking where status_count='0';")->result_array();
  		if($assign_details){
  			foreach ($assign_details as $assignList) {

				$id = !empty($assignList['id']) ? $assignList['id'] : "";
  				$booking_request_id = !empty($assignList['booking_request_id']) ? $assignList['booking_request_id'] : "";
  				$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";
  				$status_count = !empty($assignList['status_count']) ? $assignList['status_count'] : "";

  				$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id' ")->result_array();
  		
		  		$booking_status = !empty($booking_details[0]['booking_status']) ? $booking_details[0]['booking_status'] : "";

		  		if($booking_status != 'Cancelled'){

			  		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
					$to_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

					$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
					$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
					
					$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

					$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
					$showArray = array();
					if(!empty($categoryDetails)){
						foreach ($categoryDetails as $categoryList) {

							$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
							$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}
					}

					$showType = !empty($showArray) ? implode(' , ', $showArray) : "";

	  				// Start Admin History
					$historyMessage = "Admin sent performer accept or reject mail to  booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'Performer Accept or Reject mail', $historyMessage);
					// End Admin History

					$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='DETAIL_FORM_REQUEST' ")->result_array();

			        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
			        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
			        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

			        $booking_id = !empty($booking_request_id) ? base64_encode($booking_request_id) : "";

			        $url = HTTP_CONTROLLER."generalAddBooking/?".$booking_id;

			        $data = array('LINK' => $url, 'SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType);
			        if(!empty($data)){
			        	foreach($data as $key => $value){
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			 	 	$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);
			 	 	if($result_mail){

			 	 		$postData = array(
							'mail_count'=> '1',
							'updated_on'=> date("Y-m-d H:i:s")
						);
						$postWhere = array(
							'id=' => $booking_request_id
						);

						$this->db->where($postWhere);
						$result = $this->db->update('booking_request',$postData);
			 	 	}
		  				
	  				// Start Admin Email
					$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
					$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

					if(!empty($admin_email)){
				        $result_mail_admin = $this->sendMail($from_email, $admin_email, $message, $subject);
					}
					// End Admin Email
				}	
			}	
  		}	
  	}



  	public function performerList(){

	    $this->checkLogin();  

	    $response = array();
	    $temp = array();

      	$performer_details = $this->db->query("SELECT * FROM performers;")->result_array();
      
      	if(!empty($performer_details)){
	        foreach($performer_details  as $performer_list){
	          
	          	$temp['id'] = !empty($performer_list['id']) ? $performer_list['id'] : "";
	          	$temp['name'] = !empty($performer_list['name']) ? $performer_list['name'] : "";
	          	
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

  	public function completeFormMail($id){

  		$this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);

		if(!empty($jsondata)){

			$id = !empty($jsondata['id']) ? $jsondata['id'] : "";

	  		$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id' ")->result_array();
	  		
	  		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
			$to_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

			$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
			$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
			
			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
			$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
			$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

			$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
			$showArray = array();
			if(!empty($categoryDetails)){
				foreach ($categoryDetails as $categoryList) {

					$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
					$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
				}
			}

			$showType = !empty($showArray) ? implode(' ,\n ', $showArray) : " ";

			$historyMessage = "Admin sent mail to complete form ".$name." to complete form for booking request details";
			$resultHistory = $this->addHistory('Booking Request', 'Email Complete Form', $historyMessage);

			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='DETAIL_FORM_REQUEST' ")->result_array();

	        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
	        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
	        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

	        $booking_id = !empty($id) ? base64_encode($id) : "";

	        $url = HTTP_CONTROLLER."generalAddBooking/?".$booking_id;

	        $data = array('LINK' => $url, 'SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType,'SHOW_DETAILS'=>$show_type_text);
	        if(!empty($data)){
	        	foreach($data as $key => $value){
					$message = str_replace('{{'.$key.'}}', $value, $message);
				}
	       	}

	 	 	$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);
	 	 	
	 	 	if(!empty($result_mail)){

	 	 		$postData = array(
					'mail_count'=> '1',
					'updated_on'=> date("Y-m-d H:i:s")
				);

				$postWhere = array(
					'id=' => $id
				);

				$this->db->where($postWhere);
				$result = $this->db->update('booking_request',$postData);

	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Detail mail sent successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Detail mail does not sent!")); die;
	      	}
 	 	}
  	}

  	public function unblockPerformer(){

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$currentDate = date("Y-m-d");

    		$assign_performer_details = $this->db->query("SELECT * FROM assign_booking where assign_date='$currentDate';")->result_array();
    		$arrBlock = array();
    		if(!empty($assign_performer_details)){
				foreach($assign_performer_details as $assign_list){
					$arrBlock[] = $assign_list['performer_id'];
				}
			}
			$assign_performer_id = implode(',', $arrBlock);

	      	if(!empty($jsondata['searchtype']) || !empty($jsondata['category_id'])){
	        	$searchdata = !empty($jsondata['searchtype']) ? $jsondata['searchtype'] : "";
	        	$category = !empty($jsondata['category_id']) ? $jsondata['category_id'] : "";
	  		} 
	      	else{
	        	$searchdata = "";
	        	$category = "";
	      	}

	      	if(!empty($category)){
		        $performer_total = $this->db->query("SELECT count(*) as total FROM performers where block_status='0' and (category like '%$category%');")->result_array();
		        $total = !empty($performer_total[0]['total']) ? $performer_total[0]['total'] : 0;
	      	}
	      	else if(!empty($searchdata)){
	      		$performer_total = $this->db->query("SELECT count(*) as total FROM performers where block_status='0' and (name like '%$searchdata%');")->result_array();
		        $total = !empty($performer_total[0]['total']) ? $performer_total[0]['total'] : 0;
	      	}
	      	else{
		        $performer_total = $this->db->query("SELECT count(*) as total FROM performers where block_status='0' ;")->result_array();
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

	      	if(!empty($category)){
	      		$performer_details = $this->db->query("SELECT * FROM performers where block_status='0' and (category like '%$category%') $limitto ;")->result_array();
	      	}
	      	else if(!empty($searchdata)){
	      		$performer_details = $this->db->query("SELECT * FROM performers where block_status='0' and (name like '%$searchdata%') $limitto ;")->result_array();
	      	}
	      	else{
	      		$performer_details = $this->db->query("SELECT * FROM performers where block_status='0' $limitto ;")->result_array();
	      	}
      
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

		          	$category_id = !empty($performer_list['category']) ? $performer_list['category'] : "";

		          	$show_category_details = explode(",",$category_id);
		          	$typeArray = array();
					
					if(!empty($show_category_details)){
						foreach($show_category_details as $show_category_list){
							
							$category_details = $this->db->query("SELECT * FROM categories where id='$show_category_list';")->result_array();
							$typeArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}	
					}
					else{
						$typeArray[] = "";
					}

					$temp['category'] = !empty($typeArray) ? implode(",",$typeArray) : "";

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

  	

  	public function performerBooking(){

  		$jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$id = !empty($jsondata['id']) ? base64_decode($jsondata['id']) : "";

	      	$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id';")->result_array();
	      
	      	if(!empty($assign_details)){
	        	foreach($assign_details  as $assign_list){
	          
		          	$temp['performer_id'] = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";
		          	$performer_id = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";

		          	$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id';")->result_array();
		          	$temp['name'] = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
		          	$temp['email'] = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
		          	$temp['mobile'] = !empty($performer_details[0]['mobile_a']) ? $performer_details[0]['mobile_a'] : "";
		          	$temp['description'] = !empty($performer_details[0]['description']) ? $performer_details[0]['description'] : "";
		          	$temp['profile_image'] = !empty($performer_details[0]['profile_image']) ? GET_PERFORMER_IMAGE_PATH.$performer_details[0]['profile_image'] : GET_DUMMY_IMAGE_PATH.'dummy.png';

		          	$category_id = !empty($performer_details[0]['category']) ? $performer_details[0]['category'] : "";
		          	$category_details = $this->db->query("SELECT * FROM categories where id='$category_id' ;")->result_array();
		          	$temp['category_name'] = !empty($category_details['0']['category']) ? $category_details['0']['category'] : "";
		          	
		          	$response[] =$temp;
	        	}
	      	}
      
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Assign Performer List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Assign Performer List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	


  	public function paperworkMail(){

  		$this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);

		if(!empty($jsondata)){

			$id = !empty($jsondata['id']) ? $jsondata['id'] : "";

	  		$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id' ")->result_array();
	  		
	  		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
			$to_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='CHASING_PAPERWORK' ")->result_array();

	        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
	        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
	        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

	        $booking_id = !empty($id) ? base64_encode($id) : "";

	        $url = HTTP_CONTROLLER."generalAddBooking/?".$booking_id;

	        $data = array('LINK' => $url, 'SHOW_NAME' => $name);
	        if(!empty($data)){
	        	foreach($data as $key => $value){
					$message = str_replace('{{'.$key.'}}', $value, $message);
				}
	       	}

	 	 	$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);

	 	 	// Start Admin History
			$historyMessage = "Admin sent paperwork mail to ".$name." booking request details";
			$resultHistory = $this->addHistory('Booking Request', 'paperwork mail', $historyMessage);
			// End Admin History

	 	 	// Start Admin Email
			$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
			$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

			if(!empty($admin_email)){

		        $result_mail_admin = $this->sendMail($from_email, $admin_email, $message, $subject);
			}
			// End Admin Email
	        
	 	 	if(!empty($result_mail)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Paper Work mail sent successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Paper Work mail does not sent!")); die;
	      	}
 	 	}
  	}

  	public function cronPaperworkMail(){

  		$booking_details = $this->db->query("SELECT * FROM booking_request")->result_array();
  		if($booking_details){
  			foreach ($booking_details as $bookingList) {

  				$booking_status = !empty($bookingList['booking_status']) ? $bookingList['booking_status'] : "";

  				if($booking_status != 'Cancelled'){

	  				$paperwork_count = !empty($bookingList['paperwork_count']) ? $bookingList['paperwork_count'] : "";

	  				if($paperwork_count == '0'){

	  					$id = !empty($bookingList['id']) ? $bookingList['id'] : "";
	  				
		  				$name = !empty($bookingList['name']) ? $bookingList['name'] : "";
		  				$to_email = !empty($bookingList['email']) ? $bookingList['email'] : "";

		  				// Start Admin History
						$historyMessage = "Admin sent paperwork mail to ".$name." booking request details";
						$resultHistory = $this->addHistory('Booking Request', 'paperwork mail', $historyMessage);
						// End Admin History

		  				$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='CHASING_PAPERWORK' ")->result_array();

		  				$subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
				        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
				        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

				        $booking_id = !empty($id) ? base64_encode($id) : "";

				        $url = HTTP_CONTROLLER."generalAddBooking/?".$booking_id;

				        $data = array('LINK' => $url, 'SHOW_NAME' => $name);
				        if(!empty($data)){
				        	foreach($data as $key => $value){
								$message = str_replace('{{'.$key.'}}', $value, $message);
							}
				       	}

				 	 	$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);
		  				
		  				// Start Admin Email
						$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
						$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

						if(!empty($admin_email)){

					        $result_mail_admin = $this->sendMail($from_email, $admin_email, $message, $subject);
						}
						// End Admin Email
	  				}
	  			}
  			}
  		}	
  	}


  	public function generalAddBooking(){

  		$this->load->view('generalAddBooking');

		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$id = !empty($jsondata['id']) ? base64_decode($jsondata['id']) : "";

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

	    	$postData = array(
				'id'=> $id,
				'hear_supersteph'=> $hear_supersteph,
				'party_bags'=> $party_bags,
				'party_address'=> $party_address,
				'home_address'=> $home_address,
				'parking_facility'=> $parking_facility,
				'party_in_out'=> $party_in_out,
				'rain_plan'=> $rain_plan,
				'second_mobile'=> $second_mobile,
				'child_name'=> $child_name,
				'age'=> $age,
				'dob'=> $dob,
				'children_party'=> $children_party,
				'gender_party'=> $gender_party,
				'children_count'=> $children_count,
				'party_seen'=> $party_seen,
				'show_fullname'=> $show_fullname,
				//'booking_status'=> 'Pending',
				'paperwork_count'=> '1',
				'looking_count'=> '0',
				'updated_on'=> date("Y-m-d H:i:s")
			);

			$this->db->where('id',$id);
    		$result = $this->db->update('booking_request',$postData);

    		$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id';")->result_array();

    		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
    		$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
    		
			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
    		$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

    		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
    		$showArray = array();
    		if(!empty($categoryDetails)){
    			foreach ($categoryDetails as $categoryList) {

    				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
    				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
    			}
    		}

    		$showType = !empty($showArray) ? implode(',', $showArray) : "";

    		// Start Admin History
    		$historyMessage = "Admin added ".$name." booking request details";
			$resultHistory = $this->addHistory('Booking Request', 'Complete Form', $historyMessage);
			// End Admin History

    		// Start Admin Email
    		$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
    		$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

    		if(!empty($admin_email)){

    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='FILL_FORM_CUSTOMER' ")->result_array();

		        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
		        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
		        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

		        $data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType);
		        if(!empty($data)){
		        	foreach($data as $key => $value){

						$subject = str_replace('{{'.$key.'}}', $value, $subject);
						$message = str_replace('{{'.$key.'}}', $value, $message);
					}
		       	}

		        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
    		}
    		// End Admin Email
	    	
	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking added successfully!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Booking does not reject!", 'response' =>$response)); die;
	      	}
    	}
  	}



  	public function lookingForwardMail(){

  		$this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);

		if(!empty($jsondata)){

			$id = !empty($jsondata['id']) ? $jsondata['id'] : "";

	  		$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id' ")->result_array();
	  		
	  		$name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
	    	$email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
	    	$event_date = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";

			$show_time = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
	    	$duration = !empty($booking_details[0]['duration']) ? $booking_details[0]['duration'] : "";
	    	$host = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";

	    	$party_bags = !empty($booking_details[0]['party_bags']) ? $booking_details[0]['party_bags'] : "";
	    	$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
	    	$home_address = !empty($booking_details[0]['home_address']) ? $booking_details[0]['home_address'] : "";
	    	$parking_facility = !empty($booking_details[0]['parking_facility']) ? $booking_details[0]['parking_facility'] : "";

	    	$party_in_out = !empty($booking_details[0]['party_in_out']) ? $booking_details[0]['party_in_out'] : "";
	    	$rain_plan = !empty($booking_details[0]['rain_plan']) ? $booking_details[0]['rain_plan'] : "";
	    	$age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";

	    	$dob = !empty($booking_details[0]['dob']) ? $booking_details[0]['dob'] : "";
	    	$children_count = !empty($booking_details[0]['children_count']) ? $booking_details[0]['children_count'] : "";
	    	$children_party = !empty($booking_details[0]['children_party']) ? $booking_details[0]['children_party'] : "";

	    	$gender_party = !empty($booking_details[0]['gender_party']) ? $booking_details[0]['gender_party'] : "";
	    	$party_seen = !empty($booking_details[0]['party_seen']) ? $booking_details[0]['party_seen'] : "";
	    	$mobile_number = !empty($booking_details[0]['mobile_number']) ? $booking_details[0]['mobile_number'] : "";
	    	$second_mobile = !empty($booking_details[0]['second_mobile']) ? $booking_details[0]['second_mobile'] : "";

			
			$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
			$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

			$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
			$showArray = array();
			if(!empty($categoryDetails)){
				foreach ($categoryDetails as $categoryList) {

					$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
					$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
				}
			}

			$show_type = !empty($showArray) ? implode(',', $showArray) : "";

			$performer_message = "";
			$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' and status='Accept' ")->result_array();
			if(!empty($assign_details)){
				foreach ($assign_details as $assignList) {
					$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

					$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

					$performer_name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
					$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
					$performer_mobile = !empty($performer_details[0]['mobile_a']) ? $performer_details[0]['mobile_a'] : "";

					$performer_message.='Performer name: '.$performer_name.'   ';
					$performer_message.='Performer email: '.$performer_email.'   ';
					$performer_message.='Performer mobile: '.$performer_mobile.'   ';
				}
			}

			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='LOOKING_FORWARD_YOU' ")->result_array();

	        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
	        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
	        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

	        
	        $data = array('HOST' =>$host, 'SHOW_DATE' => $event_date, 'EMAIL' => $email, 'SHOW_TIME' => $show_time, 'DURATION' => $duration, 'SHOW_TYPE' => $show_type,'SHOW_DETAILS'=>$show_type_text, 'PARTY_BAGS' => $party_bags, 'PARTY_ADDRESS' => $party_address, 'HOME_ADDRESS' => $home_address, 'PARKING_FACILITY' => $parking_facility, 'PARTY_IN_OUT' => $party_in_out, 'RAIN_PLAN' => $rain_plan, 'CHILD_NAME' => $name, 'AGE' => $age, 'DOB' => $dob, 'CHILDREN_COUNT' => $children_count, 'CHILDREN_PARTY' => $children_party, 'GENDER_PARTY' => $gender_party, 'PARTY_SEEN' => $party_seen, 'MOBILE_NUMBER' => $mobile_number, 'SECOND_MOBILE' => $second_mobile, 'PERFORMER_DETAILS' => $performer_message);
	        if(!empty($data)){
	        	foreach($data as $key => $value){
					$subject = str_replace('{{'.$key.'}}', $value, $subject);
					$message = str_replace('{{'.$key.'}}', $value, $message);
				}
	       	}

	       	// Start Admin History
			$historyMessage = "Admin sent mail to ".$name." booking request details";
			$resultHistory = $this->addHistory('Booking Request', 'Looking Forward mail', $historyMessage);
			// End Admin History

	       	// Start Customer Email
	       	$customer_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

	 	 	$result_mail = $this->sendMail($from_email, $customer_email, $message, $subject);
	 	 	// End Customer Email


	 	 	// Start Performer Email
	       	$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' and status='Accept' ")->result_array();
			if(!empty($assign_details)){
				foreach ($assign_details as $assignList) {
					$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

					$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

					$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
					
					$result_mail_performer = $this->sendMail($from_email, $performer_email, $message, $subject);
				}
			}
	 	 	// End Performer Email

	 	 	// Start Admin Email
			$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
			$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

			if(!empty($admin_email)){
		        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
			}
			// End Admin Email

	 	 	if(!empty($result_mail)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Looking Forward mail sent successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Looking Forward mail does not sent!")); die;
	      	}
	 	}	
  	}

  	public function cronLookingForwardMail(){

  		$booking_details = $this->db->query("SELECT * FROM booking_request")->result_array();
  		if($booking_details){
  			foreach ($booking_details as $bookingList) {

  				$booking_status = !empty($bookingList['booking_status']) ? $bookingList['booking_status'] : "";

  				if($booking_status != 'Cancelled'){

	  				$id = !empty($bookingList['id']) ? $bookingList['id'] : "";
	  				$name = !empty($bookingList['child_name']) ? $bookingList['child_name'] : "";
	  				$email = !empty($bookingList['email']) ? $bookingList['email'] : "";
	  				$event_date = !empty($bookingList['event_date']) ? $bookingList['event_date'] : "";

	  				$show_time = !empty($bookingList['show_time']) ? $bookingList['show_time'] : "";
	  				$duration = !empty($bookingList['duration']) ? $bookingList['duration'] : "";
	  				$host = !empty($bookingList['name']) ? $bookingList['name'] : "";

	  				$party_bags = !empty($bookingList['party_bags']) ? $bookingList['party_bags'] : "";
	  				$party_address = !empty($bookingList['party_address']) ? $bookingList['party_address'] : "";
	  				$home_address = !empty($bookingList['home_address']) ? $bookingList['home_address'] : "";
	  				$parking_facility = !empty($bookingList['parking_facility']) ? $bookingList['parking_facility'] : "";

	  				$party_in_out = !empty($bookingList['party_in_out']) ? $bookingList['party_in_out'] : "";
	  				$rain_plan = !empty($bookingList['rain_plan']) ? $bookingList['rain_plan'] : "";
	  				$age = !empty($bookingList['age']) ? $bookingList['age'] : "";

	  				$dob = !empty($bookingList['dob']) ? $bookingList['dob'] : "";
	  				$children_count = !empty($bookingList['children_count']) ? $bookingList['children_count'] : "";
	  				$children_party = !empty($bookingList['children_party']) ? $bookingList['children_party'] : "";


	  				$gender_party = !empty($bookingList['gender_party']) ? $bookingList['gender_party'] : "";
	  				$party_seen = !empty($bookingList['party_seen']) ? $bookingList['party_seen'] : "";
	  				$mobile_number = !empty($bookingList['mobile_number']) ? $bookingList['mobile_number'] : "";
	  				$second_mobile = !empty($bookingList['second_mobile']) ? $bookingList['second_mobile'] : "";


	  				$categoryID = !empty($bookingList['show_type']) ? $bookingList['show_type'] : "";

					$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
					$showArray = array();
					if(!empty($categoryDetails)){
						foreach ($categoryDetails as $categoryList) {

							$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
							$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}
					}

					$show_type = !empty($showArray) ? implode(',', $showArray) : "";

					$performer_message = "";
					$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' and status='Accept' ")->result_array();
					if(!empty($assign_details)){
						foreach ($assign_details as $assignList) {
							$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

							$performer_name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
							$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
							$performer_mobile = !empty($performer_details[0]['mobile_a']) ? $performer_details[0]['mobile_a'] : "";

							$performer_message.='Performer name: '.$performer_name.'   ';
							$performer_message.='Performer email: '.$performer_email.'   ';
							$performer_message.='Performer mobile: '.$performer_mobile.'   ';
						}
					}

					$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='LOOKING_FORWARD_YOU' ")->result_array();

			        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
			        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
			        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

			        
			        $data = array('HOST' =>$host, 'SHOW_DATE' => $event_date, 'EMAIL' => $email, 'SHOW_TIME' => $show_time, 'DURATION' => $duration, 'SHOW_TYPE' => $show_type, 'PARTY_BAGS' => $party_bags, 'PARTY_ADDRESS' => $party_address, 'HOME_ADDRESS' => $home_address, 'PARKING_FACILITY' => $parking_facility, 'PARTY_IN_OUT' => $party_in_out, 'RAIN_PLAN' => $rain_plan, 'CHILD_NAME' => $name, 'AGE' => $age, 'DOB' => $dob, 'CHILDREN_COUNT' => $children_count, 'CHILDREN_PARTY' => $children_party, 'GENDER_PARTY' => $gender_party, 'PARTY_SEEN' => $party_seen, 'MOBILE_NUMBER' => $mobile_number, 'SECOND_MOBILE' => $second_mobile, 'PERFORMER_DETAILS' => $performer_message);
			        if(!empty($data)){
			        	foreach($data as $key => $value){
							$subject = str_replace('{{'.$key.'}}', $value, $subject);
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}


	  				$showDate = !empty($bookingList['event_date']) ? $bookingList['event_date'] : "";

	  				$currentDate = date("Y-m-d");
	  				$yesterday = date('Y-m-d', strtotime($showDate . ' -1 day'));
	  				$three_day = date('Y-m-d', strtotime($showDate . ' -3 day'));
	  				$five_day = date('Y-m-d', strtotime($showDate . ' -5 day'));
	  				$ten_day = date('Y-m-d', strtotime($showDate . ' -10 day'));

	  				if($currentDate == $yesterday){

	  					// Start Admin History
						$historyMessage = "Admin sent mail to ".$name." booking request details";
						$resultHistory = $this->addHistory('Booking Request', 'Looking Forward mail', $historyMessage);
						// End Admin History

	  					// Start Customer Email
				       	$customer_email = !empty($bookingList['email']) ? $bookingList['email'] : "";

				 	 	$result_mail = $this->sendMail($from_email, $customer_email, $message, $subject);
				 	 	// End Customer Email


				 	 	// Start Performer Email
				       	$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' and status='Accept' ")->result_array();
						if(!empty($assign_details)){
							foreach ($assign_details as $assignList) {
								$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

								$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

								$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
								
								$result_mail_performer = $this->sendMail($from_email, $performer_email, $message, $subject);
							}
						}
				 	 	// End Performer Email

				 	 	// Start Admin Email
						$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
						$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

						if(!empty($admin_email)){
					        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
						}
						// End Admin Email
	  				}

	  				if($currentDate == $three_day){

	  					// Start Admin History
						$historyMessage = "Admin sent mail to ".$name." booking request details";
						$resultHistory = $this->addHistory('Booking Request', 'Looking Forward mail', $historyMessage);
						// End Admin History

	  					// Start Customer Email
				       	$customer_email = !empty($bookingList['email']) ? $bookingList['email'] : "";

				 	 	$result_mail = $this->sendMail($from_email, $customer_email, $message, $subject);
				 	 	// End Customer Email


				 	 	// Start Performer Email
				       	$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' and status='Accept' ")->result_array();
						if(!empty($assign_details)){
							foreach ($assign_details as $assignList) {
								$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

								$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

								$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
								
								$result_mail_performer = $this->sendMail($from_email, $performer_email, $message, $subject);
							}
						}
				 	 	// End Performer Email

				 	 	// Start Admin Email
						$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
						$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

						if(!empty($admin_email)){
					        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
						}
						// End Admin Email

	  				}
	  				if($currentDate == $five_day){

	  					// Start Admin History
						$historyMessage = "Admin sent mail to ".$name." booking request details";
						$resultHistory = $this->addHistory('Booking Request', 'Looking Forward mail', $historyMessage);
						// End Admin History

	  					// Start Customer Email
				       	$customer_email = !empty($bookingList['email']) ? $bookingList['email'] : "";

				 	 	$result_mail = $this->sendMail($from_email, $customer_email, $message, $subject);
				 	 	// End Customer Email


				 	 	// Start Performer Email
				       	$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' and status='Accept' ")->result_array();
						if(!empty($assign_details)){
							foreach ($assign_details as $assignList) {
								$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

								$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

								$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
								
								$result_mail_performer = $this->sendMail($from_email, $performer_email, $message, $subject);
							}
						}
				 	 	// End Performer Email

				 	 	// Start Admin Email
						$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
						$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

						if(!empty($admin_email)){
					        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
						}
						// End Admin Email
	  					
	  				}
  				}
  			}
  		}	
  	}


  	public function paymentMail(){

  		$this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);

		if(!empty($jsondata)){

			$id = !empty($jsondata['id']) ? $jsondata['id'] : "";

			$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id' ")->result_array();
  		
	  		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
			$to_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='CHASING_PAYMENTS' ")->result_array();

	        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
	        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
	        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

	        $booking_id = !empty($id) ? base64_encode($id) : "";

	        $data = array('SHOW_NAME' => $name);
	        if(!empty($data)){
	        	foreach($data as $key => $value){
					$message = str_replace('{{'.$key.'}}', $value, $message);
				}
	       	}

	 	 	$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);

	 	 	// Start Admin History
			$historyMessage = "Admin sent payment mail to ".$name." booking request details";
			$resultHistory = $this->addHistory('Booking Request', 'Payments mail', $historyMessage);
			// End Admin History

	 	 	// Start Admin Email
			$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
			$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

			if(!empty($admin_email)){

		        $result_mail_admin = $this->sendMail($from_email, $admin_email, $message, $subject);
			}
			// End Admin Email
	        
	 	 	if(!empty($result_mail)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Payment mail sent successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Payment mail does not sent!")); die;
	      	}
		}
  	}




	public function category(){

	    $this->load->view('header');
	    $this->load->view('category');
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
		        $category_total = $this->db->query("SELECT count(*) as total FROM categories where (category like '%$searchdata%');")->result_array();
		        $total = !empty($category_total[0]['total']) ? $category_total[0]['total'] : 0;
	      	}
	      	else{
		        $category_total = $this->db->query("SELECT count(*) as total FROM categories;")->result_array();
		        $total = !empty($category_total[0]['total']) ? $category_total[0]['total'] : 0;
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
      
	      	$category_details = $this->db->query("SELECT * FROM categories where (category like '%$searchdata%') order by id desc $limitto;")->result_array();
	      
	      	if(!empty($category_details)){
		        foreach($category_details  as $category_list){
		          
		          	$temp['id'] = !empty($category_list['id']) ? $category_list['id'] : "";
		          	$temp['category_name'] = !empty($category_list['category']) ? $category_list['category'] : "";
		          	
		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Category List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Category List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function addcategory(){

	    $this->load->view('header');
	    $this->load->view('addcategory');
	    $this->load->view('footer');

	    $this->checkLogin();  
	    
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$category = !empty($jsondata['category_name']) ? $jsondata['category_name'] : "";
	    	
			$postData = array(
				'category'=> $category,
				'created_on'=>date('Y-m-d H:i:s')
			);
    		$result = $this->db->insert('categories',$postData);

    		// Start Admin History
			$historyMessage = "Admin added ".$category;
			$resultHistory = $this->addHistory('Category', 'Add', $historyMessage);
			// End Admin History

	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Category added successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Category not added!")); die;
	      	}
    	}
  	}

  	public function editcategory(){

	    $this->load->view('header');
	    $this->load->view('editcategory');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$id = !empty($jsondata['id']) ? $jsondata['id'] : "";

	      	$category_details = $this->db->query("SELECT * FROM categories where id='$id';")->result_array();
	      
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
  	}

  	public function saveEditcategory(){

	    $this->load->view('header');
	    $this->load->view('editcategory');
	    $this->load->view('footer');

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$id = !empty($jsondata['id']) ? $jsondata['id'] : "";
	    	$category = !empty($jsondata['category_name']) ? $jsondata['category_name'] : "";
	    	
			$postData = array(
				'category'=> $category
			);

			$this->db->where('id',$id);
    		$result = $this->db->update('categories',$postData);

    		// Start Admin History
			$historyMessage = "Admin edited ".$category;
			$resultHistory = $this->addHistory('Category', 'Add', $historyMessage);
			// End Admin History

	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Category edited successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Category not edited!")); die;
	      	}
    	}
  	}

  	public function deletecategory(){

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

    	if(!empty($jsondata)){

    		$category_id = !empty($jsondata['id']) ? $jsondata['id'] : "";

    		$category_details = $this->db->query("SELECT * FROM categories  where id='$category_id';")->result_array();
	        $category = !empty($category_details[0]['category']) ? $category_details[0]['category'] : 0;

    		$historyMessage = "Admin deleted ".$category." category";
    		$resultHistory = $this->addHistory('Performer', 'Delete', $historyMessage);


    		$performer_details = $this->db->query("SELECT * FROM performers  where category like '%$category_id%';")->result_array();

    		if(!empty($performer_details)){

    			foreach ($performer_details as $performer_list) {
    				
    				$categoryArray = !empty($performer_list['category']) ? explode(',', $performer_list['category']) : "";
		    		$performer_id = !empty($performer_list['id']) ? $performer_list['id'] : "";

		    		if(!empty($categoryArray)){
		    			$newArray = array();
		    			foreach ($categoryArray as $categoryList) {
		    				if($categoryList != $category_id){

		    					$newArray[] = $categoryList;
		    				}
		    			}

		    			$categoryPerformer = implode(',', $newArray);

		    			$performerData = array(
							'category'=> $categoryPerformer
						);

						$this->db->where('id',$performer_id);
			    		$resultPerformer = $this->db->update('performers',$performerData);
		    		}
    			}
    		}

    		$booking_details = $this->db->query("SELECT * FROM booking_request  where show_type like '%$category_id%';")->result_array();
    		if(!empty($booking_details)){

    			foreach ($booking_details as $booking_list) {

    				$show_typeArray = !empty($booking_list['show_type']) ? explode(',', $booking_list['show_type']) : "";
		    		$booking_id = !empty($booking_list['id']) ? $booking_list['id'] : "";

		    		if(!empty($show_typeArray)){
		    			$newArray1 = array();
		    			foreach ($show_typeArray as $show_typeList) {
		    				if($show_typeList != $category_id){

		    					$newArray1[] = $show_typeList;
		    				}
		    			}

		    			$categoryBooking = implode(',', $newArray1);

		    			$bookingData = array(
							'show_type'=> $categoryBooking
						);

						$this->db->where('id',$booking_id);
			    		$resultBooking = $this->db->update('booking_request',$bookingData);
		    		}
    			}
    		}

    		
  			$this -> db -> where('id', $category_id);
  			$result = $this -> db -> delete('categories');

	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Category deleted successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Category does not deleted!")); die;
	      	}
	    }
  	}



	public function emailTemplate(){

	    $this->load->view('header');
	    $this->load->view('emailTemplate');
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
		        $email_total = $this->db->query("SELECT count(*) as total FROM email_templates where (email_template_name like '%$searchdata%');")->result_array();
		        $total = !empty($email_total[0]['total']) ? $email_total[0]['total'] : 0;
	      	}
	      	else{
		        $email_total = $this->db->query("SELECT count(*) as total FROM email_templates;")->result_array();
		        $total = !empty($email_total[0]['total']) ? $email_total[0]['total'] : 0;
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
      
	      	$email_details = $this->db->query("SELECT * FROM email_templates where (email_template_name like '%$searchdata%') or (email_template_subject like '%$searchdata%') order by id desc $limitto;")->result_array();
	      
	      	if(!empty($email_details)){
		        foreach($email_details  as $email_list){
		          
		          	$temp['id'] = !empty($email_list['id']) ? $email_list['id'] : "";
		          	$temp['email_from'] = !empty($email_list['email_from']) ? $email_list['email_from'] : "";
		          	$temp['email_template_name'] = !empty($email_list['email_template_name']) ? $email_list['email_template_name'] : "";
		          	$temp['email_template_subject'] = !empty($email_list['email_template_subject']) ? $email_list['email_template_subject'] : "";
		          	$temp['email_template_html'] = !empty($email_list['email_template_html']) ? $email_list['email_template_html'] : "";
		          	
		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Email Template List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Email Template List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function editemailTemplate(){

	    $this->load->view('header');
	    $this->load->view('editemailTemplate');
	    $this->load->view('footer');

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$id = !empty($jsondata['id']) ? $jsondata['id'] : "";

	      	$email_details = $this->db->query("SELECT * FROM email_templates where id='$id';")->result_array();
	      
	      	if(!empty($email_details)){
	        	foreach($email_details  as $email_list){
	          
		          	$temp['id'] = !empty($email_list['id']) ? $email_list['id'] : "";
		          	$temp['email_from'] = !empty($email_list['email_from']) ? $email_list['email_from'] : "";
		          	$temp['email_template_name'] = !empty($email_list['email_template_name']) ? $email_list['email_template_name'] : "";
		          	$temp['email_template_subject'] = !empty($email_list['email_template_subject']) ? $email_list['email_template_subject'] : "";
		          	$temp['email_template_html'] = !empty($email_list['email_template_html']) ? $email_list['email_template_html'] : "";
		          	
		          	$response[] =$temp;
	        	}
	      	}
      
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Email Template List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Email Template List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function saveEditemailTemplate(){

	    $this->load->view('header');
	    $this->load->view('editemailTemplate');
	    $this->load->view('footer');

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$id = !empty($jsondata['id']) ? $jsondata['id'] : "";
	    	$email_from = !empty($jsondata['email_from']) ? $jsondata['email_from'] : "";
	    	$email_template_html = !empty($jsondata['email_template_html']) ? $jsondata['email_template_html'] : "";
	    	$email_template_name = !empty($jsondata['email_template_name']) ? $jsondata['email_template_name'] : "";
	    	$email_template_subject = !empty($jsondata['email_template_subject']) ? $jsondata['email_template_subject'] : "";
	    	
			$postData = array(
				'email_from'=> $email_from,
				'email_template_html'=> $email_template_html,
				'email_template_name'=> $email_template_name,
				'email_template_subject'=> $email_template_subject
			);

			$this->db->where('id',$id);
    		$result = $this->db->update('email_templates',$postData);

    		// Start Admin History
			$historyMessage = "Admin edited ".$email_template_html." email template";
			$resultHistory = $this->addHistory('Email Template', 'Edit', $historyMessage);
			// End Admin History

	      	if(!empty($result)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Template edited successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Template not edited!")); die;
	      	}
    	}
  	}



  	public function emailManager(){

  		$this->checkLogin();  

	    $this->load->view('header');
	    $this->load->view('emailManager');
	    $this->load->view('footer');

  	}


  	public function managerEmailTemplate(){

	    $this->checkLogin();  

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

	      	if( $jsondata['template'] == 'Chasing Paperwork'){
	        	$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='CHASING_PAPERWORK';")->result_array();
	  		} 
	  		else if( $jsondata['template'] == 'Looking Forward'){
	        	$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='LOOKING_FORWARD_YOU';")->result_array();
	  		}
	  		else if( $jsondata['template'] == 'Chasing Feedback'){
	        	$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='ADMIN_REQ_FEEDBACK_PERFORMER';")->result_array();
	  		}
	  		else if( $jsondata['template'] == 'Chasing Payment'){
	        	$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='CHASING_PAYMENTS';")->result_array();
	  		}
	  		else if( $jsondata['template'] == 'Chasing Performer'){
	        	$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='ACCEPT_PERFORMER';")->result_array();
	  		}
	  		else if( $jsondata['template'] == 'Chasing Thank You'){
	        	$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='CHASING_THANK_YOU';")->result_array();
	  		}
	      	else{
	        	$email_details = "";
	      	}

	      	if(!empty($email_details)){
		        foreach($email_details  as $email_list){
		          
		          	$temp['id'] = !empty($email_list['id']) ? $email_list['id'] : "";
		          	$temp['email_from'] = !empty($email_list['email_from']) ? $email_list['email_from'] : "";
		          	$temp['email_template_name'] = !empty($email_list['email_template_name']) ? $email_list['email_template_name'] : "";
		          	$temp['email_template_subject'] = !empty($email_list['email_template_subject']) ? $email_list['email_template_subject'] : "";
		          	$temp['email_template_html'] = !empty($email_list['email_template_html']) ? $email_list['email_template_html'] : "";
		          	
		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Email Template List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Email Template List!", 'response' =>$response)); die;
	      	}
	    }
  	}


  	public function chasingPaperwork(){

	    $this->checkLogin();  

	    $this->load->view('header');
	    $this->load->view('chasingPaperwork');
	    $this->load->view('footer');

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    
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
    		$currentDate = date("Y-m-d"); // and event_date>='$currentDate'
    		if(!empty($upcomingFilter)){

			$booking_total= $this->db->query("SELECT count(*) as total FROM booking_request where paperwork_count='0' and booking_status <> 'Cancelled' and event_date>='$currentDate' $upcomingFilter order by event_date ASC;")->result_array();
	        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	}else {


	      	$booking_total= $this->db->query("SELECT count(*) as total FROM booking_request where paperwork_count='0' and booking_status <> '	Cancelled'  and event_date>='$currentDate' order by event_date ASC;")->result_array();
	        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
      }

	      //	$booking_total= $this->db->query("SELECT count(*) as total FROM booking_request  where paperwork_count='0';")->result_array();
	      //  $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
      
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
      $currentDate = date("Y-m-d"); // and event_date>='$currentDate'
      		 if(!empty($upcomingFilter)){

	      	$booking_details = $this->db->query("SELECT * FROM booking_request where paperwork_count='0' and booking_status <> 'Cancelled'   and event_date>='$currentDate' $upcomingFilter order by event_date desc $limitto;")->result_array();
	      	//echo '<script> alert("under condition")</script>';// die();
	      }else{
	      	$booking_details = $this->db->query("SELECT * FROM booking_request where paperwork_count='0' and booking_status <> 'Cancelled'  and event_date>='$currentDate' order by event_date desc $limitto;")->result_array();
	      //	echo '<script> alert("not  condition")</script>';//
	      } 


	      	//$booking_details = $this->db->query("SELECT * FROM booking_request where paperwork_count='0' order by id desc $limitto;")->result_array();
	      
	      	if(!empty($booking_details)){
		        foreach($booking_details  as $booking_list){
		          
		          	$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
		          	$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
		          	$temp['email'] = !empty($booking_list['email']) ? $booking_list['email'] : "";
		          	$temp['mobile_number'] = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		          	
		          	$temp['event_date'] = !empty($booking_list['event_date']) ? date('d-m-Y', strtotime($booking_list['event_date'])) : "";
		          	$temp['show_time'] = !empty($booking_list['show_time']) ? date('h:i A', strtotime($booking_list['show_time'])) : "";
		          	$temp['show_end_time'] = !empty($booking_list['show_end_time']) ? date('h:i A', strtotime($booking_list['show_end_time'])) : "";

		          	$temp['event_amount'] = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : "";

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
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Detail List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Detail List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  public function sendPaperworkEmail(){

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    
	    if(!empty($jsondata)){

	    	$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";

	    	if(!empty($booking_id)){
	    		foreach ($booking_id as $id) {
	    			
	    			$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id'")->result_array();

			  		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
					$to_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";


			    	$subject = !empty($jsondata['email_template_subject']) ? $jsondata['email_template_subject'] : "";
			    	$message = !empty($jsondata['email_template_html']) ? $jsondata['email_template_html'] : "";
			    	$from_email = !empty($jsondata['email_from']) ? $jsondata['email_from'] : "";

			        $booking_id = !empty($id) ? base64_encode($id) : "";

			        $url = HTTP_CONTROLLER."generalAddBooking/?".$booking_id;

			        $data = array('LINK' => $url, 'SHOW_NAME' => $name);
			        if(!empty($data)){
			        	foreach($data as $key => $value){
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			 	 	$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);

			 	 	// Start Admin History
					$historyMessage = "Admin sent paperwork mail to ".$name." booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'paperwork mail', $historyMessage);
					// End Admin History

			 	 	// Start Admin Email
					$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
					$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

					if(!empty($admin_email)){
				        $result_mail_admin = $this->sendMail($from_email, $admin_email, $message, $subject);
					}
					// End Admin Email
	    		}

		      	if(!empty($result_mail)){
		        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Email Sent successfully!")); die;
		      	}
		      	else{
		        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Email Sending Failed")); die;
		      	}
	    	}
	    	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Id!")); die;
	      	}
    	}
  	}

  	public function ignorePaperworkEmail(){
  		$this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    
	    if(!empty($jsondata)){

	    	$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";

	    	if(!empty($booking_id)){
	    		foreach ($booking_id as $id) {
	    			
	    			$booking_details = $this->db->query("UPDATE booking_request set paperwork_count='1' where id='$id'")->result_array();

					}
			}
	}
if(!empty($booking_details)){
		        	echo json_encode(array('status'=>1, 'type'=>'success', "message" => "Email Sent successfully!")); die;
		      	}
		      	else{
		        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Email Sending Failed")); die;
		      	}
  		//echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Email Sent successfully!")); die;
  	}

  	public function lookingForward(){

	    $this->checkLogin();  

	    $this->load->view('header');
	    $this->load->view('lookingForward');
	    $this->load->view('footer');

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
    		}else if($filter == '10'){
    			$currentDate = date("Y-m-d");
  				$ten = date('Y-m-d', strtotime($currentDate . ' +10 day'));
				$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$ten') ";
    		}
    		else{
    			$upcomingFilter="";
    		}


$currentDate = date("Y-m-d");//event_date>='$currentDate'
if(!empty($upcomingFilter)){

			$booking_total= $this->db->query("SELECT count(*) as total FROM booking_request where looking_count='0' and booking_status <> 'Cancelled'  $upcomingFilter;")->result_array();
	        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	}else {


	      	$booking_total= $this->db->query("SELECT count(*) as total FROM booking_request where event_date>='$currentDate' and booking_status <> 'Cancelled'  and looking_count='0' ;")->result_array();
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
      if(!empty($upcomingFilter)){

	      	$booking_details = $this->db->query("SELECT * FROM booking_request where looking_count='0' and booking_status <> 'Cancelled' $upcomingFilter order by event_date ASC $limitto;")->result_array();
	      	//echo '<script> alert("under condition")</script>';// die();
	      }else{
	      	$booking_details = $this->db->query("SELECT * FROM booking_request where event_date>='$currentDate' and booking_status <> 'Cancelled'  and looking_count='0' order by event_date ASC $limitto;")->result_array();
	      //	echo '<script> alert("not  condition")</script>';//
	      } 


	      	if(!empty($booking_details)){
		        foreach($booking_details  as $booking_list){
		          
		          	$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
		          	$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
		          	$temp['email'] = !empty($booking_list['email']) ? $booking_list['email'] : "";
		          	$temp['mobile_number'] = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		          	
		          	$temp['event_date'] = !empty($booking_list['event_date']) ? date('d-m-Y', strtotime($booking_list['event_date'])) : "";
		          	$temp['show_time'] = !empty($booking_list['show_time']) ? date('h:i A', strtotime($booking_list['show_time'])) : "";
		          	$temp['show_end_time'] = !empty($booking_list['show_end_time']) ? date('h:i A', strtotime($booking_list['show_end_time'])) : "";

		          	$temp['event_amount'] = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : "";

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
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Detail List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Detail List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function sendLookingEmail(){

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    
	    if(!empty($jsondata)){

	    	$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";

	    	if(!empty($booking_id)){
	    		foreach ($booking_id as $id) {
	    			
	    			$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id'")->result_array();

	    			$name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
			    	$email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
			    	$event_date = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
			    	
			    	$date = $event_date;
					$dayOfWeek = date("l", strtotime($date));
					//echo $date . ' fell on a ' . $dayOfWeek;


					$show_time = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
					$currentDateTime = $show_time;
					$show_time = date('h:i A', strtotime($show_time));

			    	$duration = !empty($booking_details[0]['duration']) ? $booking_details[0]['duration'] : "";
			    	$host = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
			    	$fee = !empty($booking_details[0]['event_amount']) ? $booking_details[0]['event_amount'] : "";
			    	$paidfee = !empty($booking_details[0]['paid_amount']) ? $booking_details[0]['paid_amount'] : "";
			    	$remainfee = !empty($booking_details[0]['remain_amount']) ? $booking_details[0]['remain_amount'] : "";

			    	$party_bags = !empty($booking_details[0]['party_bags']) ? $booking_details[0]['party_bags'] : "";
			    	$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
			    	$home_address = !empty($booking_details[0]['home_address']) ? $booking_details[0]['home_address'] : "";
			    	$parking_facility = !empty($booking_details[0]['parking_facility']) ? $booking_details[0]['parking_facility'] : "";

			    	$party_in_out = !empty($booking_details[0]['party_in_out']) ? $booking_details[0]['party_in_out'] : "";
			    	$rain_plan = !empty($booking_details[0]['rain_plan']) ? $booking_details[0]['rain_plan'] : "";
			    	$age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";

			    	$dob = !empty($booking_details[0]['dob']) ? $booking_details[0]['dob'] : "";
			    	$children_count = !empty($booking_details[0]['children_count']) ? $booking_details[0]['children_count'] : "";
			    	$children_party = !empty($booking_details[0]['children_party']) ? $booking_details[0]['children_party'] : "";

			    	$gender_party = !empty($booking_details[0]['gender_party']) ? $booking_details[0]['gender_party'] : "";
			    	$party_seen = !empty($booking_details[0]['party_seen']) ? $booking_details[0]['party_seen'] : "";
			    	$mobile_number = !empty($booking_details[0]['mobile_number']) ? $booking_details[0]['mobile_number'] : "";
			    	$second_mobile = !empty($booking_details[0]['second_mobile']) ? $booking_details[0]['second_mobile'] : "";

					$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";

					$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

					$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
					$showArray = array();
					if(!empty($categoryDetails)){
						foreach ($categoryDetails as $categoryList) {

							$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
							$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}
					}

					$show_type = !empty($showArray) ? implode(',', $showArray) : "";


					$performer_message = "";
					$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' ")->result_array();

					if(!empty($assign_details)){
						foreach ($assign_details as $assignList) {
							$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

							$performer_name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
							$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
							$performer_mobile = !empty($performer_details[0]['mobile_a']) ? $performer_details[0]['mobile_a'] : "";

							$performer_message.='Performer name: '.$performer_name.'   ';
							$performer_message.='Performer email: '.$performer_email.'   ';
							$performer_message.='Performer mobile: '.$performer_mobile.'   ';
						}
					}


					$subject = !empty($jsondata['email_template_subject']) ? $jsondata['email_template_subject'] : "";
			    	$message = !empty($jsondata['email_template_html']) ? $jsondata['email_template_html'] : "";
			    	$from_email = !empty($jsondata['email_from']) ? $jsondata['email_from'] : "";

			    	$data = array('HOST' =>$host, 'SHOW_DAY' => $dayOfWeek, 'SHOW_DATE' => $event_date, 'EMAIL' => $email, 'SHOW_TIME' => $show_time, 'DURATION' => $duration, 'SHOW_TYPE' => $show_type,'SHOW_DETAILS'=>$show_type_text, 'PARTY_BAGS' => $party_bags, 'PARTY_ADDRESS' => $party_address, 'HOME_ADDRESS' => $home_address, 'PARKING_FACILITY' => $parking_facility, 'PARTY_IN_OUT' => $party_in_out, 'RAIN_PLAN' => $rain_plan, 'CHILD_NAME' => $name, 'AGE' => $age, 'DOB' => $dob, 'CHILDREN_COUNT' => $children_count, 'CHILDREN_PARTY' => $children_party, 'GENDER_PARTY' => $gender_party, 'PARTY_SEEN' => $party_seen, 'MOBILE_NUMBER' => $mobile_number,'FEE'=>$fee,'PAID'=>$paidfee,'REMAIN'=>$remainfee , 'SECOND_MOBILE' => $second_mobile, 'PERFORMER_DETAILS' => $performer_message);

			        if(!empty($data)){
			        	foreach($data as $key => $value){
							$subject = str_replace('{{'.$key.'}}', $value, $subject);
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			       	// Start Customer Email
			       	$customer_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

			 	 	$result_mail = $this->sendMail($from_email, $customer_email, $message, $subject);
			 	 	$update_details = $this->db->query("UPDATE  booking_request set looking_count='1' where id='$id' ");
			 	 	// End Customer Email

			 	 	// Start Admin History
					$historyMessage = "Admin sent mail to ".$name." booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'Looking Forward mail', $historyMessage);
					// End Admin History

					// Start Performer Email
			       	$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' and status='Accept' ")->result_array();
					if(!empty($assign_details)){
						foreach ($assign_details as $assignList) {
							$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

							$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
							
							$result_mail_performer = $this->sendMail($from_email, $performer_email, $message, $subject);
						}
					}
			 	 	// End Performer Email

			 	 	// Start Admin Email
					$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
					$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

					if(!empty($admin_email)){
				        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
					}
					// End Admin Email

	    		}

		      	if(!empty($result_mail)){
		      			
		        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Email Send successfully!")); die;
		      	}
		      	else{
		        	echo json_encode(array('status'=>1, 'type'=>'success', "message" => "Email Sending Failed!")); die;
		      	}
	    	}
	    	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Id!")); die;
	      	}
    	}
  	}

	public function ignoreLookingForward(){
  		$this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    
	    if(!empty($jsondata)){

	    	$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";

	    	if(!empty($booking_id)){
	    		foreach ($booking_id as $id) {
	    			
	    			$booking_details = $this->db->query("UPDATE booking_request set looking_count='1' where id='$id'")->result_array();

					}
			}
	}
if(!empty($booking_details)){
		        	echo json_encode(array('status'=>1, 'type'=>'success', "message" => "Data Ignored successfully!")); die;
		      	}
		      	else{
		        	echo json_encode(array('status'=>0, 'type'=>'error', "message" => "Email Sending Failed")); die;
		      	}
  		//echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Email Sent successfully!")); die;
  	}


  	public function chasingFeedback(){

	    $this->checkLogin();  

	    $this->load->view('header');
	    $this->load->view('chasingFeedback');
	    $this->load->view('footer');
	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    
    	if(!empty($jsondata)){

			$currentDate = date("Y-m-d");
	      	//assign_date < '$currentDate'
	      	$booking_total= $this->db->query("SELECT count(*) as total FROM assign_booking  where booking_status='Feedback Pending' and assign_date < '$currentDate';")->result_array();
	        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
      
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
      
	      	$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_status='Feedback Pending' and assign_date < '$currentDate' order by assign_date ASC $limitto;")->result_array();
	      
	      	if(!empty($assign_details)){
		        foreach($assign_details  as $assign_list){
		          
		          	$temp['id'] = !empty($assign_list['id']) ? $assign_list['id'] : "";
		          	$temp['booking_request_id'] = !empty($assign_list['booking_request_id']) ? $assign_list['booking_request_id'] : "";

		          	$booking_id = !empty($assign_list['booking_request_id']) ? $assign_list['booking_request_id'] : "";
		          	//$temp['performer_id'] = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";

		          	$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_id' and booking_status='Feedback Pending';")->result_array();
					$temp['name'] = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";

		          	$temp['performer_id'] = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";

		          	$performer_id = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";
		          	$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id';")->result_array();
					$temp['performer_name'] = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";


		          	$temp['assign_date'] = !empty($assign_list['assign_date']) ? date('d-m-Y', strtotime($assign_list['assign_date'])) : "";
		          	$temp['assign_start_datetime'] = !empty($assign_list['assign_start_datetime']) ? $assign_list['assign_start_datetime'] : "";
		          	$temp['assign_end_datetime'] = !empty($assign_list['assign_end_datetime']) ? $assign_list['assign_end_datetime'] : "";
		          	
		          	//$temp['id'] = $temp['booking_request_id'];
		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Chasing Performer Detail List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Chasing Performer Detail List!", 'response' =>$response)); die;
	      	}
	    }



	    // Rakesh CHaturvedi 

	
  	}

  	public function sendFeedbackEmail(){

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    
	    if(!empty($jsondata)){

	    	$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";
			
	    	if(!empty($booking_id)){
	    		foreach ($booking_id as $id) {
	    			$assign_details = $this->db->query("SELECT * FROM assign_booking where id='$id'")->result_array();

			  		$booking_request_id = !empty($assign_details[0]['booking_request_id']) ? $assign_details[0]['booking_request_id'] : "";
	    			$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id'")->result_array();

			  		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
			  		$child_name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
					
					$to_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

					$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
					$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
					$show_time = date('h:i A', strtotime($show_time));

			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
					$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
					$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

					$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
					$showArray = array();
					if(!empty($categoryDetails)){
						foreach ($categoryDetails as $categoryList) {

							$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
							$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}
					}

					$showType = !empty($showArray) ? implode(', ', $showArray) : "";

					// Start Admin History
					$historyMessage = "Admin sent mail to ".$name." booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'Feedback', $historyMessage);
					// End Admin History


			    	$subject = !empty($jsondata['email_template_subject']) ? $jsondata['email_template_subject'] : "";
			    	$message = !empty($jsondata['email_template_html']) ? $jsondata['email_template_html'] : "";
			    	$from_email = !empty($jsondata['email_from']) ? $jsondata['email_from'] : "";

			        $booking_id = !empty($booking_request_id) ? base64_encode($booking_request_id) : "";

			        $assign_details = $this->db->query("SELECT * FROM assign_booking where id='$id';")->result_array();

			        
			        $count = 0;
			        if(!empty($assign_details)){
			        	foreach ($assign_details as $assignList) {

			        		$performer_base_id = !empty($assignList['performer_id']) ? base64_encode($assignList['performer_id']) : "";

			        		$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

			        		$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id';")->result_array();
							$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";

			        		$url = HTTP_CONTROLLER."generalAddFeedback/?".$booking_id.'/?'.$performer_base_id;

			        		$data = array('LINK' => $url, 'SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $show_time,'HOST' => $name, 'CHILD_NAME' => $child_name,'SHOW_TYPE' => $showType,'SHOW_DETAILS' => $showType_text);
					        if(!empty($data)){
					        	foreach($data as $key => $value){
					        		$subject = str_replace('{{'.$key.'}}', $value, $subject);
									$message = str_replace('{{'.$key.'}}', $value, $message);
								}
					       	}

					 	 	$result_mail = $this->sendMail($from_email, $performer_email, $message, $subject);
					 	 	$count = $count + 1;
			        	}
			        }
	    		}

		      	if(!empty($count)){
		        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Email Sent successfully!")); die;
		      	}
		      	else{ //"Email Sending Failed!"$performer_id.$booking_id
		        	echo json_encode(array('status'=>1, 'type'=>'error', "message" =>"Email Sending Failed!" )); die;
		      	}
	    	}
	    	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Id!")); die;
	      	}
    	}
  	}
  	public function ignoreFeedbackEmail(){
  		

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    
	    if(!empty($jsondata)){

	    	$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";
			
	    	if(!empty($booking_id)){
	    		foreach ($booking_id as $id) {
	    			$assign_details = $this->db->query("SELECT * FROM assign_booking where id='$id'")->result_array();

			  		$booking_request_id = !empty($assign_details[0]['booking_request_id']) ? $assign_details[0]['booking_request_id'] : "";
	    			$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_request_id'")->result_array();

			  		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
					$to_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

					$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
					$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
					
			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
					$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

					$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
					$showArray = array();
					if(!empty($categoryDetails)){
						foreach ($categoryDetails as $categoryList) {

							$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
							$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}
					}

					$showType = !empty($showArray) ? implode(',', $showArray) : "";

					// Start Admin History
					$historyMessage = "Admin sent mail to ".$name." booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'Feedback', $historyMessage);
					// End Admin History


			    	$subject = !empty($jsondata['email_template_subject']) ? $jsondata['email_template_subject'] : "";
			    	$message = !empty($jsondata['email_template_html']) ? $jsondata['email_template_html'] : "";
			    	$from_email = !empty($jsondata['email_from']) ? $jsondata['email_from'] : "";

			        $booking_id = !empty($booking_request_id) ? base64_encode($booking_request_id) : "";

			        $assign_details = $this->db->query("SELECT * FROM assign_booking where id='$id';")->result_array();

			        
			        $count = 0;
	 	 		

			        	foreach ($assign_details as $assignList) {

			        		$performer_base_id = !empty($assignList['performer_id']) ? base64_encode($assignList['performer_id']) : "";

			        		$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

			        		$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id';")->result_array();
							$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";

			        		$url = HTTP_CONTROLLER."generalAddFeedback/?".$booking_id.'/?'.$performer_base_id;

			        		$data = array('LINK' => $url, 'SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType);
					        if(!empty($data)){
					        	foreach($data as $key => $value){
					        		$subject = str_replace('{{'.$key.'}}', $value, $subject);
									$message = str_replace('{{'.$key.'}}', $value, $message);
								}
					       	}

					 	  if(!empty($assign_details)){
						$Update = $this->db->query("UPDATE `assign_booking` SET `booking_status`='Completed'  WHERE `id`='$id'");  
			 	//  			//$Update = $this->db->query("UPDATE `assign_booking` SET `booking_status`='Completed'  WHERE `id`='$id'");  
			 	 		if($Update){
			 	 			$awaiting_details = $this->db->query("SELECT count(*) as awaiting FROM assign_booking where booking_request_id='$booking_request_id' and booking_status='Feedback Pending';")->result_array();
	    			$awaiting = !empty($awaiting_details[0]['awaiting']) ? $awaiting_details[0]['awaiting'] : "";

			    	if($awaiting == 0){
						$updatebookingrequest = $this->db->query("UPDATE `booking_request` SET `booking_status`='Completed' WHERE `id`='$booking_request_id'");   
					} 
}
		
					 	 	$count = $count + 1; 
			        	}
			        }
	    		}

		      	if(!empty($count)){
		        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Performer ignored successfully!")); die;
		      	}
		      	else{ //"Email Sending Failed!"$performer_id.$booking_id
		        	echo json_encode(array('status'=>1, 'type'=>'error', "message" =>"Email Sending Failed!" )); die;
		      	}
	    	}
	    	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Id!")); die;
	      	}
    	}
  	
  	}


  	public function chasingPayment(){

	    $this->checkLogin();  

	    $this->load->view('header');
	    $this->load->view('chasingPayment');
	    $this->load->view('footer');

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $currentDate = date("Y-m-d"); 
	      	// assign_date < '$currentDate'
    	if(!empty($jsondata)){

	      	$booking_total= $this->db->query("SELECT count(*) as total FROM booking_request  where remain_amount >'0' and event_date >= '$currentDate';")->result_array();
	        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
      
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
      
	      	$booking_details = $this->db->query("SELECT * FROM booking_request where remain_amount >'0' and event_date >= '$currentDate'  order by event_date ASC $limitto;")->result_array();
	      
	      	if(!empty($booking_details)){
		        foreach($booking_details  as $booking_list){
		          
		          	$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
		          	$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
		          	$temp['email'] = !empty($booking_list['email']) ? $booking_list['email'] : "";
		          	$temp['mobile_number'] = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		          	
		          	$temp['event_date'] = !empty($booking_list['event_date']) ? date('d-m-Y', strtotime($booking_list['event_date'])) : "";
		          	$temp['show_time'] = !empty($booking_list['show_time']) ? date('h:i A', strtotime($booking_list['show_time'])) : "";
		          	$temp['show_end_time'] = !empty($booking_list['show_end_time']) ? date('h:i A', strtotime($booking_list['show_end_time'])) : "";

		          	$temp['event_amount'] = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : "";
		          	$temp['paid_amount'] = !empty($booking_list['paid_amount']) ? $booking_list['paid_amount'] : "";
		          	$temp['remain_amount'] = !empty($booking_list['remain_amount']) ? $booking_list['remain_amount'] : "";

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
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Detail List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Detail List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function sendPaymentEmail(){

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    
	    if(!empty($jsondata)){

	    	$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";

	    	if(!empty($booking_id)){
	    		foreach ($booking_id as $id) {
	    			
	    			$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id'")->result_array();

			  		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
					$to_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";


			    	$subject = !empty($jsondata['email_template_subject']) ? $jsondata['email_template_subject'] : "";
			    	$message = !empty($jsondata['email_template_html']) ? $jsondata['email_template_html'] : "";
			    	$from_email = !empty($jsondata['email_from']) ? $jsondata['email_from'] : "";

			        $data = array('SHOW_NAME' => $name);
			        if(!empty($data)){
			        	foreach($data as $key => $value){
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			 	 	$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);

			 	 	// Start Admin History
					$historyMessage = "Admin sent payment mail to ".$name." booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'payment mail', $historyMessage);
					// End Admin History

			 	 	// Start Admin Email
					$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
					$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

					if(!empty($admin_email)){
				        $result_mail_admin = $this->sendMail($from_email, $admin_email, $message, $subject);
					}
					// End Admin Email
	    		}

		      	if(!empty($result_mail)){
		        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Email Sent successfully!")); die;
		      	}
		      	else{
		        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Email Sending Failed!")); die;
		      	}
	    	}
	    	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Id!")); die;
	      	}
    	}
  	}


//Chasing Thank you 

public function chasingthankyou(){
$this->checkLogin();  

	    $this->load->view('header');
	    $this->load->view('chasingThankyou');
	    $this->load->view('footer');

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    
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

    		if(!empty($upcomingFilter)){

			$booking_total= $this->db->query("SELECT count(*) as total FROM booking_request where booking_status='Completed' and thankyou_count='0' $upcomingFilter;")->result_array();
	        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
	}else {
	      	$booking_total= $this->db->query("SELECT count(*) as total FROM booking_request where booking_status='Completed' and thankyou_count='0';")->result_array();
	        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
      }

	      //	$booking_total= $this->db->query("SELECT count(*) as total FROM booking_request  where paperwork_count='0';")->result_array();
	      //  $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
      
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
      
      		 if(!empty($upcomingFilter)){

	      	$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status='Completed' and thankyou_count='0' $upcomingFilter order by event_date ASC $limitto;")->result_array();
	      	//echo '<script> alert("under condition")</script>';// die();
	      }else{
	      	$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status='Completed' and thankyou_count='0' order by event_date ASC $limitto;")->result_array();
	      //	echo '<script> alert("not  condition")</script>';//
	      } 


	      	//$booking_details = $this->db->query("SELECT * FROM booking_request where paperwork_count='0' order by id desc $limitto;")->result_array();
	      
	      	if(!empty($booking_details)){
		        foreach($booking_details  as $booking_list){
		          
		          	$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
		          	$temp['name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
		          	$temp['email'] = !empty($booking_list['email']) ? $booking_list['email'] : "";
		          	$temp['mobile_number'] = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		          	
		          	$temp['event_date'] = !empty($booking_list['event_date']) ? date('d-m-Y', strtotime($booking_list['event_date'])) : "";
		          	$temp['show_time'] = !empty($booking_list['show_time']) ? date('h:i A', strtotime($booking_list['show_time'])) : "";
		          	$temp['show_end_time'] = !empty($booking_list['show_end_time']) ? date('h:i A', strtotime($booking_list['show_end_time'])) : "";

		          	$temp['event_amount'] = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : "";

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
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking Detail List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Detail List!", 'response' =>$response)); die;
	      	}
	    }

}

 	public function sendThankyouEmail(){

	   

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    
	    if(!empty($jsondata)){

	    	$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";

	    	if(!empty($booking_id)){
	    		foreach ($booking_id as $id) {
	    			
	    			$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id'")->result_array();

	    			$name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
			    	$email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
			    	$event_date = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";

					$show_time = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
			    	$duration = !empty($booking_details[0]['duration']) ? $booking_details[0]['duration'] : "";
			    	$host = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
			    	$fee = !empty($booking_details[0]['event_amount']) ? $booking_details[0]['event_amount'] : "";
			    	$paidfee = !empty($booking_details[0]['paid_amount']) ? $booking_details[0]['paid_amount'] : "";
			    	$remainfee = !empty($booking_details[0]['remain_amount']) ? $booking_details[0]['remain_amount'] : "";

			    	$party_bags = !empty($booking_details[0]['party_bags']) ? $booking_details[0]['party_bags'] : "";
			    	$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
			    	$home_address = !empty($booking_details[0]['home_address']) ? $booking_details[0]['home_address'] : "";
			    	$parking_facility = !empty($booking_details[0]['parking_facility']) ? $booking_details[0]['parking_facility'] : "";

			    	$party_in_out = !empty($booking_details[0]['party_in_out']) ? $booking_details[0]['party_in_out'] : "";
			    	$rain_plan = !empty($booking_details[0]['rain_plan']) ? $booking_details[0]['rain_plan'] : "";
			    	$age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";

			    	$dob = !empty($booking_details[0]['dob']) ? $booking_details[0]['dob'] : "";
			    	$children_count = !empty($booking_details[0]['children_count']) ? $booking_details[0]['children_count'] : "";
			    	$children_party = !empty($booking_details[0]['children_party']) ? $booking_details[0]['children_party'] : "";

			    	$gender_party = !empty($booking_details[0]['gender_party']) ? $booking_details[0]['gender_party'] : "";
			    	$party_seen = !empty($booking_details[0]['party_seen']) ? $booking_details[0]['party_seen'] : "";
			    	$mobile_number = !empty($booking_details[0]['mobile_number']) ? $booking_details[0]['mobile_number'] : "";
			    	$second_mobile = !empty($booking_details[0]['second_mobile']) ? $booking_details[0]['second_mobile'] : "";

					$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";

					$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

					$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
					$showArray = array();
					if(!empty($categoryDetails)){
						foreach ($categoryDetails as $categoryList) {

							$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
							$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}
					}

					$show_type = !empty($showArray) ? implode(',', $showArray) : "";


					$performer_message = "";
					$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' ")->result_array();

					if(!empty($assign_details)){
						foreach ($assign_details as $assignList) {
							$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

							$performer_name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
							$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
							$performer_mobile = !empty($performer_details[0]['mobile_a']) ? $performer_details[0]['mobile_a'] : "";

							 $performer_message.=$performer_name.',';
							// $performer_message.='Performer email: '.$performer_email.'   ';
							// $performer_message.='Performer mobile: '.$performer_mobile.'   ';
						}
					}
					$feedback = "";
					$newline = '
					';
					$feedback_details = $this->db->query("SELECT * FROM feedback where booking_id='$id' ")->result_array();
					//and ( rating='Best party ever- the show buzzed!!' or rating='Great party!' )
					if(!empty($feedback_details)){
						foreach ($feedback_details as $feedbackList) {
							$performer_id = !empty($feedbackList['performer_id']) ? $feedbackList['performer_id'] : "";

							//$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

							$goodpoint = !empty($feedbackList['good_points']) ? $feedbackList['good_points'] : "";
							$badpoint = !empty($feedbackList['bad_points']) ? $feedbackList['bad_points'] : "";
							$rating = !empty($feedbackList['rating']) ? $feedbackList['rating'] : "";
							//$feedback.='Performer Name         : '.$performer_name.$newline;
							 $feedback.=''.$goodpoint.$newline;
							// $feedback.='Bad point about party : '.$badpoint.$newline;
							// $feedback.='Rating your Party     : '.$rating.$newline;
							// $performer_message.='Performer mobile: '.$performer_mobile.'   ';
						}
					}


					$subject = !empty($jsondata['email_template_subject']) ? $jsondata['email_template_subject'] : "";
			    	$message = !empty($jsondata['email_template_html']) ? $jsondata['email_template_html'] : "";
			    	$from_email = !empty($jsondata['email_from']) ? $jsondata['email_from'] : "";

			    	$data = array('HOST' =>$host, 'SHOW_DATE' => $event_date, 'EMAIL' => $email, 'SHOW_TIME' => $show_time, 'DURATION' => $duration, 'SHOW_TYPE' => $show_type,'SHOW_DETAILS'=>$show_type_text, 'PARTY_BAGS' => $party_bags, 'PARTY_ADDRESS' => $party_address, 'HOME_ADDRESS' => $home_address, 'PARKING_FACILITY' => $parking_facility, 'PARTY_IN_OUT' => $party_in_out, 'RAIN_PLAN' => $rain_plan, 'CHILD_NAME' => $name, 'AGE' => $age, 'DOB' => $dob, 'CHILDREN_COUNT' => $children_count, 'CHILDREN_PARTY' => $children_party, 'GENDER_PARTY' => $gender_party, 'PARTY_SEEN' => $party_seen, 'MOBILE_NUMBER' => $mobile_number,'FEE'=>$fee,'PAID'=>$paidfee,'REMAIN'=>$remainfee , 'SECOND_MOBILE' => $second_mobile, 'PERFORMER_DETAILS' => $performer_message,'FEEDBACKS'=>$feedback);

			        if(!empty($data)){
			        	foreach($data as $key => $value){
							$subject = str_replace('{{'.$key.'}}', $value, $subject);
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			       	// Start Customer Email
			       	$customer_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

			 	 	$result_mail = $this->sendMail($from_email, $customer_email, $message, $subject);
			 	 	// End Customer Email

			 	 	// Start Admin History
					$historyMessage = "Admin sent mail to ".$name." booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'Looking Forward mail', $historyMessage);
					// End Admin History

					// Start Performer Email
			       	$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' and status='Accept' ")->result_array();
					if(!empty($assign_details)){
						foreach ($assign_details as $assignList) {
							$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

							$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
							
							$result_mail_performer = $this->sendMail($from_email, $performer_email, $message, $subject);
						}
					}
			 	 	// End Performer Email

			 	 	// Start Admin Email
					$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
					$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

					if(!empty($admin_email)){
				        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);

					}
					// End Admin Email

	    		}

		      	if(!empty($result_mail)){
		      		$updatebookingrequest = $this->db->query("UPDATE `booking_request` SET thankyou_count='1' WHERE `id`='$id'");   
		        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Email Send successfully!")); die;
		      	}
		      	else{
		      		$updatebookingrequest = $this->db->query("UPDATE `booking_request` SET thankyou_count='1' WHERE `id`='$id'");   
		        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Email Sending Failed!")); die;
		      	}
	    	}
	    	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Id!")); die;
	      	}
    	}
  	}


// Chasing thank you 

public function ignoreThankyouEmail(){
	   

	 
  	
$this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    
	    if(!empty($jsondata)){

	    	$booking_id = !empty($jsondata['booking_id']) ? $jsondata['booking_id'] : "";

	    	if(!empty($booking_id)){
	    		foreach ($booking_id as $id) {
	    			
	    			$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id'")->result_array();

	    			$name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
			    	$email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
			    	$event_date = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";

					$show_time = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
			    	$duration = !empty($booking_details[0]['duration']) ? $booking_details[0]['duration'] : "";
			    	$host = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
			    	$fee = !empty($booking_details[0]['event_amount']) ? $booking_details[0]['event_amount'] : "";
			    	$paidfee = !empty($booking_details[0]['paid_amount']) ? $booking_details[0]['paid_amount'] : "";
			    	$remainfee = !empty($booking_details[0]['remain_amount']) ? $booking_details[0]['remain_amount'] : "";

			    	$party_bags = !empty($booking_details[0]['party_bags']) ? $booking_details[0]['party_bags'] : "";
			    	$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
			    	$home_address = !empty($booking_details[0]['home_address']) ? $booking_details[0]['home_address'] : "";
			    	$parking_facility = !empty($booking_details[0]['parking_facility']) ? $booking_details[0]['parking_facility'] : "";

			    	$party_in_out = !empty($booking_details[0]['party_in_out']) ? $booking_details[0]['party_in_out'] : "";
			    	$rain_plan = !empty($booking_details[0]['rain_plan']) ? $booking_details[0]['rain_plan'] : "";
			    	$age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";

			    	$dob = !empty($booking_details[0]['dob']) ? $booking_details[0]['dob'] : "";
			    	$children_count = !empty($booking_details[0]['children_count']) ? $booking_details[0]['children_count'] : "";
			    	$children_party = !empty($booking_details[0]['children_party']) ? $booking_details[0]['children_party'] : "";

			    	$gender_party = !empty($booking_details[0]['gender_party']) ? $booking_details[0]['gender_party'] : "";
			    	$party_seen = !empty($booking_details[0]['party_seen']) ? $booking_details[0]['party_seen'] : "";
			    	$mobile_number = !empty($booking_details[0]['mobile_number']) ? $booking_details[0]['mobile_number'] : "";
			    	$second_mobile = !empty($booking_details[0]['second_mobile']) ? $booking_details[0]['second_mobile'] : "";

					$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";

					$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

					$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
					$showArray = array();
					if(!empty($categoryDetails)){
						foreach ($categoryDetails as $categoryList) {

							$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
							$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}
					}

					$show_type = !empty($showArray) ? implode(',', $showArray) : "";


					$performer_message = "";
					$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' ")->result_array();

					if(!empty($assign_details)){
						foreach ($assign_details as $assignList) {
							$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

							$performer_name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
							$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
							$performer_mobile = !empty($performer_details[0]['mobile_a']) ? $performer_details[0]['mobile_a'] : "";

							 $performer_message.=$performer_name.',';
							// $performer_message.='Performer email: '.$performer_email.'   ';
							// $performer_message.='Performer mobile: '.$performer_mobile.'   ';
						}
					}
					$feedback = "";
					$newline = '
					';
					$feedback_details = $this->db->query("SELECT * FROM feedback where booking_id='$id' ")->result_array();

					if(!empty($feedback_details)){
						foreach ($feedback_details as $feedbackList) {
							$performer_id = !empty($feedbackList['performer_id']) ? $feedbackList['performer_id'] : "";

							//$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

							$goodpoint = !empty($feedbackList['good_points']) ? $feedbackList['good_points'] : "";
							$badpoint = !empty($feedbackList['bad_points']) ? $feedbackList['bad_points'] : "";
							$rating = !empty($feedbackList['rating']) ? $feedbackList['rating'] : "";
							//$feedback.='Performer Name         : '.$performer_name.$newline;
							 $feedback.='Good point about party: '.$goodpoint.$newline;
							 $feedback.='Bad point about party : '.$badpoint.$newline;
							 $feedback.='Rating your Party     : '.$rating.$newline;
							// $performer_message.='Performer mobile: '.$performer_mobile.'   ';
						}
					}


					$subject = !empty($jsondata['email_template_subject']) ? $jsondata['email_template_subject'] : "";
			    	$message = !empty($jsondata['email_template_html']) ? $jsondata['email_template_html'] : "";
			    	$from_email = !empty($jsondata['email_from']) ? $jsondata['email_from'] : "";

			    	$data = array('HOST' =>$host, 'SHOW_DATE' => $event_date, 'EMAIL' => $email, 'SHOW_TIME' => $show_time, 'DURATION' => $duration, 'SHOW_TYPE' => $show_type,'SHOW_DETAILS'=>$show_type_text, 'PARTY_BAGS' => $party_bags, 'PARTY_ADDRESS' => $party_address, 'HOME_ADDRESS' => $home_address, 'PARKING_FACILITY' => $parking_facility, 'PARTY_IN_OUT' => $party_in_out, 'RAIN_PLAN' => $rain_plan, 'CHILD_NAME' => $name, 'AGE' => $age, 'DOB' => $dob, 'CHILDREN_COUNT' => $children_count, 'CHILDREN_PARTY' => $children_party, 'GENDER_PARTY' => $gender_party, 'PARTY_SEEN' => $party_seen, 'MOBILE_NUMBER' => $mobile_number,'FEE'=>$fee,'PAID'=>$paidfee,'REMAIN'=>$remainfee , 'SECOND_MOBILE' => $second_mobile, 'PERFORMER_DETAILS' => $performer_message,'FEEDBACKS'=>$feedback);

			        if(!empty($data)){
			        	foreach($data as $key => $value){
							$subject = str_replace('{{'.$key.'}}', $value, $subject);
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			       	// Start Customer Email
			       	$customer_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
			     //  $Update = $this->db->query("UPDATE `booking_request` SET `booking_status`='Ignored'  WHERE `id`='$id'"); 
			       $Update = $this->db->query("UPDATE `booking_request` SET `thankyou_count`='1'  WHERE `id`='$id'");  
			 	 	//$result_mail = $this->sendMail($from_email, $customer_email, $message, $subject);
			 	 	// End Customer Email

			 	 	// Start Admin History
					$historyMessage = "Admin Ignored ".$name." booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'Looking Forward mail', $historyMessage);
					// End Admin History

					// Start Performer Email
			       	$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' and status='Accept' ")->result_array();
					if(!empty($assign_details)){
						foreach ($assign_details as $assignList) {
							$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

							$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
							
							$result_mail_performer = $this->sendMail($from_email, $performer_email, $message, $subject);
						}
					}
			 	 	// End Performer Email

			 	 	// Start Admin Email
					$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
					$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

					if(!empty($admin_email)){
				        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
					}
					// End Admin Email

	    		}

		      	if(!empty($result_mail)){
		        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Booking ignored successfully!")); die;
		      	}
		      	else{
		        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Booking ignore Failed!")); die;
		      	}
	    	}
	    	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Id!")); die;
	      	}
    	}
}

  	public function chasingPerformer(){

	    $this->checkLogin();  

	    $this->load->view('header');
	    $this->load->view('chasingPerformer');
	    $this->load->view('footer');

	    $jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    
    	if(!empty($jsondata)){

			$currentDate = date("Y-m-d");
	      	
	      	$booking_total= $this->db->query("SELECT count(*) as total FROM assign_booking  where status = '' and assign_date > '$currentDate';")->result_array();
	        $total = !empty($booking_total[0]['total']) ? $booking_total[0]['total'] : 0;
      
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
      
	      	$assign_details = $this->db->query("SELECT * FROM assign_booking where status='' and assign_date > '$currentDate' order by id desc $limitto;")->result_array();
	      
	      	if(!empty($assign_details)){
		        foreach($assign_details  as $assign_list){
		          
		          	$temp['id'] = !empty($assign_list['id']) ? $assign_list['id'] : "";
		          	$temp['booking_request_id'] = !empty($assign_list['booking_request_id']) ? $assign_list['booking_request_id'] : "";

		          	$booking_id = !empty($assign_list['booking_request_id']) ? $assign_list['booking_request_id'] : "";

		          	$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_id';")->result_array();
					$temp['name'] = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";

		          	$temp['performer_id'] = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";

		          	$performer_id = !empty($assign_list['performer_id']) ? $assign_list['performer_id'] : "";
		          	$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id';")->result_array();
					$temp['performer_name'] = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";


		          	$temp['assign_date'] = !empty($assign_list['assign_date']) ? date('d-m-Y', strtotime($assign_list['assign_date'])) : "";
		          	$temp['assign_start_datetime'] = !empty($assign_list['assign_start_datetime']) ? $assign_list['assign_start_datetime'] : "";
		          	$temp['assign_end_datetime'] = !empty($assign_list['assign_end_datetime']) ? $assign_list['assign_end_datetime'] : "";
		          	
		          	
		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Chasing Performer Detail List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Chasing Performer Detail List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function sendPerformerEmail(){

	    $this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    
	    if(!empty($jsondata)){

	    	$assign_id = !empty($jsondata['assign_id']) ? $jsondata['assign_id'] : "";
	    	//$assign_id = !empty($jsondata['id']) ? $jsondata['id'] : "";

	    	if(!empty($assign_id)){
	    		foreach ($assign_id as $id) {

	    			$assign_details = $this->db->query("SELECT * FROM assign_booking where id='$id'")->result_array();

	    			$assign_date = !empty($assign_details[0]['assign_date']) ? $assign_details[0]['assign_date'] : "";
	    			$assign_start_datetime = !empty($assign_details[0]['assign_start_datetime']) ? $assign_details[0]['assign_start_datetime'] : "";
	    			$assign_end_datetime = !empty($assign_details[0]['assign_end_datetime']) ? $assign_details[0]['assign_end_datetime'] : "";

			  		$booking_id = !empty($assign_details[0]['booking_request_id']) ? $assign_details[0]['booking_request_id'] : "";
			  		$performer_id = !empty($assign_details[0]['performer_id']) ? $assign_details[0]['performer_id'] : "";

			  		$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_id'")->result_array();

			  		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
			  		$event_date = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
			  		$show_time = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
			  		$show_type = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";
					$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
					$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
					
			  		$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id'")->result_array();
			  		$ids=base64_encode($performer_id);
			  		$booking_ids=base64_encode($booking_id);
			  		$accepturl = HTTP_CONTROLLER.",/?".$ids."/?".$booking_ids;
			  		$to_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
			  		$show_type = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";
					$show_type_details = explode(",",$show_type);
					$typeArray = array();
					
					if(!empty($show_type_details)){
						foreach($show_type_details as $show_type_list){
							
							$category_details = $this->db->query("SELECT * FROM categories where id='$show_type_list';")->result_array();
							$typeArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}	
					}
					
					$show= !empty($typeArray) ? implode(",",$typeArray) : "";
		          	

			    	$subject = !empty($jsondata['email_template_subject']) ? $jsondata['email_template_subject'] : "";
			    	$message = !empty($jsondata['email_template_html']) ? $jsondata['email_template_html'] : "";
			    	$from_email = !empty($jsondata['email_from']) ? $jsondata['email_from'] : "";
			    	$show=explode('',$typeArray);
			        $data = array('URL' => $accepturl,'SHOW_NAME' => $name,'SHOW_DATE'=>$assign_date,'SHOW_START_TIME'=>$assign_start_datetime,'ADDRESS' => $party_address,'SHOW_TYPE'=>$show,'SHOW_DETAILS'=>$show_type_text);
			        if(!empty($data)){
			        	foreach($data as $key => $value){
			        		$subject=	str_replace('{{'.$key.'}}', $value, $subject);
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			 	 	$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);

			 	 	// Start Admin History
					$historyMessage = "Admin sent performer mail to ".$name." booking request details";
					$resultHistory = $this->addHistory('Booking Request', 'performer mail', $historyMessage);
					// End Admin History

			 	 	// Start Admin Email
					$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
					$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

					if(!empty($admin_email)){
				        $result_mail_admin = $this->sendMail($from_email, $admin_email, $message, $subject);
					}
					// End Admin Email
	    		}
	    		$response[]=array();
		      	if(!empty($result_mail)){
		        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Email Sent successfully!", 'response' =>$response)); die;
		      	}
		      	else{
		        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Email Sending Failed", 'response' =>$response)); die;
		      	}
	    	}
	    	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Booking Id!", 'response' =>$response)); die;
	      	}
    	}
  	}




  	public function feedback(){

	    $this->load->view('header');
	    $this->load->view('feedback');
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
		        $feedback_total = $this->db->query("SELECT count(*) as total FROM feedback where (rating like '%$searchdata%');")->result_array();
		        $total = !empty($feedback_total[0]['total']) ? $feedback_total[0]['total'] : 0;
	      	}
	      	else{
		        $feedback_total = $this->db->query("SELECT count(*) as total FROM feedback;")->result_array();
		        $total = !empty($feedback_total[0]['total']) ? $feedback_total[0]['total'] : 0;
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
      
	      	$feedback_details = $this->db->query("SELECT * FROM feedback where (rating like '%$searchdata%') order by id desc $limitto;")->result_array();
	      
	      	if(!empty($feedback_details)){
		        foreach($feedback_details  as $feedback_list){
		          
		          	$temp['id'] = !empty($feedback_list['id']) ? $feedback_list['id'] : "";
		          	$booking_id = !empty($feedback_list['booking_id']) ? $feedback_list['booking_id'] : "";

		          	$booking_total = $this->db->query("SELECT * FROM booking_request where id= '$booking_id' ;")->result_array();
		        	$temp['booking_name'] = !empty($booking_total[0]['name']) ? $booking_total[0]['name'] : 0;

		        	$performer_id = !empty($feedback_list['performer_id']) ? $feedback_list['performer_id'] : "";

		        	$performer_total = $this->db->query("SELECT * FROM performers where id= '$performer_id';")->result_array();
		        	$temp['performer_name'] = !empty($performer_total[0]['name']) ? $performer_total[0]['name'] : 0;

		          	$temp['good_points'] = !empty($feedback_list['good_points']) ? $feedback_list['good_points'] : "";
		          	$temp['bad_points'] = !empty($feedback_list['bad_points']) ? $feedback_list['bad_points'] : "";
		          	$temp['rating'] = !empty($feedback_list['rating']) ? $feedback_list['rating'] : "";

		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Feedback List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Feedback List!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function feedbackMail(){

  		$this->checkLogin();  
	   
		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);

		if(!empty($jsondata)){

			$id = !empty($jsondata['id']) ? $jsondata['id'] : "";

			$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id' ")->result_array();
  		
	  		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
			$to_email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";

			$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
			$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
			$host = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
			$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";
			$child=!empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
			$show_type_text=!empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
			$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
			$showArray = array();
			if(!empty($categoryDetails)){
				foreach ($categoryDetails as $categoryList) {

					$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
					$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
				}
			}

			$showType = !empty($showArray) ? implode(',', $showArray) : "";

			// Start Admin History
			$historyMessage = "Admin sent mail to ".$name." booking request details";
			$resultHistory = $this->addHistory('Booking Request', 'Feedback', $historyMessage);
			// End Admin History

			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='ADMIN_REQ_FEEDBACK_PERFORMER' ")->result_array();

	        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
	        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
	        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

	        $booking_id = !empty($id) ? base64_encode($id) : "";

	        $assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' and status='Accept' ")->result_array();
	        $count = 0;
	        if($assign_details){
	        	foreach ($assign_details as $assignList) {

	        		$performer_id = !empty($assignList['performer_id']) ? base64_encode($assignList['performer_id']) : "";
	        		$url = HTTP_CONTROLLER."generalAddFeedback/?".$booking_id.'/?'.$performer_id;

	        		$data = array('LINK' => $url, 'SHOW_DATE' => $showDate,'HOST' => $host, 'SHOW_START_TIME' => $showTime,'CHILD_NAME' => $child, 'SHOW_TYPE' => $showType, 'SHOW_DETAILS' => $show_type_text);
			        if(!empty($data)){
			        	foreach($data as $key => $value){
			        		$subject = str_replace('{{'.$key.'}}', $value, $subject);
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			 	 	$result_mail = $this->sendMail($from_email, $to_email, $message, $subject);
			 	 	$count = $count + 1;
	        	}
	        }

	        if(!empty($count)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Feedback mail sent successfully!")); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Feedback mail does not sent!")); die;
	      	}
		}

  	}

  	public function feedbackBooking(){

  		$jsondata =file_get_contents("php://input");
	    $jsondata =json_decode($jsondata,true);

	    $response = array();
	    $temp = array();

    	if(!empty($jsondata)){

    		$id = !empty($jsondata['id']) ? base64_decode($jsondata['id']) : "";

	      	$booking_details = $this->db->query("SELECT * FROM booking_request where id='$id';")->result_array();
	      
	      	if(!empty($booking_details)){
	        	foreach($booking_details  as $booking_list){
	          
		          	$temp['id'] = !empty($booking_list['id']) ? $booking_list['id'] : "";
		          	$temp['booking_name'] = !empty($booking_list['name']) ? $booking_list['name'] : "";
		          	$temp['showDate'] = !empty($booking_list['event_date']) ? $booking_list['event_date'] : "";
		          	//$temp['showTime'] = !empty($booking_list['show_time']) ? $booking_list['show_time'] : "";
		          	$temp['showTime'] = !empty($booking_list['show_time']) ? date('h:i A', strtotime($booking_list['show_time'])) : "";
	    			$temp['show_type_text'] = !empty($booking_list['show_type_text']) ? $booking_list['show_type_text'] : "";
		          
		          	$temp['showType'] = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";
		          	$categoryID = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";
				$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
				$showArray = array();
				if(!empty($categoryDetails)){
					foreach ($categoryDetails as $categoryList) {

						$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
						$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
					}
				}

				$temp['showType'] = !empty($showArray) ? implode(',', $showArray) : "";

		          	$temp['child_name'] = !empty($booking_list['child_name']) ? $booking_list['child_name'] : "";
		          	
		          	$response[] =$temp;
	        	}
	      	}
      
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Feedback Booking Form!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty Feedback Booking Form!", 'response' =>$response)); die;
	      	}
	    }
  	}

  	public function generalAddFeedback(){

  		$this->load->view('generalAddFeedback');

		$jsondata=file_get_contents("php://input");
		$jsondata=json_decode($jsondata,true);
		
	    $response = array();
	    $temp = array();

	    if(!empty($jsondata)){

	    	$booking_id = !empty($jsondata['booking_id']) ? base64_decode($jsondata['booking_id']) : "";
	    	$performer_id = !empty($jsondata['performer_id']) ? base64_decode($jsondata['performer_id']) : "";
	    	$good_points = !empty($jsondata['good_points']) ? $jsondata['good_points'] : "";
	    	$bad_points = !empty($jsondata['bad_points']) ? $jsondata['bad_points'] : "";
	    	$rating = !empty($jsondata['rating']) ? $jsondata['rating'] : "";
	    	
	    	$feedback_details = $this->db->query("SELECT count(*) as total FROM feedback where booking_id='$booking_id' and performer_id='$performer_id';")->result_array();
	    	$total = !empty($feedback_details[0]['total']) ? $feedback_details[0]['total'] : "";

	    	if($total == 0){

	    		$postData = array(
					'booking_id'=> $booking_id,
					'performer_id'=> $performer_id,
					'good_points'=> $good_points,
					'bad_points'=> $bad_points,
					'rating'=> $rating,
					'created_on'=> date("Y-m-d H:i:s"),
					'updated_on'=> date("Y-m-d H:i:s")
				);
    			$result = $this->db->insert('feedback',$postData);


if($result){
					$updatebookingassign = $this->db->query("UPDATE `assign_booking` SET `booking_status`='Completed' WHERE `booking_request_id`='$booking_id' AND `performer_id`='$performer_id';");
				
					$awaiting_details = $this->db->query("SELECT count(*) as awaiting FROM assign_booking where booking_request_id='$booking_id' and booking_status='Feedback Pending';")->result_array();
	    			$awaiting = !empty($awaiting_details[0]['awaiting']) ? $awaiting_details[0]['awaiting'] : "";

			    	if($awaiting == 0){
			    		$date=date("Y-m-d H:i:s");
						$updatebookingrequest = $this->db->query("UPDATE `booking_request` SET `booking_status`='Completed',`updated_on`='$date'  WHERE `id`='$booking_id'");   
					} 

}

				$booking_details = $this->db->query("SELECT * FROM booking_request where id='$booking_id';")->result_array();

	    		$name = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
	    		$showDate = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";
	    		$showTime = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
	    		$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
	    		$showType = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";
	    		$child_name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
				$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";
	    		
				$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";
				$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
				$showArray = array();
				if(!empty($categoryDetails)){
					foreach ($categoryDetails as $categoryList) {

						$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
						$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
					}
				}

				$showType = !empty($showArray) ? implode(',', $showArray) : "";


	    		// Start Admin History
				$historyMessage = "Performer fill form for ".$name." booking request details";
				$resultHistory = $this->addHistory('Booking Request', 'Performer Feedback Form', $historyMessage);
				// End Admin History

	    		// Start Admin Email
	    		$admin_details = $this->db->query("SELECT * FROM admin;")->result_array();
	    		$admin_email = !empty($admin_details[0]['email']) ? $admin_details[0]['email'] : "";

	    		if(!empty($admin_email)){

	    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='FEEDBACK_FILL_FORM_PERFORMER' ")->result_array();

			        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
			        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
			        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

			        $data = array('SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType,'SHOW_DETAILS' => $show_type_text,'CHILD_NAME' => $child_name);
			        if(!empty($data)){
			        	foreach($data as $key => $value){

							$subject = str_replace('{{'.$key.'}}', $value, $subject);
							$message = str_replace('{{'.$key.'}}', $value, $message);
						}
			       	}

			        $result_mail = $this->sendMail($from_email, $admin_email, $message, $subject);
	    		}
	    		// End Admin Email

	    		// Start Performer Email
	    		$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ;")->result_array();
	    		$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";

	    		if(!empty($performer_email)){

	    			$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='THANK_YOU' ")->result_array();

			        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
			        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
			        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

			        $result_mail = $this->sendMail($from_email, $performer_email, $message, $subject);
	    		}
	    		// End Performer Email
	    		//, 'response' =>$response 
	    		//, 'response' =>$response
	    		//, 'response' =>$response

    			if(!empty($result)){
    			   	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "Feedback added successfully!",'response' =>$response)); die;
		      	}
		      	else{
		        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Feedback does not added!",'response' =>$response)); die;
		      	}
	    	}
	    	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Feedback already filled!",'response' =>$response)); die;
	      	}
    	} 
  	}

  		public function editfeedback(){
  			
  			$id=$_REQUEST['id'];
  			$temp = array();
  		$temp['feedback_details'] = $this->db->query("SELECT * FROM feedback where id='$id' ;")->result_array();
  		$this->load->view('header');
  		$this->load->view('editfeedback',$temp);
  		$this->load->view('footer');
		

	   
  		}
  		public function saveeditedfeedback(){
  			 $id=$_REQUEST['id'];
  			 $good_points=$_REQUEST['good_points'];
  			$bad_points=$_REQUEST['bad_points'];
  			$rating=$_REQUEST['rating'];
  			$updatebookingrequest = $this->db->query("UPDATE `feedback` SET `good_points`='$good_points',`bad_points`='$bad_points',`rating`='$rating' WHERE `id`='$id'");  
  			if($updatebookingrequest){
  				echo "<script>window.location.href='http://supersteph.com/supersteph/welcome/feedback?response=success';</script>";
  			}else{
  				echo "NOt working "; 
  			}
  		}
  	public function cronFeedbackMail(){

  		$booking_details = $this->db->query("SELECT * FROM booking_request")->result_array();
  		if($booking_details){
  			foreach ($booking_details as $bookingList) {

  				$booking_status = !empty($bookingList['booking_status']) ? $bookingList['booking_status'] : "";

  				if($booking_status != 'Cancelled'){

	  				$id = !empty($bookingList['id']) ? $bookingList['id'] : "";
	  				$host = !empty($bookingList['name']) ? $bookingList['name'] : "";
	  				$to_email = !empty($bookingList['email']) ? $bookingList['email'] : "";
	  				$showDate = !empty($bookingList['event_date']) ? $bookingList['event_date'] : "";
	  				$showTime = !empty($bookingList['show_time']) ? $bookingList['show_time'] : "";
			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		

	  				$categoryID = !empty($bookingList['show_type']) ? $bookingList['show_type'] : "";
	  				$show_type_text = !empty($bookingList['show_type_text']) ? $bookingList['show_type_text'] : "";

					$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
					$showArray = array();
					if(!empty($categoryDetails)){
						foreach ($categoryDetails as $categoryList) {

							$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
							$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}
					}

					$showType = !empty($showArray) ? implode(',', $showArray) : "";

					$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='ADMIN_REQ_FEEDBACK_PERFORMER' ")->result_array();

			        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
			        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
			        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

			        $booking_id = !empty($id) ? base64_encode($id) : "";

			        $assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' and status='Accept' ")->result_array();
			        if($assign_details){
			        	foreach ($assign_details as $assignList) {

			        		$showDate = !empty($bookingList['event_date']) ? $bookingList['event_date'] : "";

			  				$currentDate = date("Y-m-d");
			  				$one_day = date('Y-m-d', strtotime($showDate . ' +1 day'));
			  				$three_day = date('Y-m-d', strtotime($showDate . ' +3 day'));
			  				$five_day = date('Y-m-d', strtotime($showDate . ' +5 day'));
			  				$ten_day = date('Y-m-d', strtotime($showDate . ' +10 day'));

	  						if($currentDate == $one_day){

	  							// Start Admin History
								$historyMessage = "Admin sent mail to ".$host." booking request details";
								$resultHistory = $this->addHistory('Booking Request', 'Feedback mail', $historyMessage);
								// End Admin History

	  							$performer_id = !empty($assignList['performer_id']) ? base64_encode($assignList['performer_id']) : "";

	  							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

								$performer_name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
								$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
								
				        		$url = HTTP_CONTROLLER."generalAddFeedback/?".$booking_id.'/?'.$performer_id;

				        		$data = array('LINK' => $url, 'SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime,'SHOW_DETAILS' => $show_type_text, 'SHOW_TYPE' => $showType);
						        if(!empty($data)){
						        	foreach($data as $key => $value){
						        		$subject = str_replace('{{'.$key.'}}', $value, $subject);
										$message = str_replace('{{'.$key.'}}', $value, $message);
									}
						       	}

						 	 	$result_mail = $this->sendMail($from_email, $performer_email, $message, $subject);
	  						}

	  						if($currentDate == $three_day){

	  							// Start Admin History
								$historyMessage = "Admin sent mail to ".$host." booking request details";
								$resultHistory = $this->addHistory('Booking Request', 'Feedback mail', $historyMessage);
								// End Admin History

	  							$performer_id = !empty($assignList['performer_id']) ? base64_encode($assignList['performer_id']) : "";

	  							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

								$performer_name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
								$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
								
				        		$url = HTTP_CONTROLLER."generalAddFeedback/?".$booking_id.'/?'.$performer_id;

				        		$data = array('LINK' => $url, 'SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime,'SHOW_DETAILS' => $show_type_text,  'SHOW_TYPE' => $showType);
						        if(!empty($data)){
						        	foreach($data as $key => $value){
						        		$subject = str_replace('{{'.$key.'}}', $value, $subject);
										$message = str_replace('{{'.$key.'}}', $value, $message);
									}
						       	}

						 	 	$result_mail = $this->sendMail($from_email, $performer_email, $message, $subject);
	  						}


	  						if($currentDate == $five_day){

	  							// Start Admin History
								$historyMessage = "Admin sent mail to ".$host." booking request details";
								$resultHistory = $this->addHistory('Booking Request', 'Feedback mail', $historyMessage);
								// End Admin History

	  							$performer_id = !empty($assignList['performer_id']) ? base64_encode($assignList['performer_id']) : "";

	  							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

								$performer_name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
								$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
								
				        		$url = HTTP_CONTROLLER."generalAddFeedback/?".$booking_id.'/?'.$performer_id;

				        		$data = array('LINK' => $url, 'SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime,'SHOW_DETAILS' => $show_type_text,  'SHOW_TYPE' => $showType);
						        if(!empty($data)){
						        	foreach($data as $key => $value){
						        		$subject = str_replace('{{'.$key.'}}', $value, $subject);
										$message = str_replace('{{'.$key.'}}', $value, $message);
									}
						       	}

						 	 	$result_mail = $this->sendMail($from_email, $performer_email, $message, $subject);
	  						}

	  						if($currentDate == $ten_day){

	  							// Start Admin History
								$historyMessage = "Admin sent mail to ".$host." booking request details";
								$resultHistory = $this->addHistory('Booking Request', 'Feedback mail', $historyMessage);
								// End Admin History

	  							$performer_id = !empty($assignList['performer_id']) ? base64_encode($assignList['performer_id']) : "";

	  							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

								$performer_name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
								$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
								
				        		$url = HTTP_CONTROLLER."generalAddFeedback/?".$booking_id.'/?'.$performer_id;

				        		$data = array('LINK' => $url, 'SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime,'SHOW_DETAILS' => $show_type_text,  'SHOW_TYPE' => $showType);
						        if(!empty($data)){
						        	foreach($data as $key => $value){
						        		$subject = str_replace('{{'.$key.'}}', $value, $subject);
										$message = str_replace('{{'.$key.'}}', $value, $message);
									}
						       	}

						 	 	$result_mail = $this->sendMail($from_email, $performer_email, $message, $subject);
	  						}

			        	}
			        }
		        }
  			}
  		}	
  	}


  	public function cronStartShowPerformerMail(){

  		$booking_details = $this->db->query("SELECT * FROM booking_request")->result_array();
  		if($booking_details){
  			foreach ($booking_details as $bookingList) {

  				$booking_status = !empty($bookingList['booking_status']) ? $bookingList['booking_status'] : "";

  				if($booking_status != 'Cancelled'){

  					$id = !empty($bookingList['id']) ? $bookingList['id'] : "";
	  				$host = !empty($bookingList['name']) ? $bookingList['name'] : "";
	  				$to_email = !empty($bookingList['email']) ? $bookingList['email'] : "";
	  				$showDate = !empty($bookingList['event_date']) ? $bookingList['event_date'] : "";
	  				$showTime = !empty($bookingList['show_time']) ? $bookingList['show_time'] : "";

			$showTime = !empty($booking_details[0]['show_time']) ? date('h:i A', strtotime($booking_details[0]['show_time'])) : "";
	    		
	  				$categoryID = !empty($bookingList['show_type']) ? $bookingList['show_type'] : "";

					$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
					$showArray = array();
					if(!empty($categoryDetails)){
						foreach ($categoryDetails as $categoryList) {

							$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
							$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
						}
					}

					$showType = !empty($showArray) ? implode(',', $showArray) : "";

					$email_details = $this->db->query("SELECT * FROM email_templates where email_template_name='START_SHOW_PERFORMER' ")->result_array();

			        $subject = !empty($email_details[0]['email_template_subject']) ? $email_details[0]['email_template_subject'] : "";
			        $message = !empty($email_details[0]['email_template_html']) ? $email_details[0]['email_template_html'] : "";
			        $from_email = !empty($email_details[0]['email_from']) ? $email_details[0]['email_from'] : "";

			        $booking_id = !empty($id) ? base64_encode($id) : "";

			        $assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' and status='Accept' ")->result_array();
			        if($assign_details){
			        	foreach ($assign_details as $assignList) {

			        		$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

							$performer_name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
							$performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";

			        		$showDate = !empty($bookingList['event_date']) ? $bookingList['event_date'] : "";
			    
			        		$show_date_time = $showDate.' '.$showTime;
			        		$two_show_date = date("Y-m-d H:i:s", strtotime($show_date_time . ' -2 hours'));
			        		
			  				$currentDate = date("Y-m-d H:i:s");
			  				
	  						if($currentDate >= $two_show_date && $currentDate <= $two_show_date){

	  							// Start Admin History
								$historyMessage = "Admin sent mail to performer for ".$host." booking request details";
								$resultHistory = $this->addHistory('Booking Request', 'Start show mail', $historyMessage);
								// End Admin History

				        		$data = array('PERFORMER_NAME' => $performer_name, 'SHOW_DATE' => $showDate, 'SHOW_START_TIME' => $showTime, 'SHOW_TYPE' => $showType);
						        if(!empty($data)){
						        	foreach($data as $key => $value){
						        		$subject = str_replace('{{'.$key.'}}', $value, $subject);
										$message = str_replace('{{'.$key.'}}', $value, $message);
									}
						       	}

						 	 	$result_mail = $this->sendMail($from_email, $performer_email, $message, $subject);
	  						}

			        	}
			        }
  				}
  			}
  		}	
  	}



  	public function downloadPdf($id = NULL){

	    $this->checkLogin(); 

	    $booking_details = $this->db->query("SELECT * FROM booking_request where id='$id';")->result_array();

    	$name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
    	$email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
    	$event_date = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";

    	$show_time = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
    	$duration = !empty($booking_details[0]['duration']) ? $booking_details[0]['duration'] : "";
    	$host = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";

    	$event_amount = !empty($booking_details[0]['event_amount']) ? $booking_details[0]['event_amount'] : 0;
    	$paid_amount = !empty($booking_details[0]['paid_amount']) ? $booking_details[0]['paid_amount'] : 0;
    	$remain_amount = !empty($booking_details[0]['remain_amount']) ? $booking_details[0]['remain_amount'] : 0;

    	$party_bags = !empty($booking_details[0]['party_bags']) ? $booking_details[0]['party_bags'] : "";
    	$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
    	$home_address = !empty($booking_details[0]['home_address']) ? $booking_details[0]['home_address'] : "";

    	$party_in_out = !empty($booking_details[0]['party_in_out']) ? $booking_details[0]['party_in_out'] : "";
    	$rain_plan = !empty($booking_details[0]['rain_plan']) ? $booking_details[0]['rain_plan'] : "";
    	$age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";

    	$dob = !empty($booking_details[0]['dob']) ? $booking_details[0]['dob'] : "";
    	$children_count = !empty($booking_details[0]['children_count']) ? $booking_details[0]['children_count'] : "";
    	$children_party = !empty($booking_details[0]['children_party']) ? $booking_details[0]['children_party'] : "";

    	$gender_party = !empty($booking_details[0]['gender_party']) ? $booking_details[0]['gender_party'] : "";
    	$party_seen = !empty($booking_details[0]['party_seen']) ? $booking_details[0]['party_seen'] : "";
    	$mobile_number = !empty($booking_details[0]['mobile_number']) ? $booking_details[0]['mobile_number'] : "";
    	$second_mobile = !empty($booking_details[0]['second_mobile']) ? $booking_details[0]['second_mobile'] : "";

    	$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
		$showArray = array();
		if(!empty($categoryDetails)){
			foreach ($categoryDetails as $categoryList) {

				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
			}
		}

		$show_type = !empty($showArray) ? implode(',', $showArray) : "";

		$postData = array(
			'print_count'=> '1',
			'updated_on'=> date("Y-m-d H:i:s")
		);

		$postWhere = array(
			'id=' => $id
		);

		$this->db->where($postWhere);
		$result = $this->db->update('booking_request',$postData);

		$historyMessage = "Admin download pdf ".$name." booking request details";
		$resultHistory = $this->addHistory('Booking Request', 'Pdf', $historyMessage);

	    ob_start();
	    $html = ob_get_clean();
	    $html = utf8_encode($html);

	    $html .= '
		    <!DOCTYPE html>
		<html lang="en">
		  <head>
		    <meta charset="utf-8">
		    <title>Example 1</title>
		    <style>
		      .clearfix:after {
		        content: "";
		        display: table;
		        clear: both;
		      }

		      a {
		        color: #5D6975;
		        text-decoration: underline;
		      }

		      body {
		        position: relative;
		        width: 21cm;  
		        height: 29.7cm; 
		        margin: 0 auto; 
		        color: #001028;
		        background: #FFFFFF; 
		        font-family: Arial, sans-serif; 
		        font-size: 12px; 
		        font-family: Arial;
		      }

		      header {
		        padding: 10px 0;
		        margin-bottom: 30px;
		      }

		      #logo {
		        text-align: center;
		        margin-bottom: 10px;
		      }

		      #logo img {
		        width: 90px;
		      }

		      h1 {
		        border-top: 1px solid  #5D6975;
		        border-bottom: 1px solid  #5D6975;
		        color: #5D6975;
		        font-size: 2.4em;
		        line-height: 1.4em;
		        font-weight: normal;
		        text-align: center;
		        margin: 0 0 20px 0;
		        background: url(dimension.png);
		      }

		      #project {
		        float: left;
		      }

		      #project span {
		        color: #5D6975;
		        text-align: right;
		        width: 52px;
		        margin-right: 10px;
		        display: inline-block;
		        font-size: 0.8em;
		      }

		      #company {
		        float: right;
		        text-align: right;
		      }

		      #project div,
		      #company div {
		        white-space: nowrap;
		        font-size: 10px;        
		      }

		      table {
		        width: 100%;
		        border-collapse: collapse;
		        border-spacing: 0;
		        margin-bottom: 20px;
		      }

		      table tr:nth-child(2n-1) td {
		        background: #F5F5F5;
		      }

		      table th,
		      table td {
		        text-align: center;
		      }

		      table th {
		        padding: 5px 0px;
		        font-size: 10px;
		      }

		      table .service,
		      table .desc {
		        text-align: left;
		      }

		      table td {
		        padding: 20px;
		        text-align: left;
		        font-size: 20px;
		      }

		      table td.service,
		      table td.desc {
		        vertical-align: top;
		      }

		      table td.unit,
		      table td.qty,
		      table td.total {
		        font-size: 1.2em;
		      }

		      table td.grand {
		        border-top: 1px solid #5D6975;;
		      }

		      #notices .notice {
		        color: #5D6975;
		        font-size: 1.2em;
		      }

		      footer {
		        color: #5D6975;
		        width: 100%;
		        height: 30px;
		        position: absolute;
		        bottom: 0;
		        border-top: 1px solid #C1CED9;
		        padding: 8px 0;
		        text-align: center;
		      }
		    </style>
		  </head>
		  <body>
		    <header class="clearfix">
		      
		      <h1>Invoice</h1>
		      <div id="company" class="clearfix">
		        <div>Date: 21/12/2017</div>
		      </div>
		      <div id="project">
		        <div>PROJECT: Super Steph</div>
		      </div>
		    </header>
		    <main>
		      <div>
		        <strong>Fee: '.$event_amount.', <span style="background:green;">Paid: '.$paid_amount.'</span>, <span style="background:red;">Remain: '.$remain_amount.'</span></strong>
		      </div>
		      <table>
		       
		        <tbody>
		          	<tr>
			            <th>Name</th>
			            <th>:</th>
			            <th>'.$name.'</th>
		          	</tr>
		          	<tr>  
			            <th>Email</th>
			            <th>:</th>
			            <th>'.$email.'</th>
		          	</tr>
		           	<tr>  
			            <th>Date</th>
			            <th>:</th>
			            <th>'.$event_date.'</th>
		          	</tr>
		           	<tr>  
			            <th>Time OF Show</th>
			            <th>:</th>
			            <th>'.$show_time.'</th>
		          	</tr>
		           	<tr>  
			            <th>Duration</th>
			            <th>:</th>
			            <th>'.$duration.'</th>
		          	</tr>
		          	<tr>  
			            <th>Host</th>
			            <th>:</th>
			            <th>'.$host.'</th>
		          	</tr>
		          	<tr>  
			            <th>Type Of Show</th>
			            <th>:</th>
			            <th>'.$show_type.'</th>
		          	</tr>
		          	<tr>  
			            <th>How many party bags required</th>
			            <th>:</th>
			            <th>'.$party_bags.'</th>
		          	</tr>
		          	<tr>  
			            <th>Address OF Party</th>
			            <th>:</th>
			            <th>'.$party_address.'</th>
		          	</tr>
		          	<tr>  
			            <th>Home Address</th>
			            <th>:</th>
			            <th>'.$home_address.'</th>
		          	</tr>
		          	<tr>  
			            <th>Is the Party inside or outside</th>
			            <th>:</th>
			            <th>'.$party_in_out.'</th>
		          	</tr>
		          	<tr>  
			            <th>Rain contingency plans</th>
			            <th>:</th>
			            <th>'.$rain_plan.'</th>
		          	</tr>
		          	<tr>  
			            <th>Age OF Child</th>
			            <th>:</th>
			            <th>'.$age.'</th>
		          	</tr>
		          	<tr>  
			            <th>Date OF Birth</th>
			            <th>:</th>
			            <th>'.$dob.'</th>
		          	</tr>
		          	<tr>  
			            <th>Approximate number of children of party</th>
			            <th>:</th>
			            <th>'.$children_count.'</th>
		          	</tr>
		          	<tr>  
			            <th>Main Age of children at the party</th>
			            <th>:</th>
			            <th>'.$children_party.'</th>
		          	</tr>
		          	<tr>  
			            <th>Boys, girls or mixed party</th>
			            <th>:</th>
			            <th>'.$gender_party.'</th>
		          	</tr>
		          	<tr>  
			            <th>Has your child Seen my show before</th>
			            <th>:</th>
			            <th>'.$party_seen.'</th>
		          	</tr>
		          	<tr>  
			            <th>Mobile Number</th>
			            <th>:</th>
			            <th>'.$mobile_number.'</th>
		          	</tr>
		          	<tr>  
			            <th>2nd Mobile Number (required)</th>
			            <th>:</th>
			            <th>'.$second_mobile.'</th>
		          	</tr>
		        </tbody>
		        
		      </table>
		    </main>
		    
		  </body>
		</html>
		';


		$mpdf = new mPDF(); 
		$mpdf->allow_charset_conversion = true;
		$mpdf->charset_in = 'UTF-8';
		$mpdf->setFooter();
		$mpdf->AddPage('','','','','on');
		$mpdf->WriteHTML($html);
		$mpdf->Output('booking.pdf', 'D');

		//exit();
  	}

  	public function alldownloadPdf($id = NULL){

  		$this->checkLogin(); 
  		$filter = $id;
  		
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
			$fiveDay = date('Y-m-d', strtotime($currentDate . ' +7 day'));
			$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$fiveDay') ";
		}
		else{
			$upcomingFilter="";
		}

		$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status!='Cancelled' and print_count='0' $upcomingFilter;")->result_array();
		
	    if(!empty($booking_details)){

	    	$mpdf = new mPDF(); 

	    	foreach ($booking_details as $booking_list) {
	    		
	    		$id = !empty($booking_list['id']) ? $booking_list['id'] : "";

	    		$name = !empty($booking_list['child_name']) ? $booking_list['child_name'] : "";
		    	$email = !empty($booking_list['email']) ? $booking_list['email'] : "";
		    	$event_date = !empty($booking_list['event_date']) ? $booking_list['event_date'] : "";

		    	$show_time = !empty($booking_list['show_time']) ? $booking_list['show_time'] : "";
		    	$duration = !empty($booking_list['duration']) ? $booking_list['duration'] : "";
		    	$host = !empty($booking_list['name']) ? $booking_list['name'] : "";

		    	$event_amount = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : 0;
		    	$paid_amount = !empty($booking_list['paid_amount']) ? $booking_list['paid_amount'] : 0;
		    	$remain_amount = !empty($booking_list['remain_amount']) ? $booking_list['remain_amount'] : 0;

		    	$party_bags = !empty($booking_list['party_bags']) ? $booking_list['party_bags'] : "";
		    	$party_address = !empty($booking_list['party_address']) ? $booking_list['party_address'] : "";
		    	$home_address = !empty($booking_list['home_address']) ? $booking_list['home_address'] : "";

		    	$party_in_out = !empty($booking_list['party_in_out']) ? $booking_list['party_in_out'] : "";
		    	$rain_plan = !empty($booking_list['rain_plan']) ? $booking_list['rain_plan'] : "";
		    	$age = !empty($booking_list['age']) ? $booking_list['age'] : "";

		    	$dob = !empty($booking_list['dob']) ? $booking_list['dob'] : "";
		    	$children_count = !empty($booking_list['children_count']) ? $booking_list['children_count'] : "";
		    	$children_party = !empty($booking_list['children_party']) ? $booking_list['children_party'] : "";

		    	$gender_party = !empty($booking_list['gender_party']) ? $booking_list['gender_party'] : "";
		    	$party_seen = !empty($booking_list['party_seen']) ? $booking_list['party_seen'] : "";
		    	$mobile_number = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		    	$second_mobile = !empty($booking_list['second_mobile']) ? $booking_list['second_mobile'] : "";

		    	$categoryID = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";

		    	$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
				$showArray = array();
				if(!empty($categoryDetails)){
					foreach ($categoryDetails as $categoryList) {

						$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
						$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
					}
				}

				$show_type = !empty($showArray) ? implode(',', $showArray) : "";

				$postData = array(
					'print_count'=> '1',
					'updated_on'=> date("Y-m-d H:i:s")
				);

				$postWhere = array(
					'id=' => $id
				);

				$this->db->where($postWhere);
				$result = $this->db->update('booking_request',$postData);

				$historyMessage = "Admin download pdf ".$name." booking request details";
				$resultHistory = $this->addHistory('Booking Request', 'Pdf', $historyMessage);


				ob_start();
			    $html = ob_get_clean();
			    $html = utf8_encode($html);

			    $html .= '
				    <!DOCTYPE html>
				<html lang="en">
				  <head>
				    <meta charset="utf-8">
				    <title>Example 1</title>
				    <style>
				      .clearfix:after {
				        content: "";
				        display: table;
				        clear: both;
				      }

				      a {
				        color: #5D6975;
				        text-decoration: underline;
				      }

				      body {
				        position: relative;
				        width: 21cm;  
				        height: 29.7cm; 
				        margin: 0 auto; 
				        color: #001028;
				        background: #FFFFFF; 
				        font-family: Arial, sans-serif; 
				        font-size: 12px; 
				        font-family: Arial;
				      }

				      header {
				        padding: 10px 0;
				        margin-bottom: 30px;
				      }

				      #logo {
				        text-align: center;
				        margin-bottom: 10px;
				      }

				      #logo img {
				        width: 90px;
				      }

				      h1 {
				        border-top: 1px solid  #5D6975;
				        border-bottom: 1px solid  #5D6975;
				        color: #5D6975;
				        font-size: 2.4em;
				        line-height: 1.4em;
				        font-weight: normal;
				        text-align: center;
				        margin: 0 0 20px 0;
				        background: url(dimension.png);
				      }

				      #project {
				        float: left;
				      }

				      #project span {
				        color: #5D6975;
				        text-align: right;
				        width: 52px;
				        margin-right: 10px;
				        display: inline-block;
				        font-size: 0.8em;
				      }

				      #company {
				        float: right;
				        text-align: right;
				      }

				      #project div,
				      #company div {
				        white-space: nowrap;
				        font-size: 10px;        
				      }

				      table {
				        width: 100%;
				        border-collapse: collapse;
				        border-spacing: 0;
				        margin-bottom: 20px;
				      }

				      table tr:nth-child(2n-1) td {
				        background: #F5F5F5;
				      }

				      table th,
				      table td {
				        text-align: center;
				      }

				      table th {
				        padding: 5px 0px;
				        font-size: 10px;
				      }

				      table .service,
				      table .desc {
				        text-align: left;
				      }

				      table td {
				        padding: 20px;
				        text-align: left;
				        font-size: 20px;
				      }

				      table td.service,
				      table td.desc {
				        vertical-align: top;
				      }

				      table td.unit,
				      table td.qty,
				      table td.total {
				        font-size: 1.2em;
				      }

				      table td.grand {
				        border-top: 1px solid #5D6975;;
				      }

				      #notices .notice {
				        color: #5D6975;
				        font-size: 1.2em;
				      }

				      footer {
				        color: #5D6975;
				        width: 100%;
				        height: 30px;
				        position: absolute;
				        bottom: 0;
				        border-top: 1px solid #C1CED9;
				        padding: 8px 0;
				        text-align: center;
				      }
				    </style>
				  </head>
				  <body>
				    <header class="clearfix">
				      
				      <h1>Invoice</h1>
				      <div id="company" class="clearfix">
				        <div>Date: 21/12/2017</div>
				      </div>
				      <div id="project">
				        <div>PROJECT: Super Steph</div>
				      </div>
				    </header>
				    <main>
				      <div>
				        <strong>Fee: '.$event_amount.', <span style="background:green;">Paid: '.$paid_amount.'</span>, <span style="background:red;">Remain: '.$remain_amount.'</span></strong>
				      </div>
				      <table>
				       
				        <tbody>
				          	<tr>
					            <th>Name</th>
					            <th>:</th>
					            <th>'.$name.'</th>
				          	</tr>
				          	<tr>  
					            <th>Email</th>
					            <th>:</th>
					            <th>'.$email.'</th>
				          	</tr>
				           	<tr>  
					            <th>Date</th>
					            <th>:</th>
					            <th>'.$event_date.'</th>
				          	</tr>
				           	<tr>  
					            <th>Time OF Show</th>
					            <th>:</th>
					            <th>'.$show_time.'</th>
				          	</tr>
				           	<tr>  
					            <th>Duration</th>
					            <th>:</th>
					            <th>'.$duration.'</th>
				          	</tr>
				          	<tr>  
					            <th>Host</th>
					            <th>:</th>
					            <th>'.$host.'</th>
				          	</tr>
				          	<tr>  
					            <th>Type Of Show</th>
					            <th>:</th>
					            <th>'.$show_type.'</th>
				          	</tr>
				          	<tr>  
					            <th>How many party bags required</th>
					            <th>:</th>
					            <th>'.$party_bags.'</th>
				          	</tr>
				          	<tr>  
					            <th>Address OF Party</th>
					            <th>:</th>
					            <th>'.$party_address.'</th>
				          	</tr>
				          	<tr>  
					            <th>Home Address</th>
					            <th>:</th>
					            <th>'.$home_address.'</th>
				          	</tr>
				          	<tr>  
					            <th>Is the Party inside or outside</th>
					            <th>:</th>
					            <th>'.$party_in_out.'</th>
				          	</tr>
				          	<tr>  
					            <th>Rain contingency plans</th>
					            <th>:</th>
					            <th>'.$rain_plan.'</th>
				          	</tr>
				          	<tr>  
					            <th>Age OF Child</th>
					            <th>:</th>
					            <th>'.$age.'</th>
				          	</tr>
				          	<tr>  
					            <th>Date OF Birth</th>
					            <th>:</th>
					            <th>'.$dob.'</th>
				          	</tr>
				          	<tr>  
					            <th>Approximate number of children of party</th>
					            <th>:</th>
					            <th>'.$children_count.'</th>
				          	</tr>
				          	<tr>  
					            <th>Main Age of children at the party</th>
					            <th>:</th>
					            <th>'.$children_party.'</th>
				          	</tr>
				          	<tr>  
					            <th>Boys, girls or mixed party</th>
					            <th>:</th>
					            <th>'.$gender_party.'</th>
				          	</tr>
				          	<tr>  
					            <th>Has your child Seen my show before</th>
					            <th>:</th>
					            <th>'.$party_seen.'</th>
				          	</tr>
				          	<tr>  
					            <th>Mobile Number</th>
					            <th>:</th>
					            <th>'.$mobile_number.'</th>
				          	</tr>
				          	<tr>  
					            <th>2nd Mobile Number (required)</th>
					            <th>:</th>
					            <th>'.$second_mobile.'</th>
				          	</tr>
				        </tbody>
				        
				      </table>
				    </main>
				    
				  </body>
				</html>
				';


				// $mpdf = new mPDF(); 
				$mpdf->allow_charset_conversion = true;
				$mpdf->charset_in = 'UTF-8';
				$mpdf->setFooter();
				$mpdf->AddPage('','','','','on');
				$mpdf->WriteHTML($html);
				// $mpdf->Output('booking.pdf', 'D');
	    	}

	    	$mpdf->Output('booking.pdf', 'D');
	    }
  	}


  	public function blockalldownloadPdf($id = NULL){

  		$this->checkLogin(); 
  		$filter = $id;
  		
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
		else if($filter == '5'){
			$currentDate = date("Y-m-d");
			$fiveDay = date('Y-m-d', strtotime($currentDate . ' +5 day'));
			$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$fiveDay') ";
		}
		else if($filter == '10'){
			$currentDate = date("Y-m-d");
			$tenDay = date('Y-m-d', strtotime($currentDate . ' +10 day'));
			$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$tenDay') ";
		}
		else{
			$upcomingFilter="";
		}

		$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status='Cancelled' $upcomingFilter;")->result_array();
		
	    if(!empty($booking_details)){

	    	$mpdf = new mPDF(); 

	    	foreach ($booking_details as $booking_list) {
	    		
	    		$id = !empty($booking_list['id']) ? $booking_list['id'] : "";

	    		$name = !empty($booking_list['child_name']) ? $booking_list['child_name'] : "";
		    	$email = !empty($booking_list['email']) ? $booking_list['email'] : "";
		    	$event_date = !empty($booking_list['event_date']) ? $booking_list['event_date'] : "";

		    	$show_time = !empty($booking_list['show_time']) ? $booking_list['show_time'] : "";
		    	$duration = !empty($booking_list['duration']) ? $booking_list['duration'] : "";
		    	$host = !empty($booking_list['name']) ? $booking_list['name'] : "";

		    	$event_amount = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : 0;
		    	$paid_amount = !empty($booking_list['paid_amount']) ? $booking_list['paid_amount'] : 0;
		    	$remain_amount = !empty($booking_list['remain_amount']) ? $booking_list['remain_amount'] : 0;

		    	$party_bags = !empty($booking_list['party_bags']) ? $booking_list['party_bags'] : "";
		    	$party_address = !empty($booking_list['party_address']) ? $booking_list['party_address'] : "";
		    	$home_address = !empty($booking_list['home_address']) ? $booking_list['home_address'] : "";

		    	$party_in_out = !empty($booking_list['party_in_out']) ? $booking_list['party_in_out'] : "";
		    	$rain_plan = !empty($booking_list['rain_plan']) ? $booking_list['rain_plan'] : "";
		    	$age = !empty($booking_list['age']) ? $booking_list['age'] : "";

		    	$dob = !empty($booking_list['dob']) ? $booking_list['dob'] : "";
		    	$children_count = !empty($booking_list['children_count']) ? $booking_list['children_count'] : "";
		    	$children_party = !empty($booking_list['children_party']) ? $booking_list['children_party'] : "";

		    	$gender_party = !empty($booking_list['gender_party']) ? $booking_list['gender_party'] : "";
		    	$party_seen = !empty($booking_list['party_seen']) ? $booking_list['party_seen'] : "";
		    	$mobile_number = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		    	$second_mobile = !empty($booking_list['second_mobile']) ? $booking_list['second_mobile'] : "";

		    	$categoryID = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";

		    	$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
				$showArray = array();
				if(!empty($categoryDetails)){
					foreach ($categoryDetails as $categoryList) {

						$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
						$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
					}
				}

				$show_type = !empty($showArray) ? implode(',', $showArray) : "";

				$postData = array(
					'print_count'=> '1',
					'updated_on'=> date("Y-m-d H:i:s")
				);

				$postWhere = array(
					'id=' => $id
				);

				$this->db->where($postWhere);
				$result = $this->db->update('booking_request',$postData);

				$historyMessage = "Admin download pdf ".$name." booking request details";
				$resultHistory = $this->addHistory('Booking Request', 'Pdf', $historyMessage);


				ob_start();
			    $html = ob_get_clean();
			    $html = utf8_encode($html);

			    $html .= '
				    <!DOCTYPE html>
				<html lang="en">
				  <head>
				    <meta charset="utf-8">
				    <title>Example 1</title>
				    <style>
				      .clearfix:after {
				        content: "";
				        display: table;
				        clear: both;
				      }

				      a {
				        color: #5D6975;
				        text-decoration: underline;
				      }

				      body {
				        position: relative;
				        width: 21cm;  
				        height: 29.7cm; 
				        margin: 0 auto; 
				        color: #001028;
				        background: #FFFFFF; 
				        font-family: Arial, sans-serif; 
				        font-size: 12px; 
				        font-family: Arial;
				      }

				      header {
				        padding: 10px 0;
				        margin-bottom: 30px;
				      }

				      #logo {
				        text-align: center;
				        margin-bottom: 10px;
				      }

				      #logo img {
				        width: 90px;
				      }

				      h1 {
				        border-top: 1px solid  #5D6975;
				        border-bottom: 1px solid  #5D6975;
				        color: #5D6975;
				        font-size: 2.4em;
				        line-height: 1.4em;
				        font-weight: normal;
				        text-align: center;
				        margin: 0 0 20px 0;
				        background: url(dimension.png);
				      }

				      #project {
				        float: left;
				      }

				      #project span {
				        color: #5D6975;
				        text-align: right;
				        width: 52px;
				        margin-right: 10px;
				        display: inline-block;
				        font-size: 0.8em;
				      }

				      #company {
				        float: right;
				        text-align: right;
				      }

				      #project div,
				      #company div {
				        white-space: nowrap;
				        font-size: 10px;        
				      }

				      table {
				        width: 100%;
				        border-collapse: collapse;
				        border-spacing: 0;
				        margin-bottom: 20px;
				      }

				      table tr:nth-child(2n-1) td {
				        background: #F5F5F5;
				      }

				      table th,
				      table td {
				        text-align: center;
				      }

				      table th {
				        padding: 5px 0px;
				        font-size: 10px;
				      }

				      table .service,
				      table .desc {
				        text-align: left;
				      }

				      table td {
				        padding: 20px;
				        text-align: left;
				        font-size: 20px;
				      }

				      table td.service,
				      table td.desc {
				        vertical-align: top;
				      }

				      table td.unit,
				      table td.qty,
				      table td.total {
				        font-size: 1.2em;
				      }

				      table td.grand {
				        border-top: 1px solid #5D6975;;
				      }

				      #notices .notice {
				        color: #5D6975;
				        font-size: 1.2em;
				      }

				      footer {
				        color: #5D6975;
				        width: 100%;
				        height: 30px;
				        position: absolute;
				        bottom: 0;
				        border-top: 1px solid #C1CED9;
				        padding: 8px 0;
				        text-align: center;
				      }
				    </style>
				  </head>
				  <body>
				    <header class="clearfix">
				      
				      <h1>Invoice</h1>
				      <div id="company" class="clearfix">
				        <div>Date: 21/12/2017</div>
				      </div>
				      <div id="project">
				        <div>PROJECT: Super Steph</div>
				      </div>
				    </header>
				    <main>
				      <div>
				        <strong>Fee: '.$event_amount.', <span style="background:green;">Paid: '.$paid_amount.'</span>, <span style="background:red;">Remain: '.$remain_amount.'</span></strong>
				      </div>
				      <table>
				       
				        <tbody>
				          	<tr>
					            <th>Name</th>
					            <th>:</th>
					            <th>'.$name.'</th>
				          	</tr>
				          	<tr>  
					            <th>Email</th>
					            <th>:</th>
					            <th>'.$email.'</th>
				          	</tr>
				           	<tr>  
					            <th>Date</th>
					            <th>:</th>
					            <th>'.$event_date.'</th>
				          	</tr>
				           	<tr>  
					            <th>Time OF Show</th>
					            <th>:</th>
					            <th>'.$show_time.'</th>
				          	</tr>
				           	<tr>  
					            <th>Duration</th>
					            <th>:</th>
					            <th>'.$duration.'</th>
				          	</tr>
				          	<tr>  
					            <th>Host</th>
					            <th>:</th>
					            <th>'.$host.'</th>
				          	</tr>
				          	<tr>  
					            <th>Type Of Show</th>
					            <th>:</th>
					            <th>'.$show_type.'</th>
				          	</tr>
				          	<tr>  
					            <th>How many party bags required</th>
					            <th>:</th>
					            <th>'.$party_bags.'</th>
				          	</tr>
				          	<tr>  
					            <th>Address OF Party</th>
					            <th>:</th>
					            <th>'.$party_address.'</th>
				          	</tr>
				          	<tr>  
					            <th>Home Address</th>
					            <th>:</th>
					            <th>'.$home_address.'</th>
				          	</tr>
				          	<tr>  
					            <th>Is the Party inside or outside</th>
					            <th>:</th>
					            <th>'.$party_in_out.'</th>
				          	</tr>
				          	<tr>  
					            <th>Rain contingency plans</th>
					            <th>:</th>
					            <th>'.$rain_plan.'</th>
				          	</tr>
				          	<tr>  
					            <th>Age OF Child</th>
					            <th>:</th>
					            <th>'.$age.'</th>
				          	</tr>
				          	<tr>  
					            <th>Date OF Birth</th>
					            <th>:</th>
					            <th>'.$dob.'</th>
				          	</tr>
				          	<tr>  
					            <th>Approximate number of children of party</th>
					            <th>:</th>
					            <th>'.$children_count.'</th>
				          	</tr>
				          	<tr>  
					            <th>Main Age of children at the party</th>
					            <th>:</th>
					            <th>'.$children_party.'</th>
				          	</tr>
				          	<tr>  
					            <th>Boys, girls or mixed party</th>
					            <th>:</th>
					            <th>'.$gender_party.'</th>
				          	</tr>
				          	<tr>  
					            <th>Has your child Seen my show before</th>
					            <th>:</th>
					            <th>'.$party_seen.'</th>
				          	</tr>
				          	<tr>  
					            <th>Mobile Number</th>
					            <th>:</th>
					            <th>'.$mobile_number.'</th>
				          	</tr>
				          	<tr>  
					            <th>2nd Mobile Number (required)</th>
					            <th>:</th>
					            <th>'.$second_mobile.'</th>
				          	</tr>
				        </tbody>
				        
				      </table>
				    </main>
				    
				  </body>
				</html>
				';


				// $mpdf = new mPDF(); 
				$mpdf->allow_charset_conversion = true;
				$mpdf->charset_in = 'UTF-8';
				$mpdf->setFooter();
				$mpdf->AddPage('','','','','on');
				$mpdf->WriteHTML($html);
				// $mpdf->Output('booking.pdf', 'D');
	    	}

	    	$mpdf->Output('booking.pdf', 'D');
	    }
  	}


  	public function downloadWord_old($id = NULL){

	    $this->checkLogin(); 

	    header("Content-type: application/rtf");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Disposition: attachment;Filename=event_booking.rtf");

	    $booking_details = $this->db->query("SELECT * FROM booking_request where id='$id';")->result_array();

    	$name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
    	$email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
    	$event_date = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";

    	$show_time = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
    	$duration = !empty($booking_details[0]['duration']) ? $booking_details[0]['duration'] : "";
    	$host = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";

    	$event_amount = !empty($booking_details[0]['event_amount']) ? $booking_details[0]['event_amount'] : 0;
    	$paid_amount = !empty($booking_details[0]['paid_amount']) ? $booking_details[0]['paid_amount'] : 0;
    	$remain_amount = !empty($booking_details[0]['remain_amount']) ? $booking_details[0]['remain_amount'] : 0;

    	$party_bags = !empty($booking_details[0]['party_bags']) ? $booking_details[0]['party_bags'] : "";
    	$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
    	$home_address = !empty($booking_details[0]['home_address']) ? $booking_details[0]['home_address'] : "";

    	$party_in_out = !empty($booking_details[0]['party_in_out']) ? $booking_details[0]['party_in_out'] : "";
    	$rain_plan = !empty($booking_details[0]['rain_plan']) ? $booking_details[0]['rain_plan'] : "";
    	$age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";

    	$dob = !empty($booking_details[0]['dob']) ? $booking_details[0]['dob'] : "";
    	$children_count = !empty($booking_details[0]['children_count']) ? $booking_details[0]['children_count'] : "";
    	$children_party = !empty($booking_details[0]['children_party']) ? $booking_details[0]['children_party'] : "";

    	$gender_party = !empty($booking_details[0]['gender_party']) ? $booking_details[0]['gender_party'] : "";
    	$party_seen = !empty($booking_details[0]['party_seen']) ? $booking_details[0]['party_seen'] : "";
    	$mobile_number = !empty($booking_details[0]['mobile_number']) ? $booking_details[0]['mobile_number'] : "";
    	$second_mobile = !empty($booking_details[0]['second_mobile']) ? $booking_details[0]['second_mobile'] : "";

    	$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
		$showArray = array();
		if(!empty($categoryDetails)){
			foreach ($categoryDetails as $categoryList) {

				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
			}
		}

		$show_type = !empty($showArray) ? implode(',', $showArray) : "";

		$postData = array(
			'print_count'=> '1',
			'updated_on'=> date("Y-m-d H:i:s")
		);

		$postWhere = array(
			'id=' => $id
		);

		$this->db->where($postWhere);
		$result = $this->db->update('booking_request',$postData);

		$historyMessage = "Admin download pdf ".$name." booking request details";
		$resultHistory = $this->addHistory('Booking Request', 'Pdf', $historyMessage);

	    ob_start();
	    
	    $html .= '
		    <!DOCTYPE html>
		<html lang="en">
		  <head>
		    <meta charset="utf-8">
		    <title>Example 1</title>
		    <style>
		      .clearfix:after {
		        content: "";
		        display: table;
		        clear: both;
		      }

		      a {
		        color: #5D6975;
		        text-decoration: underline;
		      }

		      body {
		        position: relative;
		        width: 21cm;  
		        height: 29.7cm; 
		        margin: 0 auto; 
		        color: #001028;
		        background: #FFFFFF; 
		        font-family: Arial, sans-serif; 
		        font-size: 12px; 
		        font-family: Arial;
		      }

		      header {
		        padding: 10px 0;
		        margin-bottom: 30px;
		      }

		      #logo {
		        text-align: center;
		        margin-bottom: 10px;
		      }

		      #logo img {
		        width: 90px;
		      }

		      h1 {
		        border-top: 1px solid  #5D6975;
		        border-bottom: 1px solid  #5D6975;
		        color: #5D6975;
		        font-size: 2.4em;
		        line-height: 1.4em;
		        font-weight: normal;
		        text-align: center;
		        margin: 0 0 20px 0;
		        background: url(dimension.png);
		      }

		      #project {
		        float: left;
		      }

		      #project span {
		        color: #5D6975;
		        text-align: right;
		        width: 52px;
		        margin-right: 10px;
		        display: inline-block;
		        font-size: 0.8em;
		      }

		      #company {
		        float: right;
		        text-align: right;
		      }

		      #project div,
		      #company div {
		        white-space: nowrap;
		        font-size: 10px;        
		      }

		      table {
		        width: 100%;
		        border-collapse: collapse;
		        border-spacing: 0;
		        margin-bottom: 20px;
		      }

		      table tr:nth-child(2n-1) td {
		        background: #F5F5F5;
		      }

		      table th,
		      table td {
		        text-align: center;
		      }

		      table th {
		        padding: 5px 0px;
		        font-size: 10px;
		      }

		      table .service,
		      table .desc {
		        text-align: left;
		      }

		      table td {
		        padding: 20px;
		        text-align: left;
		        font-size: 20px;
		      }

		      table td.service,
		      table td.desc {
		        vertical-align: top;
		      }

		      table td.unit,
		      table td.qty,
		      table td.total {
		        font-size: 1.2em;
		      }

		      table td.grand {
		        border-top: 1px solid #5D6975;;
		      }

		      #notices .notice {
		        color: #5D6975;
		        font-size: 1.2em;
		      }

		      footer {
		        color: #5D6975;
		        width: 100%;
		        height: 30px;
		        position: absolute;
		        bottom: 0;
		        border-top: 1px solid #C1CED9;
		        padding: 8px 0;
		        text-align: center;
		      }
		    </style>
		  </head>
		  <body>
		    <header class="clearfix">
		      
		      <h1>Invoice</h1>
		      <div id="company" class="clearfix">
		        <div>Date: 21/12/2017</div>
		      </div>
		      <div id="project">
		        <div>PROJECT: Super Steph</div>
		      </div>
		    </header>
		    <main>
		      <div>
		        <strong>Fee: '.$event_amount.', <span style="background:green;">Paid: '.$paid_amount.'</span>, <span style="background:red;">Remain: '.$remain_amount.'</span></strong>
		      </div>
		      <br>
		      <br>
		      <table>
		       
		        <tbody>
		          	<tr>
			            <th>Name</th>
			            <th>:</th>
			            <th>'.$name.'</th>
		          	</tr>
		          	<tr>  
			            <th>Email</th>
			            <th>:</th>
			            <th>'.$email.'</th>
		          	</tr>
		           	<tr>  
			            <th>Date</th>
			            <th>:</th>
			            <th>'.$event_date.'</th>
		          	</tr>
		           	<tr>  
			            <th>Time OF Show</th>
			            <th>:</th>
			            <th>'.$show_time.'</th>
		          	</tr>
		           	<tr>  
			            <th>Duration</th>
			            <th>:</th>
			            <th>'.$duration.'</th>
		          	</tr>
		          	<tr>  
			            <th>Host</th>
			            <th>:</th>
			            <th>'.$host.'</th>
		          	</tr>
		          	<tr>  
			            <th>Type Of Show</th>
			            <th>:</th>
			            <th>'.$show_type.'</th>
		          	</tr>
		          	<tr>  
			            <th>How many party bags required</th>
			            <th>:</th>
			            <th>'.$party_bags.'</th>
		          	</tr>
		          	<tr>  
			            <th>Address OF Party</th>
			            <th>:</th>
			            <th>'.$party_address.'</th>
		          	</tr>
		          	<tr>  
			            <th>Home Address</th>
			            <th>:</th>
			            <th>'.$home_address.'</th>
		          	</tr>
		          	<tr>  
			            <th>Is the Party inside or outside</th>
			            <th>:</th>
			            <th>'.$party_in_out.'</th>
		          	</tr>
		          	<tr>  
			            <th>Rain contingency plans</th>
			            <th>:</th>
			            <th>'.$rain_plan.'</th>
		          	</tr>
		          	<tr>  
			            <th>Age OF Child</th>
			            <th>:</th>
			            <th>'.$age.'</th>
		          	</tr>
		          	<tr>  
			            <th>Date OF Birth</th>
			            <th>:</th>
			            <th>'.$dob.'</th>
		          	</tr>
		          	<tr>  
			            <th>Approximate number of children of party</th>
			            <th>:</th>
			            <th>'.$children_count.'</th>
		          	</tr>
		          	<tr>  
			            <th>Main Age of children at the party</th>
			            <th>:</th>
			            <th>'.$children_party.'</th>
		          	</tr>
		          	<tr>  
			            <th>Boys, girls or mixed party</th>
			            <th>:</th>
			            <th>'.$gender_party.'</th>
		          	</tr>
		          	<tr>  
			            <th>Has your child Seen my show before</th>
			            <th>:</th>
			            <th>'.$party_seen.'</th>
		          	</tr>
		          	<tr>  
			            <th>Mobile Number</th>
			            <th>:</th>
			            <th>'.$mobile_number.'</th>
		          	</tr>
		          	<tr>  
			            <th>2nd Mobile Number (required)</th>
			            <th>:</th>
			            <th>'.$second_mobile.'</th>
		          	</tr>
		        </tbody>
		        
		      </table>
		    </main>
		    
		  </body>
		</html>
		';


		echo $html;
		exit();
  	}
// Rakesh


public function downloadWord($id = NULL){

	    $this->checkLogin(); 

	    

	    $booking_details = $this->db->query("SELECT * FROM booking_request where id='$id' and booking_status='Ready Print'")->result_array();


// Get Performer Details .
// $assign_booking = $this->db->query("SELECT * FROM assign_booking where booking_request_id!='$id'")->result_array();
// foreach($assign_booking as $assigned){
// 	$performers_id=!empty($assigned['performer_id']) ? $assigned['performer_id'] : "";

// $assign_booking = $this->db->query("SELECT * FROM performers where id!='$performers_id'")->result_array();

// }
$performer_message = "";
					$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' ")->result_array();

					if(!empty($assign_details)){
						foreach ($assign_details as $assignList) {
							$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

							$performer_name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
							// $performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
							// $performer_mobile = !empty($performer_details[0]['mobile_a']) ? $performer_details[0]['mobile_a'] : "";

							
								$count_assign=count($assign_details);
								if($count_assign==1){
									$performer_message.=$performer_name;
								}else{
									$performer_message.=$performer_name.',';							
								}
								
							
							// $performer_message.='Performer email: '.$performer_email.'   ';
							// $performer_message.='Performer mobile: '.$performer_mobile.'   ';
						}
					}
// Get Performers Details End here .
 


    	$name = !empty($booking_details[0]['child_name']) ? $booking_details[0]['child_name'] : "";
    	$email = !empty($booking_details[0]['email']) ? $booking_details[0]['email'] : "";
    	$event_date = !empty($booking_details[0]['event_date']) ? $booking_details[0]['event_date'] : "";

    	$show_time = !empty($booking_details[0]['show_time']) ? $booking_details[0]['show_time'] : "";
    	$duration = !empty($booking_details[0]['duration']) ? $booking_details[0]['duration'] : "";
    	$host = !empty($booking_details[0]['name']) ? $booking_details[0]['name'] : "";
    	$fullname = !empty($booking_details[0]['show_fullname']) ? $booking_details[0]['show_fullname'] : "";

    	$event_amount = !empty($booking_details[0]['event_amount']) ? $booking_details[0]['event_amount'] : 0;
    	$paid_amount = !empty($booking_details[0]['paid_amount']) ? $booking_details[0]['paid_amount'] : 0;
    	$remain_amount = !empty($booking_details[0]['remain_amount']) ? $booking_details[0]['remain_amount'] : 0;

    	$party_bags = !empty($booking_details[0]['party_bags']) ? $booking_details[0]['party_bags'] : "";
    	$party_address = !empty($booking_details[0]['party_address']) ? $booking_details[0]['party_address'] : "";
    	$home_address = !empty($booking_details[0]['home_address']) ? $booking_details[0]['home_address'] : "";

    	$party_in_out = !empty($booking_details[0]['party_in_out']) ? $booking_details[0]['party_in_out'] : "";
    	$rain_plan = !empty($booking_details[0]['rain_plan']) ? $booking_details[0]['rain_plan'] : "";
    	$age = !empty($booking_details[0]['age']) ? $booking_details[0]['age'] : "";
    	$parking = !empty($booking_details[0]['parking_facility']) ? $booking_details[0]['parking_facility'] : "";

    	$dob = !empty($booking_details[0]['dob']) ? $booking_details[0]['dob'] : "";
    	$children_count = !empty($booking_details[0]['children_count']) ? $booking_details[0]['children_count'] : "";
    	$children_party = !empty($booking_details[0]['children_party']) ? $booking_details[0]['children_party'] : "";

    	$gender_party = !empty($booking_details[0]['gender_party']) ? $booking_details[0]['gender_party'] : "";
    	$party_seen = !empty($booking_details[0]['party_seen']) ? $booking_details[0]['party_seen'] : "";
    	$mobile_number = !empty($booking_details[0]['mobile_number']) ? $booking_details[0]['mobile_number'] : "";
    	$second_mobile = !empty($booking_details[0]['second_mobile']) ? $booking_details[0]['second_mobile'] : "";
    	$show_type_text = !empty($booking_details[0]['show_type_text']) ? $booking_details[0]['show_type_text'] : "";

    	$categoryID = !empty($booking_details[0]['show_type']) ? $booking_details[0]['show_type'] : "";

		$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
		$showArray = array();
		if(!empty($categoryDetails)){
			foreach ($categoryDetails as $categoryList) {

				$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
				$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
			}
		}

		$show_type = !empty($showArray) ? implode(',', $showArray) : "";

		$postData = array(
			'print_count'=> '1',
			'booking_status'=>'Feedback Pending',
			'updated_on'=> date("Y-m-d H:i:s")
		);

		$postWhere = array(
			'id=' => $id
		);

		$this->db->where($postWhere);
		$result = $this->db->update('booking_request',$postData);
		
		$postData = array(
			'booking_status'=>'Feedback Pending',
			'updated_on'=> date("Y-m-d H:i:s")
		);

		$postWhere = array(
			'booking_request_id' => $id
		);

		$this->db->where($postWhere);
		$result = $this->db->update('assign_booking',$postData);


$filename=$event_date.' '.$host;
		//$filename=str_replace(" "," ",$filename);
		header("Content-type: application/rtf");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Disposition: attachment;Filename="'.$filename.'.rtf"');
		$historyMessage = "Admin download pdf ".$name." booking request details";
		$resultHistory = $this->addHistory('Booking Request', 'Pdf', $historyMessage);
		$date=date('d/m/y');
	    ob_start();
	    
$pay =($paid_amount != $event_amount)?", Paid: \highlight1\b ".$paid_amount." \highlight0\b0\ ":", Paid:  \highlight2\b ".$paid_amount." \highlight0\b0\ ";
//Date: '.$date.' \par
$html.='{\rtf1\ansi\ansicpg1252\deff0{\fonttbl{\f0\fnil\fcharset0 Courier New;}}
{\colortbl ;\red255\green0\blue0;\red255\green0\blue255;\red255\green255\blue0;}
{\*\generator Msftedit 5.41.21.2510;}\viewkind4\uc1\pard\lang1033\f0\fs22\par
 \tab\tab\tab    \tab\tab    \cf1\b Super Steph Party\cf0\b0\par

Fee:  '.$event_amount.$pay .'\par
Name:\highlight3 '.$host.' \highlight0\par
Email:'.$email.'\par
Date: \highlight3 '.$event_date.' \highlight0\par
Time Of Show :\highlight3 '.$show_time.' \highlight0\par
Duration:'.$duration.' hour\par
Host:'.$performer_message.'\par
Type Of Show: \highlight3 '.$show_type_text.' \highlight0\par
How many party bags required:\highlight2 '.$party_bags.' \highlight0\par
Character:\highlight3 '.$show_type.' \highlight0\par
Address OF Party:\highlight3 '.$party_address.' \highlight0\par
Home Address:'.$home_address.'\par
Parking facilities (any paid parking is to be paid by the client): \highlight3 '.$parking.' \highlight0\par
Is the Party inside or outside:'.$party_in_out.'\par
Rain contingency plans:'.$rain_plan.'\par
Name of child: \highlight3 '.$name.' \highlight0\par
Age OF Child:\highlight3 '.$age.' \highlight0\par
Date OF Birth:'.$dob.'\par
Approximate number of children of party:'.$children_count.'\par
Main Age of children at the party:'.$children_party.'\par
Boys, girls or mixed party:'.$gender_party.'\par
Has your child Seen my show before:'.$party_seen.'\par
Mobile Number:\highlight3 '.$mobile_number.' \highlight0\par
\highlight0 2nd Mobile Number (required):\highlight3 '.$second_mobile.' \highlight0\par
\par
}';
		echo $html;
		exit();
  	}










//RAkesh chaturvedi end

  	public function alldownloadWord_veer($id = NULL){

  		$this->checkLogin(); 

  		header("Content-type: application/rtf");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Disposition: attachment;Filename=event_booking.rtf");

  		$filter = $id;
  		
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
			$fiveDay = date('Y-m-d', strtotime($currentDate . ' +7 day'));
			$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$fiveDay') ";
		}
		else{
			$upcomingFilter="";
		}

		$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status!='Cancelled' $upcomingFilter;")->result_array();
		
	    if(!empty($booking_details)){

	    	foreach ($booking_details as $booking_list) {
	    		
	    		$id = !empty($booking_list['id']) ? $booking_list['id'] : "";

	    		$name = !empty($booking_list['child_name']) ? $booking_list['child_name'] : "";
		    	$email = !empty($booking_list['email']) ? $booking_list['email'] : "";
		    	$event_date = !empty($booking_list['event_date']) ? $booking_list['event_date'] : "";

		    	$show_time = !empty($booking_list['show_time']) ? $booking_list['show_time'] : "";
		    	$duration = !empty($booking_list['duration']) ? $booking_list['duration'] : "";
		    	$host = !empty($booking_list['name']) ? $booking_list['name'] : "";

		    	$event_amount = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : 0;
		    	$paid_amount = !empty($booking_list['paid_amount']) ? $booking_list['paid_amount'] : 0;
		    	$remain_amount = !empty($booking_list['remain_amount']) ? $booking_list['remain_amount'] : 0;

		    	$party_bags = !empty($booking_list['party_bags']) ? $booking_list['party_bags'] : "";
		    	$party_address = !empty($booking_list['party_address']) ? $booking_list['party_address'] : "";
		    	$home_address = !empty($booking_list['home_address']) ? $booking_list['home_address'] : "";

		    	$party_in_out = !empty($booking_list['party_in_out']) ? $booking_list['party_in_out'] : "";
		    	$rain_plan = !empty($booking_list['rain_plan']) ? $booking_list['rain_plan'] : "";
		    	$age = !empty($booking_list['age']) ? $booking_list['age'] : "";

		    	$dob = !empty($booking_list['dob']) ? $booking_list['dob'] : "";
		    	$children_count = !empty($booking_list['children_count']) ? $booking_list['children_count'] : "";
		    	$children_party = !empty($booking_list['children_party']) ? $booking_list['children_party'] : "";
		    //	$show_type_text = !empty($booking_list['show_type_text']) ? $booking_list['children_party'] : "";

		    	$gender_party = !empty($booking_list['gender_party']) ? $booking_list['gender_party'] : "";
		    	$party_seen = !empty($booking_list['party_seen']) ? $booking_list['party_seen'] : "";
		    	$mobile_number = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		    	$second_mobile = !empty($booking_list['second_mobile']) ? $booking_list['second_mobile'] : "";

		    	$categoryID = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";

		    	$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
				$showArray = array();
				if(!empty($categoryDetails)){
					foreach ($categoryDetails as $categoryList) {

						$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
						$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
					}
				}

				$show_type = !empty($showArray) ? implode(',', $showArray) : "";

				$postData = array(
					'print_count'=> '1',
					'updated_on'=> date("Y-m-d H:i:s")
				);

				$postWhere = array(
					'id=' => $id
				);

				$this->db->where($postWhere);
				$result = $this->db->update('booking_request',$postData);

				$historyMessage = "Admin download pdf ".$name." booking request details";
				$resultHistory = $this->addHistory('Booking Request', 'Pdf', $historyMessage);

			    $html .= '
				   		   Super Steph Party
 Date: '.$date.'
Fee: '.$event_amount.', Paid: '.$paid_amount.',Remain: '.$remain_amount.'
Name:'.$name.'
Email:'.$email.',
Date:'.$event_date.'
Time Of Show :'.$show_time.'
Duration:'.$duration.'
Host:'.$host.'
Type Of Show:'.$show_type.'
How many party bags required:'.$party_bags.'
Address OF Party:'.$party_address.'
Home Address:'.$home_address.'
Is the Party inside or outside:'.$party_in_out.'
Rain contingency plans:'.$rain_plan.'
Age OF Child:'.$age.'
Date OF Birth:'.$dob.'
Approximate number of children of party:'.$children_count.'
Main Age of children at the party:'.$children_party.'
Boys, girls or mixed party:'.$gender_party.'
Has your child Seen my show before:'.$party_seen.'
Mobile Number:'.$mobile_number.'
Super Steph Party2nd Mobile Number (required):'.$second_mobile.'
		';

				
	    	}

	    	echo $html;

	    }
  	}
  	// New Function


  	public function alldownloadWord($id = NULL){

  		$this->checkLogin(); 


  		$filter = $id;
  		
  		if($filter == '1'){
			$currentDate = date("Y-m-d");
			$oneDay = date('Y-m-d', strtotime($currentDate . ' +1 day'));
			$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$oneDay') ";
			$time=$currentDate.' to '.$oneDay;
		}
		else if($filter == '3'){
			$currentDate = date("Y-m-d");
			$threeDay = date('Y-m-d', strtotime($currentDate . ' +3 day'));
			$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$threeDay') ";
			$time=$currentDate.' to '.$threeDay;
		}
		else if($filter == '7'){
			$currentDate = date("Y-m-d");
			$fiveDay = date('Y-m-d', strtotime($currentDate . ' +7 day'));
			$upcomingFilter = "and (event_date>='$currentDate' and event_date<='$fiveDay') ";
			$time=$currentDate.' to '.$fiveDay;
		}
		else {
			$upcomingFilter="";
			$time='All Gigs';
		}

		$booking_details = $this->db->query("SELECT * FROM booking_request where booking_status!='Cancelled' and booking_status='Ready Print' $upcomingFilter;")->result_array();
		$html.='{\rtf1\ansi\ansicpg1252\deff0{\fonttbl{\f0\fnil\fcharset0 Courier New;}}
{\colortbl ;\red255\green0\blue0;\red255\green0\blue255;\red255\green255\blue0;}
{\*\generator Msftedit 5.41.21.2510;}\viewkind4\uc1\pard\lang1033\f0\fs22\par';
	    if(!empty($booking_details)){

	    	foreach ($booking_details as $booking_list) {
	    		
	    		$id = !empty($booking_list['id']) ? $booking_list['id'] : "";

// Get Performer Details .
// $assign_booking = $this->db->query("SELECT * FROM assign_booking where booking_request_id!='$id'")->result_array();
// foreach($assign_booking as $assigned){
// 	$performers_id=!empty($assigned['performer_id']) ? $assigned['performer_id'] : "";

// $assign_booking = $this->db->query("SELECT * FROM performers where id!='$performers_id'")->result_array();

// }
$performer_message = "";
$jonty=1;
					$assign_details = $this->db->query("SELECT * FROM assign_booking where booking_request_id='$id' ")->result_array();

					if(!empty($assign_details)){
						foreach ($assign_details as $assignList) {
							$performer_id = !empty($assignList['performer_id']) ? $assignList['performer_id'] : "";

							$performer_details = $this->db->query("SELECT * FROM performers where id='$performer_id' ")->result_array();

							$performer_name = !empty($performer_details[0]['name']) ? $performer_details[0]['name'] : "";
							// $performer_email = !empty($performer_details[0]['email']) ? $performer_details[0]['email'] : "";
							// $performer_mobile = !empty($performer_details[0]['mobile_a']) ? $performer_details[0]['mobile_a'] : "";
								$count_assign=count($assign_details);
								if($count_assign==1){
									$performer_message.=$performer_name;
								}else{
									$performer_message.=$performer_name.',';							
								}
								
							 
							// $performer_message.='Performer email: '.$performer_email.'   ';
							// $performer_message.='Performer mobile: '.$performer_mobile.'   ';
						$jonty++;
					}

					}
// Get Performers Details End here .

	    		$name = !empty($booking_list['child_name']) ? $booking_list['child_name'] : "";
		    	$email = !empty($booking_list['email']) ? $booking_list['email'] : "";
		    	$event_date = !empty($booking_list['event_date']) ? $booking_list['event_date'] : "";

		    	$show_time = !empty($booking_list['show_time']) ? $booking_list['show_time'] : "";
		    	$duration = !empty($booking_list['duration']) ? $booking_list['duration'] : "";
		    	$host = !empty($booking_list['name']) ? $booking_list['name'] : "";
		    	$fullname = !empty($booking_list['show_fullname']) ? $booking_list['show_fullname'] : "";
//show_fullname
		    	$event_amount = !empty($booking_list['event_amount']) ? $booking_list['event_amount'] : 0;
		    	$paid_amount = !empty($booking_list['paid_amount']) ? $booking_list['paid_amount'] : 0;
		    	$remain_amount = !empty($booking_list['remain_amount']) ? $booking_list['remain_amount'] : 0;

		    	$party_bags = !empty($booking_list['party_bags']) ? $booking_list['party_bags'] : "";
		    	$party_address = !empty($booking_list['party_address']) ? $booking_list['party_address'] : "";
		    	$home_address = !empty($booking_list['home_address']) ? $booking_list['home_address'] : "";

		    	$party_in_out = !empty($booking_list['party_in_out']) ? $booking_list['party_in_out'] : "";
		    	$rain_plan = !empty($booking_list['rain_plan']) ? $booking_list['rain_plan'] : "";
		    	$age = !empty($booking_list['age']) ? $booking_list['age'] : "";

		    	$dob = !empty($booking_list['dob']) ? $booking_list['dob'] : "";
		    	$children_count = !empty($booking_list['children_count']) ? $booking_list['children_count'] : "";
		    	$children_party = !empty($booking_list['children_party']) ? $booking_list['children_party'] : "";

		    	$gender_party = !empty($booking_list['gender_party']) ? $booking_list['gender_party'] : "";
		    	$party_seen = !empty($booking_list['party_seen']) ? $booking_list['party_seen'] : "";
		    	$mobile_number = !empty($booking_list['mobile_number']) ? $booking_list['mobile_number'] : "";
		    	$second_mobile = !empty($booking_list['second_mobile']) ? $booking_list['second_mobile'] : "";
				$parking_facility = !empty($booking_list['parking_facility']) ? $booking_list['parking_facility'] : "";

		    	//parking_facility
		    	$categoryID = !empty($booking_list['show_type']) ? $booking_list['show_type'] : "";

		    	$categoryDetails = !empty($categoryID) ? explode(',', $categoryID) : "";
				$showArray = array();
				if(!empty($categoryDetails)){
					foreach ($categoryDetails as $categoryList) {

						$category_details = $this->db->query("SELECT * FROM categories where id='$categoryList';")->result_array();
						$showArray[] = !empty($category_details[0]['category']) ? $category_details[0]['category'] : "";
					}
				}

				$show_type = !empty($showArray) ? implode(',', $showArray) : "";

				$postData = array(
					'print_count'=> '1',
					'updated_on'=> date("Y-m-d H:i:s")
				);

				$postWhere = array(
					'id=' => $id
				);

				$this->db->where($postWhere);
				$result = $this->db->update('booking_request',$postData);
				if(!empty($time)){
					$filename=$time;
				}else{
				$filename=$event_date.' '.$host;
			}
			//$filename=str_replace(" ","_",$filename);
				  		header("Content-type: application/rtf");
						header("Expires: 0");
						header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
						header('Content-Disposition: attachment;Filename="'.$filename.'.rtf"');


	


				$historyMessage = "Admin download RTF ".$name." booking request details";
				$resultHistory = $this->addHistory('Booking Request', 'RTF', $historyMessage);

$pay =($paid_amount != $event_amount)?", Paid: \highlight1\b ".$paid_amount."\highlight0\b0\ ":", Paid:  \highlight2\b ".$paid_amount."\highlight0\b0\ ";
// $rem =($remain_amount != 0)?", Remain: ".$remain_amount."":""; Date:'.$date.' \par
$html .='
 \tab\tab\tab    \tab\tab    \cf1\b Super Steph Party\cf0\b0\par

Fee: '.$event_amount.$pay.'\par
Name:\highlight3 '.$host.'\highlight0\par
Email:'.$email.',\par
Date:\highlight3 '.$event_date.' \highlight0  \par
Time Of Show :\highlight3 '.$show_time.' \highlight0\par
Duration:'.$duration.'\par
Host:'.$performer_message.'\par
Type Of Show:  \highlight3 '.$show_type_text.' \highlight0\par
How many party bags required:\highlight2 '.$party_bags.' \highlight0\par
Character:\highlight3 '.$show_type.' \highlight0\par
Address OF Party:\highlight3 '.$party_address.' \highlight0\par
Parking facilities (any paid parking is to be paid by the client):\highlight3 '.$parking_facility.' \highlight0\par
Home Address:'.$home_address.'\par
Is the Party inside or outside:'.$party_in_out.'\par
Rain contingency plans:'.$rain_plan.'\par 
Name OF Child:\highlight3 '.$name.'  \highlight0\par
Age OF Child:\highlight3 '.$age.' \highlight0\par
Date OF Birth:'.$dob.'\par
Approximate number of children of party:'.$children_count.'\par
Main Age of children at the party:'.$children_party.'\par
Boys, girls or mixed party:'.$gender_party.'\par
Has your child Seen my show before:'.$party_seen.'\par
Mobile Number:\highlight3 '.$mobile_number.' \highlight0\par
2nd Mobile Number (required):\highlight3 '.$second_mobile.' \highlight0\par\page
';				
	    	}
$html.='}';
	    	echo $html;

	    }
  	}


  	//new Functions



  	public function history(){

	    $this->load->view('header');
	    $this->load->view('history');
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
		        $history_total = $this->db->query("SELECT count(*) as total FROM histories where type like '%$searchdata%' order by id desc ")->result_array();
		        $total = !empty($history_total[0]['total']) ? $history_total[0]['total'] : 0;
	      	}
	      	else{
		        $history_total = $this->db->query("SELECT count(*) as total FROM histories order by id desc;")->result_array();
		        $total = !empty($history_total[0]['total']) ? $history_total[0]['total'] : 0;
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

	      	if(!empty($searchdata)){
	      		$history_details = $this->db->query("SELECT * FROM histories where (type like '%$searchdata%') order by id desc $limitto;")->result_array();
	      	}
	      	else{
	      		$history_details = $this->db->query("SELECT * FROM histories order by id desc $limitto;")->result_array();
	      	}
      
	      	if(!empty($history_details)){
		        foreach($history_details  as $history_list){
		          
		          	$temp['id'] = !empty($history_list['id']) ? $history_list['id'] : "";
		          	$temp['type'] = !empty($history_list['type']) ? $history_list['type'] : "";
		          	$temp['subtype'] = !empty($history_list['subtype']) ? $history_list['subtype'] : "";
		          	$temp['message'] = !empty($history_list['message']) ? $history_list['message'] : "";
		          	$temp['date'] = !empty($history_list['date']) ? $history_list['date'] : "";
		          	
		          	$response[] =$temp;
		        }
	      	}
      
	      	$response[]=array(
	        	'total' => $total
	      	);
	    
	      	if(!empty($response)){
	        	echo json_encode(array('status'=>0, 'type'=>'success', "message" => "History List!", 'response' =>$response)); die;
	      	}
	      	else{
	        	echo json_encode(array('status'=>1, 'type'=>'error', "message" => "Empty History List!", 'response' =>$response)); die;
	      	}
	    }
  	}

 
 	public function logout(){

	    $ownerdata = array( 
	      'id'  => '',
	      'name'  => '',
	      'email'  => ''
	    );  
	    $session = $this->session->unset_userdata($ownerdata);
	    if(empty($session['id'])){

	    	$resultHistory = $this->addHistory('Login', '', 'Admin logout successfully!');
	    	$this->session->sess_destroy();	
	      	redirect('welcome/login', 'refresh');
	    }
 	}  

 	public function termsandconditions(){
 		 
	    $this->load->view('termsandconditions');
	   

 	}
	
}

