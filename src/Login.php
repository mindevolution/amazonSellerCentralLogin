<?php

namespace AmazonSellerCentral;

/*
 * Login amazon seller central user php curl
 * Exampel, get the report page 
 * $url = "https://sellercentral.amazon.com/gp/site-metrics/report.html#&cols=/c0/c1/c2/c3/c4/c5/c6/c7/c8/c9/c10/c11/c12&sortColumn=13&filterFromDate=05/10/2015&filterToDate=06/10/2015&fromDate=05/10/2015&toDate=06/10/2015&reportID=102:DetailSalesTrafficBySKU&sortIsAscending=0&currentPage=0&dateUnit=1&viewDateUnits=ALL&runDate=";
 * $report = Login::getAmazonBackendUrl(Login::loginSellercentral($username, $password), $url);
 */
class login
{
	/**
	 * @param string $username amazon sellercentral user name(email name)
	 * @param string $password amazon sellercentral password
	 * @return Resource $ch the curl handler
	 */
	static public function loginSellercentral($username, $password)
	{
		$email    = $username;

		// initial login page which redirects to correct sign in page, sets some cookies
		$URL = "https://sellercentral.amazon.com/gp/homepage.html?";

		$ch  = curl_init();

		curl_setopt($ch, CURLOPT_URL, $URL);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'amazoncookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'amazoncookie.txt');
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		//curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_STDERR,  fopen('php://stdout', 'w'));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$page = curl_exec($ch);

		// try to find the actual login form
		if (!preg_match('/<form .*?<\/form>/is', $page, $form)) {
			die('Failed to find log in form!');
		}

		$form = $form[0];

		// find the action of the login form
		if (!preg_match('/action=(?:\'|")?([^\s\'">]+)/i', $form, $action)) {
			die('Failed to find login form url');
		}

		$URL2 = $action[1]; // this is our new post url

		// find all hidden fields which we need to send with our login, this includes security tokens
		$count = preg_match_all('/<input type="hidden"\s*name="([^"]*)"\s*value="([^"]*)"/i', $form, $hiddenFields);

		$postFields = array();

		// turn the hidden fields into an array
		for ($i = 0; $i < $count; ++$i) {
			$postFields[$hiddenFields[1][$i]] = $hiddenFields[2][$i];
		}

		// add our login values
		$postFields['username'] = $email;
		$postFields['password'] = $password;

		$post = '';

		// convert to string, this won't work as an array, form will not accept multipart/form-data, only application/x-www-form-urlencoded
		foreach($postFields as $key => $value) {
			$post .= $key . '=' . urlencode($value) . '&';
		}

		$post = substr($post, 0, -1);

		// set additional curl options using our previous options
		curl_setopt($ch, CURLOPT_URL, $URL2);
		curl_setopt($ch, CURLOPT_REFERER, $URL);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		$page = curl_exec($ch); // make request

		return $ch;
	}

	/**
	 * @param Resource $ch the user login curl handler
	 * @param string $url the request backend page url
	 * @param string $refURL the refURL
	 * @return string the request page content text
	 */
	static public function getAmazonBackendUrl($ch, $url, $refURL = "https://sellercentral.amazon.com/gp/site-metrics/report.html")
	{
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $refURL);
		curl_setopt($ch, CURLOPT_HEADER, false);

		$page = curl_exec($ch); // make request
		
		return $page;
	}
}
