<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Checkout extends Main_Controller
{
	public function index()
	{
		$this->load->helper('url');
		redirect('/checkout/first');
	}

	public function first()
	{
		$this->load->helper('url');
		$this->load->library('PayPal');
		$this->load_model(array('Order', 'Payment'));

		$Cart = $this->cart;

		$order_id = $this->Order_model->insert(array(
			'user_id' => $this->session->userdata('userID'),
			'total_price' => $Cart->total(),
			'shipping_address' => 0,
			'billing_address' => 0,
			'date_created' => date('Y-m-d H:i:s'),
		));

		$payment = array(
			'user_id' => $this->session->userdata('userID'),
			'order_id' => $order_id
		);

		$payment_id = $this->Payment_model->insert($payment);
		$this->Order_model->update(array('payment_id' => $payment_id), $order_id);

		$PP = new PayPal;
		$payment = $PP->createPayment(
			array(
				'description' => '',
				'amount' => $Cart->total(),
				'currency' => 'USD',
			),
			base_url('/checkout/complete/' . $payment_id),
			base_url('/checkout/cancel/' . $payment_id),
			$Cart->contents()
		);

		$this->Payment_model->update($payment, $payment_id);

		header("Location: {$payment['approve_url']}");
		exit;
	}

	public function complete($payment_id)
	{
		$this->load->library('PayPal');

		$this->load_model('Payment');

		$this->Payment_model->set($payment_id);
		$execute_url = $this->Payment_model->execute_url;

		$payer_id = $this->input->get('PayerID');

		$PP = new PayPal;
		$PP->completePayment($execute_url, $payer_id);

		$this->Payment_model->update(array('status' => 'complete'), $payment_id);

		echo 'Payment complete. Payment ID:' . $payment_id;
	}

	public function cancel()
	{
		echo 'Payment canceled';
	}
}