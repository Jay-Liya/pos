<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct(){

        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->model('Product_model');
        $this->load->helper('html');
        $this->load->library('form_validation');
   }

   function index(){

       if(isset($_REQUEST['btnSubmit'])){

           $this->form_validation->set_rules('txtusername', 'Username', 'trim|required|xss_clean');
           $this->form_validation->set_rules('txtpassword', 'Password', 'trim|required|xss_clean|callback_check_password');

           if($this->form_validation->run() == FALSE){

                $this->load->view('header');
                $this->load->view('login');
                $this->load->view('footer');

           }else{

               $this->session->set_userdata('logged_in',array('username' => 'admin'));
                redirect('pos');
           }

       }else{

           $this->load->view('header');
           $this->load->view('login');
           $this->load->view('footer');
       }

   }

   function check_password($txtpassword){

       $username=$this->input->post('txtusername');
       $check=$this->Product_model->checkLogin($username,$txtpassword);

       if($check==false){

           $this->form_validation->set_message('check_password', 'Invalid username or password');
           return false;
           
       }else{
           $this->session->set_userdata('username', $username);
           return true;
       }
   }
}
