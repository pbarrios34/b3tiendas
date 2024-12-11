<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Bac extends REST_Controller {

    protected $methods = [
        'runTransaction_post' => ['level' => 1, 'limit' => 60],
      ];

    function getSaleResponse()
    {
        $response = json_decode('{
            "runTransactionResult": "{\"acqNumber\":\"\",\"authorizationNumber\":\"282515\",\"cardBrand\":\"retail EMV\",\"cardHolderName\":\"PAYWAVE/VISA\",\"hostDate\":\"03242023\",\"hostTime\":\"122825\",\"invoice\":\"1\",\"maskedCardNumber\":\"430303XXXXXX6343\",\"referenceNumber\":\"12282515    \",\"responseCode\":\"00\",\"responseCodeDescription\":\"APROBADA\",\"salesAmount\":\"000000000100\",\"systemTraceNumber\":\"034764\",\"transactionId\":\"03476412282515122825\",\"entryMode\":\"CLC\",\"currencyVoucher\":\"GTQ\",\"TerminalDisplayLine1Voucher\":\"TEST ISC480\",\"TerminalDisplayLine2Voucher\":\"PRUEBAS INTEGRACUONED BAC\",\"TerminalDisplayLine3Voucher\":\"ZONA 12\",\"printTags\":[\"VISA CREDITO\",\"DF:A0000000031010\",\"TVR:0000000000\",\"0E:56495341204352454449544F\"],\"signature\":\"1\",\"trnTotalTime\":\"15962\"}\r\n"
        }');
        return $response;
    }

    function getRefundResponse()
    {
        $response = json_decode('{
            "runTransactionResult": "{ }"
        }');
        return $response;
    }

    function getBatchInquiryResponse()
    {
        $response = json_decode('{
            "runTransactionResult": "{\"acqNumber\":\"000000031257034\",\"cardBrand\":\"retail EMV\",\"hostDate\":\"03242023\",\"hostTime\":\"122854\",\"refundsAmount\":\"000000000000\",\"refundsTransactions\":\"000000\",\"responseCode\":\"00\",\"responseCodeDescription\":\"APROBADA\",\"salesAmount\":\"000000000100\",\"salesTax\":\"000000000000\",\"salesTip\":\"000000000000\",\"salesTransactions\":\"000001\",\"currencyVoucher\":\"GTQ\",\"TerminalDisplayLine1Voucher\":\"TEST ISC480\",\"TerminalDisplayLine2Voucher\":\"PRUEBAS INTEGRACUONED BAC\",\"TerminalDisplayLine3Voucher\":\"ZONA 12\",\"signature\":\"1\",\"trnTotalTime\":\"9943\"}\r\n"
        }');
        $responseNoTransactions = json_decode('{
            "runTransactionResult": "{\"acqNumber\":\"\",\"responseCode\":\"21\",\"responseCodeDescription\":\"SIN TRANSACCIONES\",\"currencyVoucher\":\"GTQ\",\"TerminalDisplayLine1Voucher\":\"TEST ISC480\",\"TerminalDisplayLine2Voucher\":\"PRUEBAS INTEGRACUONED BAC\",\"TerminalDisplayLine3Voucher\":\"ZONA 12\",\"signature\":\"0\",\"trnTotalTime\":\"30157\"}\r\n"
        }');
        return $response;
    }

    function getBatchSettlementResponse()
    {
        $response = json_decode('{
            "runTransactionResult": "{\"acqNumber\":\"000000031257034\",\"authorizationNumber\":\"000339\",\"cardBrand\":\"retail EMV\",\"hostDate\":\"03242023\",\"hostTime\":\"123058\",\"refundsAmount\":\"000000000000\",\"refundsTransactions\":\"000000\",\"responseCode\":\"00\",\"responseCodeDescription\":\"APROBADA\",\"salesAmount\":\"000000000100\",\"salesTax\":\"000000000000\",\"salesTip\":\"000000000000\",\"salesTransactions\":\"000001\",\"currencyVoucher\":\"GTQ\",\"TerminalDisplayLine1Voucher\":\"TEST ISC480\",\"TerminalDisplayLine2Voucher\":\"PRUEBAS INTEGRACUONED BAC\",\"TerminalDisplayLine3Voucher\":\"ZONA 12\",\"signature\":\"1\",\"trnTotalTime\":\"13406\"}\r\n"
        }
        ');
        $responseNoTransactions = json_decode('{
            "runTransactionResult": "{\"acqNumber\":\"\",\"responseCode\":\"21\",\"responseCodeDescription\":\"SIN TRANSACCIONES\",\"currencyVoucher\":\"GTQ\",\"TerminalDisplayLine1Voucher\":\"TEST ISC480\",\"TerminalDisplayLine2Voucher\":\"PRUEBAS INTEGRACUONED BAC\",\"TerminalDisplayLine3Voucher\":\"ZONA 12\",\"signature\":\"0\",\"trnTotalTime\":\"30303\"}\r\n"
        }');
        return $response;
    }

    function runTransaction_get()
    {
        $this->runTransaction_post();
    }
    
    function runTransaction_post()
	{
        #$response = json_decode($this->input->raw_input_stream);
        $transactionType = $this->input->get('transactionType');
        $response = json_decode('{ "runTransactionResult": "{ \"test\": \"test\" }" }');
        switch ($transactionType) {
            case 'SALE':
                $response = $this->getSaleResponse();
                break;
            case 'REFUND':
                $response = $this->getRefundResponse();
                break;
            case 'BATCH_INQUIRY':
                $response = $this->getBatchInquiryResponse();
                break;
            case 'BATCH_SETTLEMENT':
                $response = $this->getBatchSettlementResponse();
                break;
        }
		$this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
	}

}