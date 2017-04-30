<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MX_Controller {

	
	public function __construct()
    {
        parent::__construct();
		$this->load->model('login_model');
		$this->load->library(array('session','form_validation'));
		$this->load->helper(array('url','form'));
		$this->load->database('default');
    }
	
	public function index()
	{	
		switch ($this->session->userdata('cargo')) {
			case '':
				$data['token'] = $this->token();
				$data['titulo'] = 'Venta de Pasajes :: BETA';
				$this->load->view('login',$data);
				break;
			case 'Administrador':
				redirect(base_url().'admin/principal');
				break;
			case 'TrabajadorX':
				redirect(base_url().'pasajes/principal');
				break;	
			case 'Trabajador':
				redirect(base_url().'encomiendas/principal');
				break;
			default:		
				$data['titulo'] = 'Login con roles de usuario en codeigniter';
				$this->load->view('login',$data);
				break;		
		}
	}
	public function process()
	{
		if($this->input->post('token') && $this->input->post('token') == $this->session->userdata('token'))
		{
            $this->form_validation->set_rules('username', 'nombre de usuario', 'required|trim|min_length[2]|max_length[150]|xss_clean');
            $this->form_validation->set_rules('password', 'password', 'required|trim|min_length[6]|max_length[150]|xss_clean');
 
            //lanzamos mensajes de error si es que los hay
            $this->form_validation->set_message('required', 'El %s es requerido');
            $this->form_validation->set_message('min_length', 'El %s debe tener al menos %s carácteres');
            $this->form_validation->set_message('max_length', 'El %s debe tener al menos %s carácteres');
            
			if($this->form_validation->run() == FALSE)
			{
				$this->index();
			}else{
				$username = $this->input->post('username');
				$password = $this->input->post('password');
				$check_user = $this->login_model->login_user($username,$password);
				if($check_user == TRUE)
				{
					$data = array(
	                'is_logued_in' 	=> 		TRUE,
	                'id_usuario' 	=> 		$check_user->idUsuario,
	                'cargo'		=>		$check_user->cargo,
	                'username' 		=> 		$check_user->username,
	                'dni' 		=> 		$check_user->NroDoc
            		);		
					$this->session->set_userdata($data);
					$this->index();
				}
			}
		}else{
			redirect(base_url().'login');
		}
	}

	public function token()
	{
		$token = md5(uniqid(rand(),true));
		$this->session->set_userdata('token',$token);
		return $token;
	}
	
	public function logout_ci()
	{
		$this->session->sess_destroy();
		$this->index();
	}
}
