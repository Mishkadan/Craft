<?php
/**
 * Emerald Payment Plugin by MintJoomla
 * a plugin for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.menu');

class EmeraldGatewayPaypal_ec extends EmeraldGateway
{
	function accept(&$subscription, $plan)
	{
		$this->log('Start check PayPal');

		if(!$this->_IPNcheck())
		{
			$this->setError(JText::_('EMR_CANNOT_VERYFY'));
			$this->log('PayPal: Verification failed', $_POST);

			return FALSE;
		}

		$post = JFactory::getApplication()->input->post;

		$gateway = $this->get_gateway_id();

		switch($post->get('txn_type'))
		{
			/*
			 case "subscr_signup" :
				if ($post->get('mc_amount1') !== NULL) {
					if ($post->get('mc_amount1') > 0) {
						break;
					} else {
						$post->set('payment_status', 'Completed');
					}
				}
				break;
			*/

			case "subscr_payment" :
				$subscription->add_new($plan, $gateway, $post->get('amount3', $subscription->price));
			case "send_money":
			case "web_accept":
			case "express_checkout":
				$subscription->gateway_id = $gateway;
				switch($post->get('payment_status'))
				{
					case 'Processed' :
					case 'Completed' :
						$subscription->published = 1;
						break;

					case 'Refunded' :
						$subscription->published = 0;
						break;
				}
				if($post->get('payment_status') == 'Pending' && $post->get('pending_reason') != 'PaymentReview')
				{
					$subscription->published = 1;
				}
				break;

			case "new_case" :
				$subscription->published = 0;
				break;

			case "adjustment" :
				$subscription->published = 1;
				break;

			case 'recurring_payment':
			case "subscr_failed" :
			case "subscr_eot" :
			case "subscr_cancel" :
			default:
				// TODO may be do somethign with this.
				return FALSE;
				break;
		}
		$this->log('End paypal check', $subscription);

		return TRUE;
	}

	function pay($amount, $name, $subscription, $plan)
	{
		include_once 'default.php';

		return TRUE;


		if(!$this->params->get('email'))
		{
			$this->setError(JText::_('PP_ERR_NOEMAIL'));

			return FALSE;
		}

		$params = $this->params;


		$param['amount']        = floatval($amount);
		$param['business']      = $params->get('email');
		$param['item_name']     = $name;
		$param['currency_code'] = $params->get('currency', 'USD');
		$param['no_shipping']   = $params->get('ship');

		$param['return']        = $this->_get_return_url($subscription->id);
		$param['notify_url']    = $this->_get_notify_url($subscription->id);
		$param['cancel_return'] = $this->_get_return_url($subscription->id);

		$param['cmd']     = "_xclick";
		$param['lc']      = $params->get('lc', 'EN');
		$param['rm']      = "2";
		$param['charset'] = 'utf-8';
		$param['email']   = JFactory::getUser()->get('email');

		if($params->get('tax'))
		{
			$param['tax'] = $params->get('tax');
		}

		if($params->get('tax_rate'))
		{
			$param['tax_rate'] = $params->get('tax_rate');
		}

		if($subscription->invoice_id)
		{
			$invoice = new EmeraldModelsEmInvoiceTo();
			$data    = $invoice->getText($subscription->invoice_id);

			$param['address1'] = $data->fields->get('address');
			$param['city']     = $data->fields->get('city');
			$param['country']  = $data->fields->get('country');
			$param['state']    = $data->fields->get('state');
			$param['zip']      = $data->fields->get('zip');
		}

		if($params->get('recurred'))
		{
			unset($param['amount']);

			$param['cmd']     = "_xclick-subscriptions";
			$param['src']     = "1";
			$param['sra']     = "1";
			$param['no_note'] = "1";

			$param['a3'] = $plan->total;
			$param['t3'] = strtoupper(substr($plan->days_type, 0, 1));
			$param['p3'] = $plan->days;


			if((int)$params->get('recurred_times'))
			{
				$param['srt'] = intval($params->get('recurred_times'));
			}

			if($amount < $plan->total)
			{
				$param['a1'] = $amount;
				$param['t1'] = strtoupper(substr($plan->days_type, 0, 1));
				$param['p1'] = $plan->days;
			}
		}

		$url = 'https://www.paypal.com/us/cgi-bin/webscr?' . http_build_query($param);
		if($params->get('sandbox') == 'sandbox')
		{
			$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?' . http_build_query($param);
		}

		JFactory::getApplication()->redirect($url);
	}

	private function _IPNcheck2()
	{
		$req = 'cmd=_notify-validate&' . file_get_contents("php://input");

		$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "User-Agent: Emerald-IPN-Validator\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$header .= "Host: www." . ($this->params->get('sandbox') == 'sandbox' ? 'sandbox.' : NULL) . "paypal.com:443\r\n";

		$fp = fsockopen('ssl://www.' . ($this->params->get('sandbox') == 'sandbox' ? 'sandbox.' : NULL) . 'paypal.com', 443, $errno, $errstr, 30);

		$this->log('Get from paypal errno', $errno);
		$this->log('Get from paypal errstr', $errstr);

		fputs($fp, $header . $req);
		while(!feof($fp))
		{
			$res = fgets($fp, 1024);
			if(strcmp($res, "VERIFIED") == 0)
			{
				$this->log('transaction verified', $res);

				return TRUE;
			}
			else if(strcmp($res, "INVALID") == 0)
			{
				$this->log('transaction verification invalid', $res);

				return FALSE;
			}
			fclose($fp);
		}
	}

	private function _IPNcheck()
	{
		$raw_post_data  = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost         = array();
		foreach($raw_post_array as $keyval)
		{
			$keyval = explode('=', $keyval);
			if(count($keyval) == 2)
			{
				// Since we do not want the plus in the datetime string to be encoded to a space, we manually encode it.
				if($keyval[0] === 'payment_date')
				{
					if(substr_count($keyval[1], '+') === 1)
					{
						$keyval[1] = str_replace('+', '%2B', $keyval[1]);
					}
				}
				$myPost[$keyval[0]] = urldecode($keyval[1]);
			}
		}
		// Build the body of the verification post request, adding the _notify-validate command.
		$req                     = 'cmd=_notify-validate';
		$get_magic_quotes_exists = FALSE;
		if(function_exists('get_magic_quotes_gpc'))
		{
			$get_magic_quotes_exists = TRUE;
		}
		foreach($myPost as $key => $value)
		{
			if($get_magic_quotes_exists == TRUE && get_magic_quotes_gpc() == 1)
			{
				$value = urlencode(stripslashes($value));
			}
			else
			{
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}


		$request = curl_init();
		$options = array(
			CURLOPT_URL            => 'https://ipnpb.' . ($this->params->get('sandbox') == 'sandbox' ? 'sandbox.' : NULL) . 'paypal.com/cgi-bin/webscr',
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_POST           => 1,
			CURLOPT_POSTFIELDS     => $req,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FORBID_REUSE   => 1,
			CURLOPT_SSL_VERIFYPEER => 1,
			CURLOPT_SSLVERSION     => 6,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_CAINFO         => __DIR__ . '/cacert.pem',
			CURLOPT_HTTPHEADER     => array(
				"Content-Type: application/x-www-form-urlencoded",
				"User-Agent: Emerald-IPN-Validator",
				"Content-Length: " . strlen($req)
			),
			CURLOPT_CONNECTTIMEOUT => 30
		);
		
		curl_setopt_array($request, $options);

		$this->log('send CURL confirm:', $_POST);

		$response = curl_exec($request);
		$status   = curl_getinfo($request, CURLINFO_HTTP_CODE);
		curl_close($request);

		if(strpos($response, "VERIFIED") !== FALSE)
		{
			$this->log('transaction verified', $response);

			return TRUE;
		}
		else
		{
			$this->log('transaction verification invalid', $response);

			return FALSE;
		}
	}

	function get_plan_id()
	{
		return JFactory::getApplication()->input->get('order_id');
	}

	function get_gateway_id()
	{
		$post = JFactory::getApplication()->input;

		return trim($post->get('subscr_id') . ' ' . $post->get('tx', $post->get('txn_id')));
	}

	function get_amount()
	{
		$post = JFactory::getApplication()->input;

		return (float)$post->get('amount', $post->get('mc_amount3', $post->get('mc_gross')));
	}

	function get_user_id()
	{
		$post = JFactory::getApplication()->input;

		return $post->getInt('cm', $post->getInt('custom'));
	}
}
