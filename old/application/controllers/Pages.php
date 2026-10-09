<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->view('pages/header');
		$this->load->view('pages/slider');
		$this->load->view('pages/main');
		$this->load->view('pages/footer');
	}
	public function view()
	{
		$page_name = $this->uri->segment('1');
		if ($page_name == '') {
			redirect(base_url());
		} else {
			if (file_exists(realpath(APPPATH . '/views/pages/' . $page_name . ".php"))) {
				$this->load->view('pages/header');
				$this->load->view('pages/' . $page_name);
				$this->load->view('pages/footer');
			} else {
				$this->load->view('pages/page-404');
			}

		}
	}
	public function admin()
	{
		$this->load->view('login');
	}
	public function enquiry()
	{
		$this->form_validation->set_rules('e_name', 'Name', 'required|trim|alpha_numeric_spaces|trim');
		$this->form_validation->set_rules('e_mobile', 'Mobile No.', 'trim|required|numeric|min_length[10]|max_length[10]');
		$this->form_validation->set_rules('e_email', 'Email', 'trim|valid_email');
		// $this->form_validation->set_rules('e_purpose', 'Purpose', 'trim|alpha_numeric_spaces|trim');
		$this->form_validation->set_rules('e_question', 'Message', 'required|trim|alpha_numeric_spaces|trim');
		$this->form_validation->set_error_delimiters('<div class="text-danger"><small>', '</small></div>');
		if ($this->form_validation->run() == false) {
			$data['success'] = false;
			$data['error'] = validation_errors();
			$this->load->view('pages/header');
			$this->load->view('pages/contact', $data);
			$this->load->view('pages/footer');
		} else {
			$data = $this->input->post();
			extract($data);
			$this->load->model('Offer_model');
			$res = $this->Offer_model->save_data('student', $data);
			$inst_name = config('sc_name');
			$inst_email = config('sc_email');

			$inst_cont = str_replace(' ', '', config('sc_contact1'));
			$inst_mobile = substr($inst_cont, -10);
			$reply_message = "Thanks for your interest. We'll contact you soon. \nRegards, \n" . $inst_name;
			$inst_message = "New enquiry generated from " . $e_name . " with mobile no. " . $e_mobile . " and message is " . $e_question . " at " . $inst_name;
			$all_data = post_clean($this->input->post());
			$res = $this->Offer_model->save_data('enquiry', $all_data);

			if ($res) {
				echo sendsms($e_mobile, $reply_message);
				//sendsms($inst_mobile,$inst_message);
				$reply_message = 'Dear' . $e_name . ' Thanks for your interest. We shall contact you soon.';
				mail($inst_email, 'New Enquiry', $inst_message);
				mail($e_email, 'Enquiry', $reply_message);

				$this->session->set_flashdata('success_message', 'Enquiry Submitted Successfully.');
				redirect(current_url());
				//redirect(base_url().'index.php/pages/view/contact');
			} else {

				$data = array('error_message' => 'An Error Occurred. Please Try Again');
				$this->load->view('pages/header');
				$this->load->view('pages/contact', $data);
				$this->load->view('pages/footer');
			}
		}
	}
}

?>