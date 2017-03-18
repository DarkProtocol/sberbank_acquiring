<?php 

namespace frontend\components;

class Sberbank 
{
	const API_USERNAME = 'sputnik_eda-api';
	const API_PASSWORD = 'sputnik_eda';

	// returnUrl param
	private $return_url = 'http://sputnik-eda.ru/pay/'; 
	// failUrl param
	private $fail_url = 'http://sputnik-eda.ru/fail-pay/'; 

	// default register options
	private $register_options = [
		'language' => 'ru',
		'currency' => '643',
		'returnUrl' => 'https://3dsec.sberbank.ru/payment/rest/register.do',
		'failUrl' => 'https://3dsec.sberbank.ru/payment/rest/getOrderStatus.do',
	];

	/**
	* __construct set returnUrl and failtUrl params 
	*
	* @param string $return_url - returnUrl param
	* @param string $fail_url - failUrl param
	* @return void
	*/
	public function __construct($return_url = false, $fail_url = false) 
	{
		if ($return_url !== false) 
			$this->return_url = $return_url;
		}

		if ($fail_url !== false) 
			$this->fail_url = $fail_url;
		}
	}

	/**
	* setRegisterOption set value to register options array
	*
	* @param string $name - index of array
	* @param string $value - value of array
	* @return void
	*/
	public function setRegisterOption($name, $value)
	{
		$this->register_options[$name] = urlencode($value);
	}

	/**
	* getRegisterOption get register options array
	*
	* @return array
	*/
	public function getRegisterOption($name, $value)
	{
		return $this->register_options;
	}

	/**
	* registerRequest make a register action request
	*
	* @param int $order_number - nrder number in site system
	* @param int $amount - order price
	* @return array
	*/
	public function registerRequest($order_number, $amount)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->register_rest_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$register_options = array_merge($this->register_options, [
			'userName' => self::API_USERNAME,
			'password' => self::API_PASSWORD,
			'orderNumber' => $order_number,
			'amount' => $amount,
		]);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $register_options);
		$responce = json_decode(curl_exec($ch));
		curl_close($ch);

		if (isset($responce['errorCode']))
			$responce['status'] => false;
		else 
			$responce['status'] => true;

		return $responce;

	}

	/**
	* orderStatusRequest make a order status action request
	*
	* @param int $order_id - order id of payment system
	* @param string $language - response language
	* @return array
	*/
	public function orderStatusRequest($order_id, $language = 'ru')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->order_status_rest_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$order_status_params = [
			'userName' => self::API_USERNAME,
			'password' => self::API_PASSWORD,
			'orderId' => $order_id,
			'language' => $language,
		];

		curl_setopt($ch, CURLOPT_POSTFIELDS, $order_status_params);
		$responce = json_decode(curl_exec($ch));
		curl_close($ch);

		return $responce;

	}
}