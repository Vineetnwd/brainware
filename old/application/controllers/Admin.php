<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	public $token;
	public $user;
	public $type;
	public $branch;
	public $code;
	public $uri_status;
	public $br_id;
	public $today;
	public function __construct()
	{
		parent::__construct();
		$session_token = $this->session->userdata('token_id');
		$this->token = $session_token;
		$user = $this->user = $this->token['login_id'];
		$type = $this->type = $this->token['user_type'];
		$today = date('d-m-Y');
	}
	public function check_input()
	{
		$this->form_validation->set_rules('user_mobile', 'Mobile  Nnumber', 'trim|required|min_length[5]');
		$this->form_validation->set_rules('user_password', 'Password', 'trim|required|min_length[5]');
		$this->form_validation->set_error_delimiters('<div class="text-danger"><small>', '</small></div>');
		if($this->form_validation->run()==false)
		{	
			$data['success'] = false;
			$data['error'] = validation_errors();
			$this->load->view('login');
		}
		else	
		{
		
		$res = $this->Offer_model->login();
		
		if($res==1)
		{	
			redirect( base_url().'dashboard');
		}
		else if($res==2)
		{
			$data = array('error_message'=>'Sorry! Your ID has been blocked. ');
			$this->load->view('login',$data);
		}
			else
			{
				
			$data = array('error_message'=>'Invalid User Id & Password');
			$this->load->view('login',$data);
			}
		}
	}
	public function index()
	{
		$this->load->view('login');
	}
	public function dashboard()
	{
		$this->load->view('admin/dashboard');
	}
	public function center()
	{	
		$this->load->view('admin/center');
	}
		
	public function user()
	{	
	$user_type=$this->type;
		if($user_type=='ADMIN')
		{	
			$this->load->view('admin/user');
		}
		else 
		{
			show_404();
		}
	}
	public function module()
	{	
	$user_type=$this->type;
		if($user_type=='ADMIN')
		{	
			$this->load->view('admin/course_module');
		}
		else 
		{
			show_404();
		}
	}
	public function course()
	{	
	$user_type=$this->type;
		if($user_type=='ADMIN')
		{	
			$this->load->view('admin/course');
		}
		else 
		{
			show_404();
		}
	}
	public function student()
	{	
	$user_type=$this->type;
		if($user_type=='ADMIN' or $user_type=='CLIENT')
		{	
			$this->load->view('admin/student');
		}
		else 
		{
			show_404();
		}
	}
	public function certificate()
	{	
	$user_type=$this->type;
		if($user_type=='ADMIN')
		{	
			$this->load->view('admin/certificate');
		}
		else 
		{
			show_404();
		}
	}
	public function results()
	{	
	$user_type=$this->type;
		if($user_type=='ADMIN')
		{	
			$this->load->view('admin/result');
		}
		else 
		{
			show_404();
		}
	}
	public function admin_gallery()
	{	
	$user_type=$this->type;
		if($user_type=='ADMIN')
		{	
			$this->load->view('admin/gallery');
		}
		else 
		{
			show_404();
		}
	}
	public function enquiry()
	{	
	$user_type=$this->type;
		if($user_type=='ADMIN')
		{	
			$this->load->view('admin/enquiry');
		}
		else 
		{
			show_404();
		}
	}
	
	public function logout() 
	{
			$token = $this->session->userdata('token_id');
			$id = $token['login_id'];
			$data = array('status'=>'LOGOUT','login_token'=>'');
			$this->Offer_model->update_data('branch',$data,$id);
            $this->session->unset_userdata('login_token');
            $this->session->sess_destroy();
            redirect(site_url().'admin');
    }
	
	
}
