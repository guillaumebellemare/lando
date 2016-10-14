<?php

class CartShipping extends AppModel {
	
	private $service_url = 'https://soa-gw.canadapost.ca/rs/ship/price';
	private $username; 
	private $password;
	private $mailed_by;
	private $origin_postal_code; 
	private $postalCode;
	private $weight;
	private $length;
	private $width;
	private $height;
	
	public function __construct() {
		parent::__construct();
		if(CANADA_POST_USERNAME) $this->username = CANADA_POST_USERNAME;
		if(CANADA_POST_PASSWORD) $this->password = CANADA_POST_PASSWORD;
		if(CANADA_POST_MAILED_BY) $this->mailed_by = CANADA_POST_MAILED_BY;
		if(CANADA_POST_ORIGIN_POSTAL_CODE) $this->origin_postal_code = CANADA_POST_ORIGIN_POSTAL_CODE;
		if(CANADA_POST_SANDBOX_MODE) $this->service_url = 'https://ct.soa-gw.canadapost.ca/rs/ship/price';
	}

	public function getShippingRates($postal_code, $weight, $height = NULL, $width = NULL, $length = NULL) {
		if($postal_code)
		{
			$this->setShippingOptions($postal_code, $weight, $height, $width, $length);
			$xml = $this->buildXML();
	
			$curl = curl_init($this->service_url); // Create REST Request
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($curl, CURLOPT_CAINFO, realpath(dirname(COMPLETE_URL_ROOT.URL_ROOT)) . '/app/helpers/cart/includes/cacert.pem');
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $this->username . ':' . $this->password);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.cpc.ship.rate-v3+xml', 'Accept: application/vnd.cpc.ship.rate-v3+xml', 'Accept-language: '.$this->lang2.'-CA'));
			$curl_response = curl_exec($curl); // Execute REST Request
			
			if(curl_errno($curl)){
				return "<div class='msg is-failure'>Curl error: " . curl_error($curl) . "</div>";
			}
			
			# Debug
			//echo "HTTP Response Status: " . curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			curl_close($curl);
			return $this->curl_response($curl_response);
		}
	}
	
	public function setShippingOptions($postal_code, $weight, $height, $width, $length) {
		$this->postalCode = $postal_code;
		if($weight) $this->weight = $weight;
		if($height) $this->height = $height;
		if($width) $this->width = $width;
		if($length) $this->length = $length;
	}
	
	public function buildXML() {
	
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<mailing-scenario xmlns="http://www.canadapost.ca/ws/ship/rate-v3">';
		$xml .= '<customer-number>'.$this->mailed_by.'</customer-number>';
		$xml .= ' <parcel-characteristics>';
		if($this->weight) $xml .= '   <weight>'.$this->weight.'</weight>';
		if($this->length) $xml .= '   <length>'.$this->length.'</length>';
		if($this->height) $xml .= '   <height>'.$this->height.'</height>';
		if($this->width) $xml .= '   <width>'.$this->width.'</width>';
		$xml .= ' </parcel-characteristics>';
		$xml .= ' <origin-postal-code>'.$this->origin_postal_code.'</origin-postal-code>';
		$xml .= ' <destination>';
		$xml .= '   <domestic>';
		$xml .= '     <postal-code>'.$this->postalCode.'</postal-code>';
		$xml .= '   </domestic>';
		$xml .= ' </destination>';
		$xml .= '</mailing-scenario>';
	
		return $xml;
	}
	
	function curl_response($params) {
	
	  libxml_use_internal_errors(true);
	  $data = NULL;
	  
	  $xml = simplexml_load_string('<root>' . preg_replace('/<\?xml.*\?>/','',$params) . '</root>');
		if (!$xml) {
			$data .= 'Failed loading XML' . "\n";
			$data .= $curl_response . "\n";
			foreach(libxml_get_errors() as $error) {
				$data .= "\t" . $error->message;
			}
			return $data;
		} else {
			if ($xml->{'price-quotes'} ) {
				$priceQuotes = $xml->{'price-quotes'}->children('http://www.canadapost.ca/ws/ship/rate-v3');
				if($priceQuotes->{'price-quote'}) {
					
					$a_data = array();
					foreach($priceQuotes as $priceQuote) {  
						$service_name = $priceQuote->{'service-name'};
						$service_code = $priceQuote->{'service-code'};
						$shipping_cost = $priceQuote->{'price-details'}->{'due'};
						$expected_delivery_date = $priceQuote->{'service-standard'}->{'expected-delivery-date'};
						#$a_data[] = ("service_name" => $service_name, "shipping_cost" => $shipping_cost, "expected_delivery_date" => $expected_delivery_date);
						$row = array();
						$row["service_name"] = $service_name;
						$row["shipping_cost"] = $shipping_cost;
						$row["expected_delivery_date"] = $expected_delivery_date;
						$row["service_code"] = $service_code;
						$a_data[] = $row;
					}
					return $a_data;
				}
			}

			if ($xml->{'messages'} ) {
				$rtr = NULL;					
				$messages = $xml->{'messages'}->children('http://www.canadapost.ca/ws/messages');		
				foreach($messages as $message) {
					$rtr .= 'Error Code: ' . $message->code . "\n";
					$rtr .= 'Error Msg: ' . $message->description . "\n\n";
				}
				return $rtr;
			}
		}
		
		return $data;
	}
	
	
}
