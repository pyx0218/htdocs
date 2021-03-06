<?php
class Reviews extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
	$this->load->model('books_model');
	$this->load->model('reviews_model');
  }
  
  public function new_review($isbn){
    $this->load->helper('form');
	$this->load->library('form_validation');
    $this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[100]');
    $this->form_validation->set_rules('rating', 'Rating', 'required');
	$this->form_validation->set_rules('content', 'Content', 'trim|required|min_length[10]|max_length[2000]');
	
	if($this->session->userdata('logged_in')){
		$user_data = $this->session->all_userdata();
		$books_item = $this->books_model->get_book($isbn);
		$this->load->view('templates/header', array('title'=>'Write a Review')); 
		$this->load->view('templates/navigation_view',$user_data);
		$this->load->view('reviews/create', $books_item);
		$this->load->view('templates/footer');
	}
	else{
		redirect('users/login');
	}
  }
  
  public function create(){
	$this->load->helper('form');
	$this->load->library('form_validation');
    $this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[100]');
    $this->form_validation->set_rules('rating', 'Rating', 'required');
	$this->form_validation->set_rules('content', 'Content', 'trim|required|min_length[10]|max_length[2000]');

	if($this->session->userdata('logged_in')){
		$isbn = $this->input->post('isbn');
		if($this->input->post('submit')=='Submit'){
			if($this->form_validation->run()===FALSE){
				$books_item = $this->books_model->get_book(strip_quotes($isbn));
				$user_data = $this->session->all_userdata();
				$this->load->view('templates/header', array('title'=>'Write a Review'));
				$this->load->view('templates/navigation_view',$user_data);
				$this->load->view('reviews/create', $books_item);
				$this->load->view('templates/footer');
			}
			else{
				$rid = $this->reviews_model->add_review();
				redirect('reviews/view/'.$rid);
			}
		}
		elseif($this->input->post('cancel')=='Return') {
			redirect('books/view/'.$isbn);
		}
	}
	else{
		redirect('users/login');
	}
	
  }
  
  public function view($rid)
  {
	  $review_item = $this->reviews_model->get_review($rid);
	  
	  if (empty($review_item) or ($review_item['VISIBILITY']==0 and !$this->session->userdata('admin')))
		show_404();
	  $user_data = $this->session->all_userdata();
	  $data=$user_data;
	  $data['review_item'] = $review_item;
	  if(!empty($user_data['logged_in'])){
		  if($data['user_id'] == $review_item['USER_ID'])
			$data['is_self'] = true;
		  else
			$data['is_self'] = false;
	  }
	  $user_data = $this->session->all_userdata();
	  $this->load->view('templates/header', array('title'=>$review_item['RTITLE'].' (Review: '.$review_item['BNAME'].')'));
	  $this->load->view('templates/navigation_view',$user_data);
	  $this->load->view('reviews/view', $data);
	  $this->load->view('templates/footer'); 
  }
  
  public function edit($rid){
    $this->load->helper('form');
	$this->load->library('form_validation');
    $this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[100]');
    $this->form_validation->set_rules('rating', 'Rating', 'required');
	$this->form_validation->set_rules('content', 'Content', 'trim|required|min_length[10]|max_length[2000]');
	
	if($this->session->userdata('logged_in')){
		$review_item = $this->reviews_model->get_review($rid);
		$user_data = $this->session->all_userdata();
		$this->load->view('templates/header', array('title'=>'Edit a Review'));
		$this->load->view('templates/navigation_view',$user_data); 
		$this->load->view('reviews/edit', $review_item);
		$this->load->view('templates/footer');
	}
	else{
		redirect('users/login');
	}
  }
  
  public function update(){
	$this->load->helper('form');
	$this->load->library('form_validation');
    $this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[100]');
    $this->form_validation->set_rules('rating', 'Rating', 'required');
	$this->form_validation->set_rules('content', 'Content', 'trim|required|min_length[10]|max_length[2000]');

	if($this->session->userdata('logged_in')){
		$rid = $this->input->post('rid');
		if($this->input->post('submit')=='Save'){
			$review_item = $this->reviews_model->get_review(strip_quotes($rid));
			if($this->form_validation->run()===FALSE){
				$user_data = $this->session->all_userdata();
				$this->load->view('templates/header', array('title'=>'Edit a Review'));
				$this->load->view('templates/navigation_view',$user_data); 
				$this->load->view('reviews/edit', $review_item);
				$this->load->view('templates/footer');
			}
			else{
				$this->reviews_model->update_review();
				redirect('reviews/view/'.$rid);
			}
		}
		elseif($this->input->post('cancel')=='Cancel') {
			redirect('reviews/view/'.$rid);
		}
	}
	else{
		redirect('users/login');
	}
  }
  
  public function delete($rid){
	  if($this->session->userdata('logged_in')){
		$this->reviews_model->delete_review($rid);
		redirect('users/view/'.$this->session->userdata('user_id'));
	  }
	  else{
		redirect('users/login');
	  }
  }
  
  public function shield($rid){
	$this->load->helper('form');
	$this->load->library('form_validation');
	$this->form_validation->set_rules('content', 'Content', 'trim|required|max_length[500]');
		if($this->form_validation->run()===FALSE){
			$review_item = $this->reviews_model->get_review($rid);
			$data = $this->session->all_userdata();
			$data['review_item'] = $review_item;
			$this->load->view('templates/header', array('title'=>'Shield a Review'));
			$this->load->view('templates/navigation_view',$data);
			$this->load->view('reviews/shield_view', $data);
			$this->load->view('templates/footer');
		}
		else{
				$review_item = $this->reviews_model->get_review($rid);
				$data = $this->session->all_userdata();
				$data['review_item'] = $review_item;
				
				$this->reviews_model->shield_review($data);
				redirect('users/view/'.$data['user_id']);
		}
  }
  
  public function restore($rid){
	$this->load->helper('form');
	$this->load->library('form_validation');
	$this->form_validation->set_rules('content', 'Content', 'trim|required|max_length[500]');
	if($this->form_validation->run()===FALSE){
		$review_item = $this->reviews_model->get_review($rid);
		$data = $this->session->all_userdata();
		$data['review_item'] = $review_item;
		$this->load->view('templates/header', array('title'=>'Restore a Review'));
		$this->load->view('templates/navigation_view',$data);
		$this->load->view('reviews/restore_view', $data);
		$this->load->view('templates/footer');
	}
	else{
			$review_item = $this->reviews_model->get_review($rid);
			$data = $this->session->all_userdata();
			$data['review_item'] = $review_item;
			
			$this->reviews_model->restore_review($data);
			redirect('users/view/'.$data['user_id']);
	}
  }
  
}