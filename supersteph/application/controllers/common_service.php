<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Commonservice extends CI_Controller {

  public function randomPassword() {
    $alphabet = "0123456789";
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i <= 6; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);  
  }     

  // public function sendMail($from_email='', $to_email='', $message='', $subject='') {

  //   $this->load->library('email');
  //   $config['protocol']='smtp';
  //   $config['smtp_host']='ssl://smtp.googlemail.com';
  //   $config['smtp_port']='465';
  //   $config['smtp_timeout']='30';
  //   $config['smtp_user']='beemteam786@gmail.com';
  //   $config['smtp_pass']='beem1234';
  //   $config['charset']='utf-8';
  //   $config['newline']="\r\n";
  //   $config['wordwrap'] = TRUE;
  //   $config['mailtype'] = 'html';

  //   $this->email->initialize($config);

  //   $this->email->from($from_email, 'CodeIgniter');
  //   $this->email->to($to_email);
  //   $this->email->subject($subject);
  //   $this->email->message($message);

  //   $result = $this->email->send();
     
  //   return $result;
  // }

  public function uploadImage($inputName,$uploads_dir,$max_width_thumb,$max_height_thumb){
    
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
  

  public function uploadMultipleImage($inputName,$uploads_dir){
    
    $imageArray = array();
    if(count($inputName['name']) > 0){
      //Loop through each file
      for($i=0; $i<count($inputName['name']); $i++) {
        //Get the temp file path
        $tmpFilePath = $inputName['tmp_name'][$i]['business_image']; 
        
        //Make sure we have a filepath
        if($tmpFilePath != ""){
        
          //save the filename
          $newImgName = date('d-m-Y H:i:s').'-'.$inputName['name'][$i]['business_image']; 

          //save the url and the file
          $imagePath = $uploads_dir.$newImgName;      
          
          $result_image = move_uploaded_file($tmpFilePath, $imagePath);
          
          //Upload the file into the temp dir
          if(!empty($result_image)) {
            $imageArray[] = $newImgName; 
            $imageData = implode(",", $imageArray);
          }
        }
      }
      return $imageData; 
    }
  }
        
  
     
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */