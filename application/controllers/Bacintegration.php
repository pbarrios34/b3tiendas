<?php
require_once ("Secure_area.php");
class Bacintegration extends Secure_area 
{

	function __construct()
    {
		parent::__construct();
        $this->load->model('Bac');
    }
	//$register_id = $this->Employee->get_logged_in_employee_current_register_id();
	//$location_id = $this->Employee->get_logged_in_employee_current_location_id();
	//$employee_id = $this->Employee->get_logged_in_employee_info()->person_id; 

	//var_dump();
		// die();
		// foreach ($results as $row) {
		// 	echo $row['title'];
		// 	echo $row['name'];
		// 	echo $row['email'];
		// }
		// $this->db->select('id, name, emv_terminal_id', false);
		// $this->db->from('registers');
		// $this->db->where('id', $register_id);
		// $query = $this->db->get();
		// var_dump($query);
		// die();
		// $register = $query->row();

	
	function index()
	{
		$register = $this->Bac->getRegister();
		$location = $this->Bac->getLocation();
		$data = [
			'register' => $register,
			'location' => $location
		];
		
		$this->load->view('bac/manage',$data);
	}

    function sale()
	{
		$register = $this->Bac->getRegister();
		$location = $this->Bac->getLocation();
		$data = [
			'register' => $register,
			'location' => $location
		];
		
		$this->load->view('bac/sale', $data);
	}

    function refund()
	{
		$register = $this->Bac->getRegister();
		$location = $this->Bac->getLocation();
		$data = [
			'register' => $register,
			'location' => $location
		];
		
		$this->load->view('bac/refund',$data);
	}

    function batch($dev = '')
	{
		$endpoint = $this->Bac->getProdEndpoint();
		if ($dev == 'dev')
		{
			$endpoint = $this->Bac->getDevEndpoint();
		}
		$register = $this->Bac->getRegister();
		$location = $this->Bac->getLocation();
		$data = [
			'register' => $register,
			'location' => $location,
			'endpoint' => $endpoint,
			'dev'      => $dev,
		];
		
		$this->load->view('bac/batch',$data);
	}

    function batch_settlement($offset=0)
	{
		$register = $this->Bac->getRegister();
		$location = $this->Bac->getLocation();
		$data = [
			'register' => $register,
			'location' => $location
		];
		
		$this->load->view('bac/batch_settlement',$data);
	}

	

}
?>