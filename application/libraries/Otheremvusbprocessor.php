<?php
require_once ("Datacapusbprocessor.php");
class Otheremvusbprocessor extends Datacapusbprocessor
{
	function __construct($controller)
	{		
		parent::__construct($controller,$controller->Location->get_info_for_key('secure_device_override_emv'),$controller->Location->get_info_for_key('secure_device_override_non_emv'));
		$this->controller->load->model('Bac');
	}

	public function start_cc_processing()
	{
		$cc_amount = to_currency_no_money($this->controller->cart->get_payment_amount(lang('common_credit')));
		# If negative amount, it might be a return, unsure of how PHP POS is handling this. Removed for now.
		#if ($cc_amount <= 0)
		#{
	#		$this->controller->_reload(array('error' => lang('sales_charging_card_failed_please_try_again')), false);
	#		return;
#		}
		$invoice = $this->_get_session_invoice_no();
		$totalAmount =  to_currency_no_money(abs($cc_amount));
		$register = $this->controller->Bac->getRegister();
		$location = $this->controller->Bac->getLocation();
		$endpoint = $this->controller->Bac->getProdEndpoint();
		# $endpoint = $this->Bac->getDevEndpoint() . + "?transactionType=sale";

		$this->controller->load->view('bac/sale',				
				array(
				'invoice' => $invoice,
				'register' => $register,
				'endpoint' => $endpoint,
				'totalAmount' => $totalAmount,
				'location' => $location
			)
		);
	}
	public function finish_cc_processing()
	{
		$acqNumber = urldecode($this->controller->input->request('acqNumber') ?? '');
		$authorizationNumber = urldecode($this->controller->input->request('authorizationNumber') ?? '');
		$cardBrand = urldecode($this->controller->input->request('cardBrand') ?? '');
		$cardHolderName = urldecode($this->controller->input->request('cardHolderName') ?? '');
		$hostDate = urldecode($this->controller->input->request('hostDate') ?? '');
		$hostTime = urldecode($this->controller->input->request('hostTime') ?? '');
		$invoice = urldecode($this->controller->input->request('invoice') ?? '');
		$maskedCardNumber = urldecode($this->controller->input->request('maskedCardNumber') ?? '');
		$referenceNumber = urldecode($this->controller->input->request('referenceNumber') ?? '');
		$responseCode = urldecode($this->controller->input->request('responseCode') ?? '');
		$responseCodeDescription = urldecode($this->controller->input->request('responseCodeDescription') ?? '');
		$salesAmount = urldecode($this->controller->input->request('salesAmount') ?? '');
		$systemTraceNumber = urldecode($this->controller->input->request('systemTraceNumber') ?? '');
		$transactionId = urldecode($this->controller->input->request('transactionId') ?? '');
		$entryMode = urldecode($this->controller->input->request('entryMode') ?? '');
		$currencyVoucher = urldecode($this->controller->input->request('currencyVoucher') ?? '');
		$TerminalDisplayLine1Voucher = urldecode($this->controller->input->request('TerminalDisplayLine1Voucher') ?? '');
		$TerminalDisplayLine2Voucher = urldecode($this->controller->input->request('TerminalDisplayLine2Voucher') ?? '');
		$TerminalDisplayLine3Voucher = urldecode($this->controller->input->request('TerminalDisplayLine3Voucher') ?? '');
		$printTags = urldecode($this->controller->input->request('printTags') ?? '');
		$signature = urldecode($this->controller->input->request('signature') ?? '');
		$trnTotalTime = urldecode($this->controller->input->request('trnTotalTime') ?? '');

		$all = [];
		foreach ($this->controller->input->request() as $key => $val)
		{
			$all[$key] = $val;
		} 

		
		if ($responseCode == '00')
		{
			//Make sure we remove invoice number in case of partial auth...We need a new invoice number
			$this->controller->cart->invoice_no = NULL;
			$this->controller->cart->save();
			$this->controller->session->set_userdata('cqNumber', $acqNumber);
			$this->controller->session->set_userdata('auth_code', $authorizationNumber);
			$this->controller->session->set_userdata('ref_no', $referenceNumber);
			$this->controller->session->set_userdata('cc_token', $maskedCardNumber);
			$this->controller->session->set_userdata('acq_ref_data', $transactionId);
			$this->controller->session->set_userdata('sys_trace_no', $systemTraceNumber);
			$this->controller->session->set_userdata('process_data', json_encode($all));
			$this->controller->session->set_userdata('tran_type', $responseCode);
			$this->controller->session->set_userdata('text_response', $responseCodeDescription);
			$this->controller->session->set_userdata('masked_account', $maskedCardNumber);
			$this->controller->session->set_userdata('card_issuer', $cardBrand);
			if ($this->controller->_payments_cover_total())
			{
				$this->controller->session->set_userdata('CC_SUCCESS', TRUE);
				redirect(site_url('sales/complete'));
			}

		}
		else
		{
			if ($responseCode == '60' || $responseCode == '61' || $responseCode == '62' || $responseCode == '63' || $responseCode == '75')
			{
				$this->controller->session->set_userdata('cqNumber', $acqNumber);
				$this->controller->session->set_userdata('auth_code', $authorizationNumber);
				$this->controller->session->set_userdata('ref_no', $referenceNumber);
				$this->controller->session->set_userdata('process_data', json_encode($all));
				$this->controller->session->set_userdata('tran_type', $responseCode);
				$this->controller->session->set_userdata('text_response', $responseCodeDescription);
				$this->controller->session->set_userdata('masked_account', $maskedCardNumber);
				$this->controller->session->set_userdata('card_issuer', $cardBrand);
				
				redirect(site_url('sales/declined'));
			}
			else
			{
				$this->controller->_reload(array('error' => $responseCode.': '.$responseCodeDescription), false);
			}
		}
	}
	public function cancel_cc_processing()
	{
		#redirect(site_url('sales/declined'));
	}
	public function void_partial_transactions()
	{
		$partial_transactions = $this->controller->cart->get_partial_transactions();
		if( count( $partial_transactions ) === 0 ) {
			return true;
		} else {
			return false;
		}
		#redirect(site_url('sales/declined'));
	}
	public function void_sale($sale_id)
	{
		if ($this->controller->Sale->can_void_cc_sale($sale_id))
		{
			$payments = $this->_get_cc_payments_for_sale($sale_id);
			
			$transactions = array();
			$counter = 0;
			
			foreach($payments as $payment)
			{
				if ($counter == 0)
				{
					$sale_info = $this->controller->Sale->get_info($sale_id)->row();
					$tip = $sale_info->tip;
				}
				else
				{
					$tip = 0;
				}
				
				$invoice = substr((date('mdy')).(time() - strtotime("today")).($this->controller->Employee->get_logged_in_employee_info()->person_id), 0, 16);
				
				$transactions[] = array(
					'authorizationCode' => $payment['auth_code'],
					'referenceNumber' => $payment['ref_no'],
					'systemTraceNumber' =>$payment['sys_trace_no'],
					'invoice' => $invoice,
					'totalAmount' => to_currency_no_money($payment['payment_amount']),
				);
				
				$counter++;
			}
			
			$register = $this->controller->Bac->getRegister();
			$location = $this->controller->Bac->getLocation();
			$endpoint = $this->controller->Bac->getProdEndpoint();
			# $endpoint = $this->Bac->getDevEndpoint() . + "?transactionType=sale";

			$this->controller->load->view('bac/refund',				
					array(
					'invoice' => $transactions[0]['invoice'],
					'register' => $register,
					'endpoint' => $endpoint,
					'totalAmount' => $transactions[0]['totalAmount'],
					'referenceNumber' => $transactions[0]['referenceNumber'],
					'authorizationCode' => $transactions[0]['authorizationCode'],
					'systemTraceNumber' => $transactions[0]['systemTraceNumber'],
					'location' => $location,
					'sale_id' => $sale_id
				)
			);		
			//Always return true as error handling is in JS
			return TRUE;
		}
		
		return FALSE;
		
	}
	public function void_return($sale_id)
	{
		#redirect(site_url('sales/declined'));
	}
	public function tip($sale_id,$tip_amount)
	{
		
	}
}