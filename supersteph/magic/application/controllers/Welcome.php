<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('header');
		$this->load->view('index');
		$this->load->view('footer');
	}

	public function addbooking()
	{
		$this->load->view('header');
		$this->load->view('addbooking');
		$this->load->view('footer');
	}

	public function viewbooking()
	{
		$this->load->view('header');
		$this->load->view('viewbooking');
		$this->load->view('footer');
	}

	public function deletebooking()
	{
		$this->load->view('header');
		$this->load->view('deletebooking');
		$this->load->view('footer');
	}

	public function adduser()
	{
		$this->load->view('header');
		$this->load->view('adduser');
		$this->load->view('footer');
	}


	public function viewuser()
	{
		$this->load->view('header');
		$this->load->view('viewuser');
		$this->load->view('footer');
	}

	public function deleteuser()
	{
		$this->load->view('header');
		$this->load->view('deleteuser');
		$this->load->view('footer');
	}	

	public function requestlist()
	{
		$this->load->view('header');
		$this->load->view('requestlist');
		$this->load->view('footer');
	}

	public function acceptrequest()
	{
		$this->load->view('header');
		$this->load->view('acceptrequest');
		$this->load->view('footer');
	}	

	public function cancelrequest()
	{
		$this->load->view('header');
		$this->load->view('cancelrequest');
		$this->load->view('footer');
	}

	public function feedback()
	{
		$this->load->view('header');
		$this->load->view('feedback');
		$this->load->view('footer');
	}






	
}

