<?php
require_once ("Secure_area.php");
class ExpiredItems extends Secure_area 
{
	function __construct(  ){

		parent::__construct();
		$this->load->model('Location');
		$this->load->model('Supplier');
		$this->load->model('Item');
		$this->load->model('Employee');
		$this->load->model('Person');
    }

	function index(  ){		
		$data = $this->get_initial_data();
		$this->load->view('expired_items_report', $data);
	}

	function get_initial_data(  ){
		$get_all_locations = $this->Location->get_all();
		$get_all_suppliers = $this->Supplier->get_all();
		$get_all_items = $this->Item->get_all();

		$array_suppliers = array();
        $array_locations = array();
        $array_items = array();

		$array_dates = [
			'Hoy',
			'Ayer',
			'Últimos 7 días',
			'Últimos 30 días',
			'Esta semana',
			'La semana pasada',
			'Este mes',
			'El mes pasado',
			'Este trimestre',
			'Último trimestre',
			'Este año',
			'El año pasado'
		];

        foreach ($get_all_items->result_array() as $item) {
			$array_items[$item['item_id']] = $item['name'] ;
		}
		asort($array_items);
		$__all_items = array( -1 => "Todos los Items" );
		$array_items = $__all_items + $array_items;

        foreach ($get_all_suppliers->result_array() as $supplier) {
			$array_suppliers[$supplier['person_id']] = $supplier['full_name'] ;
		}
		asort($array_suppliers);
		$__all_suppliers = array( -1 => "Todos los proveedores" );
		$array_suppliers = $__all_suppliers + $array_suppliers;

		foreach ($get_all_locations->result_array() as $location) {
			$array_locations[$location['location_id']] = $location['name'] ;
		}

		$data = array(
			'array_items' => $array_items,
			'array_suppliers' => $array_suppliers,
			'array_locations' => $array_locations,
			'array_dates' => $array_dates,
		);
		return $data;
	}

    function generate(  ){

        $filter_in_base = $this->input->post('items_expired_date_filter');
        $custom_date = $this->input->post('items_expired_custom_date_name');
		$supplier_filter = $this->input->post('items_expired_report_supplier');
		$item_filter = $this->input->post('items_expired_report_item');
		$export_to_excel = $this->input->post('catagory_report_export_to_excel');
		if( $custom_date ){
			$start = DateTime::createFromFormat('d-m-Y', $this->input->post('items_expired_init_date_name'));
			$start->setTime(0, 0);

			$end = DateTime::createFromFormat('d-m-Y', $this->input->post('items_expired_end_date_name'));
			$end->setTime(23, 59, 59);

			$_today = new DateTime(); 
			$today = new DateTime(); 
			$today->setTime(0, 0);

			$date_filter['start'] = $start->format('Y-m-d H:i:s');
			$date_filter['end'] = ( $end->format('Y-m-d') === $today->format('Y-m-d') ) ? $_today->format('Y-m-d H:i:s') : $end->format('Y-m-d H:i:s');
		}else{
			$date_filter = $this->get_date_range( (int)$this->input->post( 'items_expired_date_container' ) );
		}
		$get_all_locations_ids = $this->Location->get_all_ids();
		$filter_location =[];
		foreach( $get_all_locations_ids as $location ){
			if( !is_null($this->input->post('catagory_report_'.$location.'_location')) ){
				array_push($filter_location, $this->input->post('catagory_report_'.$location.'_location'));
			}
		}

		$filters = [
			'base_in'	=> ($filter_in_base == 0) ? 'expire_date' : 'date_items_quantity_exp_confirmed',
			'dates' 	=> $date_filter,
			'locations' => $filter_location,
            'supplier'  => $supplier_filter,
            'item'      => $item_filter
		];

		$data = $this->get_data_for_report( $filters );
		$data['export_to_excel'] = !is_null($export_to_excel) ? true : false;
		$this->load->view('expired_items_report', $data);
	}

    function get_date_range($date_filter) {
		$now = new DateTime();
		$date_range = [];
	
		switch ($date_filter) {
			case 0: // Hoy
				$start = new DateTime('today midnight');
				$end = $now;
				break;
	
			case 1: // Ayer
				$start = (new DateTime('yesterday'))->setTime(0, 0);
				$end = (new DateTime('yesterday'))->setTime(23, 59, 59);
				break;
	
			case 2: // Últimos 7 días
				$start = (clone $now)->modify('-7 days')->setTime(0, 0);
				$end = $now;
				break;
	
			case 3: // Últimos 30 días
				$start = (clone $now)->modify('-30 days')->setTime(0, 0);
				$end = $now;
				break;
	
			case 4: // Esta semana
				$start = (new DateTime('monday this week'))->setTime(0, 0);
				$end = $now;
				break;
	
			case 5: // La semana pasada
				$start = (new DateTime('monday last week'))->setTime(0, 0);
				$end = (new DateTime('sunday last week'))->setTime(23, 59, 59);
				break;
	
			case 6: // Este mes
				$start = (new DateTime('first day of this month'))->setTime(0, 0);
				$end = $now;
				break;
	
			case 7: // El mes pasado
				$start = (new DateTime('first day of last month'))->setTime(0, 0);
				$end = (new DateTime('last day of last month'))->setTime(23, 59, 59);
				break;
	
			case 8: // Este trimestre
				$currentMonth = (int)$now->format('m');
				$quarterStartMonth = $currentMonth - (($currentMonth - 1) % 3);
				$start = (new DateTime("first day of -".($currentMonth - $quarterStartMonth)." month"))->setTime(0, 0);
				$end = $now;
				break;
	
			case 9: // Último trimestre
				$currentMonth = (int)$now->format('m');
				$quarterStartMonth = $currentMonth - (($currentMonth - 1) % 3) - 3;
				$start = (new DateTime("first day of -".($currentMonth - $quarterStartMonth)." month"))->setTime(0, 0);
				$end = (new DateTime("last day of -".($currentMonth - $quarterStartMonth - 2)." month"))->setTime(23, 59, 59);
				break;
	
			case 10: // Este año
				$start = (new DateTime('first day of January'))->setTime(0, 0);
				$end = $now;
				break;
	
			case 11: // El año pasado
				$start = (new DateTime('first day of January last year'))->setTime(0, 0);
				$end = (new DateTime('last day of December last year'))->setTime(23, 59, 59);
				break;
	
			default:
				throw new InvalidArgumentException("Filtro de fecha no válido.");
		}
	
		$date_range['start'] = $start->format('Y-m-d H:i:s');
		$date_range['end'] = $end->format('Y-m-d H:i:s');
	
		return $date_range;
	}

    function get_data_for_report( $filters = [] ){
        $first_data = $this->get_initial_data();
		$headers = $this->get_headers_report();
		$report_data = $this->get_report_data( $filters );

		$data = $first_data;
		$data['headers'] = $headers;
		$data['report_data'] = $this->organize_array($report_data);

		return $data;
	}

    function get_headers_report(  ){
		$headers = array(
			'location' 			    => lang('reports_expired_hlocation'),
			'item' 		            => lang('reports_expired_items_item'),
            'expired_date_confirm' 	=> lang('reports_expired_hdate_confirm'),
			'expired_date' 	        => lang('reports_expired_hdate'),
			'recv_quantity' 		=> lang('reports_expired_hquantity'),
			'confirm_quantity' 		=> lang('reports_expired_hconfirm_quantity'),
			'employee' 	            => lang('reports_expired_hconfirm_employee'),
			'sku' 				    => lang('reports_expired_hsku'),
			'upc' 	                => lang('reports_expired_hupc'),
			'supplier' 	            => lang('reports_supplier'),
		);

		return $headers;
	}

    function get_report_data( $filters = [] ){

		
		$data_filter_from_recv_items =  $this->get_data_from_base_on_filters( $filters );
        $keys = array_keys($data_filter_from_recv_items);
        $locations_by_reciv_id = $this->get_location_by_recv( $keys, $filters );
        $add_location = $this->add_location_to_array( $locations_by_reciv_id, $data_filter_from_recv_items );
        $with_item_id_key = $this->replace_key_with_item_id( $add_location );
        $key_items = array_keys($with_item_id_key);
        $data_items = $this->get_item_data_by_item( $key_items );
        $add_sku_upc = $this->add_sku_upc( $data_items, $with_item_id_key );

        return $add_sku_upc;
	}

    function get_data_from_base_on_filters( $filters = [] ){
		$this->db->select('item_id, receiving_id, date_items_quantity_exp_confirmed, expire_date, quantity_received, items_quantity_exp_confirmed, employee_confirmed_quantity_items_exp, supplier_id');
        $this->db->from('receivings_items');
        $this->db->where($filters['base_in'] .' IS NOT NULL');
        $this->db->where($filters['base_in'] .' >=', $filters['dates']['start']);
        $this->db->where($filters['base_in'] .' <=', $filters['dates']['end']);
		if( $filters['supplier'] != -1 ){
			$this->db->where('supplier_id =', $filters['supplier']);
		}
		if( $filters['item'] != -1 ){
			$this->db->where('item_id =', $filters['item']);
		}
        
        $query = $this->db->get();
        $result = $query->result_array();

        $items = [];
        foreach ($result as $row) {
            $items[$row['receiving_id']] = [
                'item_id' => $row['item_id'],
                'receiving_id' => $row['receiving_id'],
                'date_items_quantity_exp_confirmed' => $row['date_items_quantity_exp_confirmed'],
                'expire_date' => $row['expire_date'],
                'quantity_received' => $row['quantity_received'],
                'items_quantity_exp_confirmed' => $row['items_quantity_exp_confirmed'],
                'employee_confirmed_quantity_items_exp' => $row['employee_confirmed_quantity_items_exp'],
                'supplier_id' => $row['supplier_id']
            ];
        }

        return $items;
    }

    function get_location_by_recv( $keys, $filters = [] ){
        $locations = [];
		if( !empty( $keys ) ){
			$this->db->select('receiving_id, location_id');
			$this->db->from('receivings');
			$this->db->where_in('receiving_id', $keys);
			$this->db->where_in('location_id', $filters['locations']);
			
			$query = $this->db->get();
			$result = $query->result_array();
	
			foreach ($result as $row) {
				$locations[$row['receiving_id']] = $row['location_id'];
			}
		}

        return $locations;
    }

    function add_location_to_array($locations_by_reciv_id, $data_filter_from_recv_items) {
        foreach ($data_filter_from_recv_items as $receiving_id => &$item) {
            if (isset($locations_by_reciv_id[$receiving_id])) {
                $item['location'] = $locations_by_reciv_id[$receiving_id];
            }else{
				unset( $data_filter_from_recv_items[$receiving_id] );
			}
        }
        return $data_filter_from_recv_items;
    }

    function replace_key_with_item_id($array) {
        $new_array = [];
        foreach ($array as $key => $value) {
            $item_id = $value['item_id'];
            $new_array[$item_id] = $value;
			unset( $new_array[$item_id]['receiving_id'] );
        }
        return $new_array;
    }

    function get_item_data_by_item( $keys ){
        $item_data = [];
		if( !empty( $keys ) ){
			$this->db->select('item_id, product_id', 'item_number');
			$this->db->from('items');
			$this->db->where_in('item_id', $keys);
			
			$query = $this->db->get();
			$result = $query->result_array();
	
			foreach ($result as $row) {
				$item_data[$row['item_id']] = [
					'sku'   => $row['product_id'],
					'upc'   => $row['item_number']
				];
			}
		}

        return $item_data;
    }

    function add_sku_upc($data_items, $with_item_id_key) {
        foreach ($with_item_id_key as $item_id => &$item) {
            if (isset($data_items[$item_id])) {
                $item['sku'] = $data_items[$item_id]['sku'];
                $item['upc'] = $data_items[$item_id]['upc'];
            }
        }
        return $with_item_id_key;
    }

	function organize_array($array) {
		$order = [
			'location',
			'item_id',
			'date_items_quantity_exp_confirmed',
			'expire_date',
			'quantity_received',
			'items_quantity_exp_confirmed',
			'employee_confirmed_quantity_items_exp',
			'sku',
			'upc',
			'supplier_id'
		];
	
		$organized_array = [];
	
		foreach ($array as $key => $item) {
			$organized_item = [];
			foreach ($order as $field) {
				if (isset($item[$field])) {
					$organized_item[$field] = $item[$field];
				} else {
					$organized_item[$field] = null; 
				}
			}
			if (isset($organized_item['location'])) {
				$organized_item['location'] = $this->Location->get_info( (int)$organized_item['location'] )->name;
			}
			if( isset($organized_item['item_id']) ){
				$organized_item['item_id'] = $this->Item->get_info( (int)$organized_item['item_id'] )->name;
			}
			if( isset($organized_item['employee_confirmed_quantity_items_exp']) ){
				$organized_item['employee_confirmed_quantity_items_exp'] = $this->Employee->get_info( (int)$organized_item['employee_confirmed_quantity_items_exp'] )->full_name;
			}
			if( isset($organized_item['supplier_id']) ){
				$organized_item['supplier_id'] = $this->Person->get_info( (int)$organized_item['supplier_id'] )->full_name;
			}
			$organized_array[$key] = $organized_item;
		}
	
		return $organized_array;
	}

}
?>