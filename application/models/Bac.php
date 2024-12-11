<?php
class Bac extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Register');
		$this->load->model('Location');
	}

	function getRegister()
	{
		$location_id_by_default = $this->Employee->get_logged_in_employee_current_location_id(  );
		$register_id_by_location_id = $this->Employee->getDefaultRegister($this->Employee->get_logged_in_employee_info()->person_id, $location_id_by_default )[ 'register_id' ];
		$register_info_by_register_id = $this->Register->get_info( $register_id_by_location_id );

		return $register_info_by_register_id;
	}

	function getLocation()
	{
		$location = $this->Location->get_info($this->Employee->get_logged_in_employee_current_location_id());
		return $location;
	}

	function getDevEndpoint()
	{
		return "/index.php/api/v1/Bac/runTransaction";
	}

	function getProdEndpoint()
	{
		return "http://localhost:8000/CSPIntegrationServices/Interop/rest/runTransaction";
	}
	

}
