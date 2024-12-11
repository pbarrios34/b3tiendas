<?php
	//Clean all buffers
	while (ob_get_level())
	{
		ob_end_clean();
	}
	if (!$this->config->item('legacy_detailed_report_export'))
	{
		$rows = array();
	
		$row = array();
		
		if (!empty($details_data))
		{
			foreach ($headers['details'] as $header) 
			{
				$row[] = strip_tags($header['data']);
			}
		}
		foreach ($headers['summary'] as $header) 
		{
			$row[] = strip_tags($header['data']);
		}
		$rows[] = $row;
	
		foreach ($summary_data as $key=>$datarow) 
		{		
			$recv_id = $key;
			if(isset($details_data[$key])) 
			{
				foreach($details_data[$key] as $datarow2)
				{
					$costo_actual = array("data" => to_currency($this->Receiving->get_actual_cost_in_recv( $recv_id, $datarow2[0]['data'] )), "align" => "left");
					$costo_anterior = array("data" => to_currency($this->Receiving->get_previous_cost_in_recv( $recv_id, $datarow2[0]['data'] )), "align" => "left");
					if( !empty( $this->Receiving->get_actual_cost_in_recv( $recv_id, $datarow2[0]['data'] ) ) && !is_null( $this->Receiving->get_actual_cost_in_recv( $recv_id, $datarow2[0]['data'] ) ) && !empty( $this->Receiving->get_previous_cost_in_recv( $recv_id, $datarow2[0]['data'] ) ) && !is_null( $this->Receiving->get_previous_cost_in_recv( $recv_id, $datarow2[0]['data'] ) ) ){
						array_splice($datarow2, 3, 0, array($costo_actual, $costo_anterior));
					}

					$row = array();
					foreach($datarow2 as $cell)
					{
						$row[] = str_replace('<span style="white-space:nowrap;">-</span>', '-', strip_tags($cell['data'] ? $cell['data'] : ''));				
					}
			
					foreach($datarow as $cell)
					{
						$row[] = str_replace('<span style="white-space:nowrap;">-</span>', '-', strip_tags($cell['data'] ? $cell['data'] : ''));
					}
					$rows[] = $row;
				}
			}
			else
			{
				$row = array();
				if (!empty($details_data))
				{
					foreach ($headers['details'] as $empty_row) 
					{
						$row[]=lang('common_na');
					}	
				}
				foreach($datarow as $cell)
				{
					$row[] = str_replace('<span style="white-space:nowrap;">-</span>', '-', strip_tags($cell['data'] ? $cell['data'] : ''));
				}
				$rows[] = $row;
			}		
		}
	}
	else
	{
		$rows = array();
		$row = array();
		foreach ($headers['summary'] as $header) 
		{
			$row[] = strip_tags($header['data'] ? $header['data'] : '');
		}
		$rows[] = $row;
	
		foreach ($summary_data as $key=>$datarow) 
		{
			$row = array();
			foreach($datarow as $cell)
			{
				$row[] = str_replace('<span style="white-space:nowrap;">-</span>', '-', strip_tags($cell['data']));			
			}
		
			$rows[] = $row;

			$row = array();
			foreach ($headers['details'] as $header) 
			{
				$row[] = strip_tags($header['data']);
			}
		
			$rows[] = $row;
		
			if(isset($details_data[$key]))
			{
				foreach($details_data[$key] as $datarow2)
				{
					$row = array();
					foreach($datarow2 as $cell)
					{
						$row[] = str_replace('<span style="white-space:nowrap;">-</span>', '-', strip_tags($cell['data']));				
					}
					$rows[] = $row;
				}
			}
		}
	}
	$this->load->helper('spreadsheet');
	array_to_spreadsheet($rows, strip_tags($title) . '.'.($this->config->item('spreadsheet_format') == 'XLSX' ? 'xlsx' : 'csv'), true);
	exit;
?>