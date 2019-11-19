<?php

/**
 * (С) Компания "Техномир-Автотрейд"
 * http://tehnomir.com.ua
 *
 * TM_Info XML
 *
 * Получение информации о деталях
 * версия 1.05
 *
 * Совместимость: php4, php5
 *
 */


class Tminfo
{
	//	var $adresses = array(0 => 'http://tm.tehno.loc/ws/xml.php', 1 => null);
	var $adresses = array(0 => 'https://tehnomir.com.ua/ws/xml.php', 1 => null);

	var $usr_login;
	var $usr_passwd;

	var $error = false;
	var $error_string;

	var $correct_encoding = false;


	function Tminfo()
	{
		Kohana::$log->attach(new Log_File(APPPATH.'logs/tehnomir'), array(Log::NOTICE), Log::NOTICE);
		if ( ! extension_loaded('SimpleXML') )
		{
			die('extension "SimpleXML" is not loaded !');
		}

	}
	
	public static function instance()
	{
		return new Tminfo();
	}

	function SetLogin( $usr_login )
	{
		// Установка логина

		$this->usr_login = $usr_login;
	}

	function SetPasswd( $usr_passwd )
	{
		// Установка пароля

		$this->usr_passwd = $usr_passwd;
	}


	function TestConnect( $String )
	{
		// Проверка связи

		$String_url = urlencode( $String );

		$url = "{$this->adresses[0]}?act=TestConnect&usr_login={$this->usr_login}&usr_passwd={$this->usr_passwd}&String=$String_url";

		$fp = @fopen($url, 'r');

		if ( $fp === false )
		{
			$this->error = true;
			$this->error_string = 'Cannot connect to host';
			return null;
		}

		stream_set_timeout($fp, 3);

		$data = '';
		while (!feof($fp))
		{
			$data .= fread($fp, 8192);
		}
		fclose( $fp );

		//передаём содержимое xml файла расширению SimpleXML
		$res = simplexml_load_string( $data );

		return (string)$res->TestString;
	}

	function GetPrice( $Number, $Brand = null)
	{
		$setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_active_admin')->find();
		$setting = $setting->value;
		if (!$setting) return null;
		// Проценка детали

		$Number_url = urlencode( $Number );

		if ( $Brand !== null )
		{
			$Brand_url = urlencode( $Brand );
			$url = "{$this->adresses[0]}?act=GetPrice&usr_login={$this->usr_login}&usr_passwd={$this->usr_passwd}&Currency=USD&Number=$Number_url&Brand=$Brand_url&Currency=USD";
		}
		else
		{
			$url = "{$this->adresses[0]}?act=GetPrice&usr_login={$this->usr_login}&usr_passwd={$this->usr_passwd}&Currency=USD&Number=$Number_url&Currency=USD";
		}

		$fp = @fopen($url, 'r');

		if ( $fp === false )
		{
			$this->error = true;
			$this->error_string = 'Cannot connect to host';
			return null;
		}

		stream_set_timeout($fp, 3);

		$data = '';
		while (!feof($fp))
		{
			$data .= fread($fp, 8192);
		}

		fclose( $fp );

		libxml_use_internal_errors(true);
		$res = simplexml_load_string( $data );
		if (!$res) return array();

		$details = array();

//		var_dump($res->Detail); exit();
		foreach ($res->Detail as $detail_info )
		{
			$detail_info_new = array();
			$detail_info_new['Brand'] = iconv('UTF-8', 'WINDOWS-1251', (string)$detail_info->Brand);
			$detail_info_new['Number'] = iconv('UTF-8', 'WINDOWS-1251', (string)$detail_info->Number);
			$detail_info_new['Name'] = iconv('UTF-8', 'UTF-8', (string)$detail_info->Name);
			$detail_info_new['Price'] = (float)$detail_info->Price;
			$detail_info_new['Currency'] = (string)$detail_info->Currency;
			$detail_info_new['Quantity'] = (string)$detail_info->Quantity;
			$detail_info_new['SupplierCode'] = (string)$detail_info->SupplierCode;
			$detail_info_new['Weight'] = (float)$detail_info->Weight;
			$detail_info_new['DeliveryTime'] = (int)$detail_info->DeliveryTime;
			$detail_info_new['DeliveryType'] = (string)$detail_info->DeliveryType;
			$detail_info_new['ReturnFlag'] = (string)$detail_info->RestoredFlag;
			$details[] = $detail_info_new;
		}

		return $details;
	}

	function GetPriceWithCrosses( $Number, $BrandID = null, $setting = 0)
	{
//		$setting = ORM::factory('Setting')->where('code_name', '=', 'tekhnomir_active')->find();
//		if($setting && $setting->value != 1) return null;
		if (!$setting) return null;
		// Проценка детали

		$Number_url = urlencode( $Number );

		if ( $BrandID !== null )
		{
			$url = "{$this->adresses[0]}?act=GetPriceWithCrosses&usr_login={$this->usr_login}&usr_passwd={$this->usr_passwd}&Currency=USD&PartNumber=$Number_url&BrandId=$BrandID&Currency=USD";

		}
		else
		{
			$url = "{$this->adresses[0]}?act=GetPriceWithCrosses&usr_login={$this->usr_login}&usr_passwd={$this->usr_passwd}&Currency=USD&PartNumber=$Number_url&Currency=USD";
		}
//		echo $url."\n";
//		exit();
		$fp = @fopen($url, 'r');

		if ( $fp === false )
		{
			$this->error = true;
			$this->error_string = 'Cannot connect to host';
			return null;
		}

		stream_set_timeout($fp, 3);

		$data = '';
		while (!feof($fp))
		{
			$data .= fread($fp, 8192);
		}

		fclose( $fp );

		try {
			$data = iconv('windows-1251', 'UTF-8//IGNORE', $data);
		} catch (Exception $e) {
			try {
				$data = iconv('UTF-8', 'windows-1251//IGNORE', $data);
				$data = iconv('windows-1251', 'UTF-8//IGNORE', $data);
			} catch (Exception $e) {}
		}
		
		$details = array();
		$details['brands'] = null;
		$details['prices'] = null;
		
		try {
			$res = simplexml_load_string( $data );
		} catch (Exception $e) {
			return $details;
		}
		
		if($res->QueryStatus->QueryStatusCode == 1) {
			if($res->Producers->Producer) {
				foreach ($res->Producers->Producer as $detail_brand )
				{
					//var_dump($detail_brand);
					$detail_brand_new = array();
					$detail_brand_new['Brand'] = iconv('UTF-8', 'WINDOWS-1251//IGNORE', (string)$detail_brand->Brand);
					$detail_brand_new['BrandId'] = (int)$detail_brand->BrandId;
					$detail_brand_new['PartDescriptionRus'] = iconv('UTF-8', 'UTF-8//IGNORE', (string)$detail_brand->PartDescriptionRus);
					$detail_brand_new['PartDescriptionRus'] = iconv('UTF-8', 'WINDOWS-1251//IGNORE', $detail_brand_new['PartDescriptionRus']);
					$details['brands'][] = $detail_brand_new;
				}
			}
		} elseif($res->QueryStatusCode == 0) {
			if($res->Prices->Price) {
				foreach ($res->Prices->Price as $detail_info )
				{
					$detail_info_new = array();
					$detail_info_new['Brand'] = iconv('UTF-8', 'WINDOWS-1251', (string)$detail_info->Brand);
					$detail_info_new['Number'] = iconv('UTF-8', 'WINDOWS-1251', (string)$detail_info->PartNumber);
					$detail_info_new['Name'] = iconv('UTF-8', 'UTF-8//IGNORE', (string)$detail_info->PartDescriptionRus);
					$detail_info_new['Name'] = iconv('UTF-8', 'WINDOWS-1251//IGNORE', $detail_info_new['Name']);
					$detail_info_new['Price'] = (float)$detail_info->Price;
					$detail_info_new['Currency'] = (string)$detail_info->Currency;
					$detail_info_new['Quantity'] = (string)$detail_info->Quantity;
					$detail_info_new['SupplierCode'] = (string)$detail_info->PriceLogo;
					$detail_info_new['Weight'] = (float)$detail_info->Weight;
					$detail_info_new['DeliveryTime'] = (int)$detail_info->DeliveryDays;
					$detail_info_new['DeliveryType'] = (string)$detail_info->DeliveryType;
					$detail_info_new['ReturnFlag'] = (string)$detail_info->ReturnFlag;
					$details['prices'][] = $detail_info_new;
				}
			}
		}

		return $details;
	}
	// добавить в корзину
	function BasketAddPos( $ProdStr, $SupCode, $Code, $Qty ) //
	{
		$Code = urlencode($Code);
		$ProdStr = urlencode($ProdStr);
		$url = "{$this->adresses[0]}?act=BasketAddPos&usr_login={$this->usr_login}&usr_passwd={$this->usr_passwd}&ProdStr=$ProdStr&SupCode=$SupCode&Code=$Code&Qty=$Qty";

		$user_id = Auth::instance()->get_user()->id;

		Log::instance()->add(Log::NOTICE, "$url $user_id");

		$fp = @fopen($url, 'r');

		if ( $fp === false )
		{
			$this->error = true;
			$this->error_string = 'Cannot connect to host';
			return null;
		}

		stream_set_timeout($fp, 3);

		$data = '';
		while ( !feof($fp) )
		{
			$data .= fread( $fp, 8192 );
		}

		fclose( $fp );

		libxml_use_internal_errors(true);
		$res = simplexml_load_string( $data );

		if (!$res) return array();

		$result	= $res->Status->Code;


		if($result == 100)
		{
			return true;
		}
		else
		{
			return false;
		}

	}
	function BasketList( $Price, $SupCode, $Code, $Qty ) //
	{
		$Code = Article::get_short_article($Code);

		$url = "{$this->adresses[0]}?act=BasketList&usr_login={$this->usr_login}&usr_passwd={$this->usr_passwd}";

		$user_id = Auth::instance()->get_user()->id;
		Log::instance()->add(Log::NOTICE, "$url $user_id");

		$fp = @fopen($url, 'r');

		if ( $fp === false )
		{
			$this->error = true;
			$this->error_string = 'Cannot connect to host';
			return null;
		}

		stream_set_timeout($fp, 3);

		$data = '';
		while ( !feof($fp) )
		{
			$data .= fread( $fp, 8192 );
		}

		fclose( $fp );

		libxml_use_internal_errors(true);
		$res = simplexml_load_string( $data );

		if (!$res) return array();

		if(Article::get_short_article($res->Positions->Position->Code) == Article::get_short_article($Code) AND $res->Positions->Position->SupCode == $SupCode AND $res->Positions->Position->Qty == $Qty AND $res->Positions->Position->Price <= $Price*1.05)
		{
			return true;
		}
		else
		{
			return false;
		}

	}
	function BasketClear()
	{
		$url = "{$this->adresses[0]}?act=BasketClear&usr_login={$this->usr_login}&usr_passwd={$this->usr_passwd}";

		$user_id = Auth::instance()->get_user()->id;
		Log::instance()->add(Log::NOTICE, "$url $user_id");

		$fp = @fopen($url, 'r');
		if ( $fp === false )
		{
			$this->error = true;
			$this->error_string = 'Cannot connect to host';
			return null;
		}

		stream_set_timeout($fp, 3);

		$data = '';
		while ( !feof($fp) )
		{
			$data .= fread( $fp, 8192 );
		}

		fclose( $fp );

		libxml_use_internal_errors(true);
		$res = simplexml_load_string( $data );

	}
	function BasketMakeOrder( $order_number ) //
	{
		//$order_number = 12494;
		$url = "{$this->adresses[0]}?act=BasketMakeOrder&usr_login={$this->usr_login}&usr_passwd={$this->usr_passwd}&OrderNum=$order_number";

		$user_id = Auth::instance()->get_user()->id;
		Log::instance()->add(Log::NOTICE, "$url $user_id");

		$fp = @fopen($url, 'r');

		if ( $fp === false )
		{
			$this->error = true;
			$this->error_string = 'Cannot connect to host';
			return null;
		}

		stream_set_timeout($fp, 3);

		$data = '';
		while ( !feof($fp) )
		{
			$data .= fread( $fp, 8192 );
		}

		fclose( $fp );

		libxml_use_internal_errors(true);
		$res = simplexml_load_string( $data );

		if (!$res) return array();

		//$details = array();
		$result	= $res->Status->Code;

		if($result == 100)
		{
			return $res->OrderId;
		}
		else
		{
			return false;
		}

	}
	function GetOrderPositions( $OrderId ) //
	{
		//$OrderId = 1836059;
		$url = "{$this->adresses[0]}?act=GetOrderPositions&usr_login={$this->usr_login}&usr_passwd={$this->usr_passwd}&order_id=$OrderId";
		$fp = @fopen($url, 'r');

		if ( $fp === false )
		{
			$this->error = true;
			$this->error_string = 'Cannot connect to host';
			return null;
		}

		stream_set_timeout($fp, 3);

		$data = '';
		while ( !feof($fp) )
		{
			$data .= fread( $fp, 8192 );
		}

		fclose( $fp );

		libxml_use_internal_errors(true);
		$res = simplexml_load_string( $data );

		if (!$res) return array();

		$details = array();
		//$result	= $res->PositionsList->Position;
		foreach ($res->PositionsList->Position as $result)
		{
			$detail_info_new = array();
			$detail_info_new['Brand'] = iconv('UTF-8', 'WINDOWS-1251', (string)$result->Producer);
			$detail_info_new['Number'] = iconv('UTF-8', 'WINDOWS-1251', (string)$result->PartNumber);
			$detail_info_new['Quantity'] = (int)$result->Quantity;
			$detail_info_new['StateId'] = (int)$result->StateId;
			$details[] = $detail_info_new;
		}
//		var_dump($details);
//		exit();

		if(count($details)>0)
		{
			return $details;
		}
		else
		{
			return false;
		}
	}

	function GetDetailInfo_ByDetailNum( $Number )
	{
		// Получение информации по детали

		$Number_url = urlencode( $Number );

		$url = "{$this->adresses[0]}?act=GetDetailInfo_ByDetailNum&usr_login={$this->usr_login}&usr_passwd={$this->usr_passwd}&Number=$Number_url";

		$fp = @fopen($url, 'r');

		if ( $fp === false )
		{
			$this->error = true;
			$this->error_string = 'Cannot connect to host';
			return null;
		}

		stream_set_timeout($fp, 3);

		$data = '';
		while ( !feof($fp) )
		{
			$data .= fread( $fp, 8192 );
		}

		fclose( $fp );



		$res = simplexml_load_string( $data );

		$details = array();
		foreach ( $res->Detail as $detail_info )
		{
			$detail_info_new = array();

			$detail_info_new['Name'] = iconv('UTF-8', 'WINDOWS-1251', (string)$detail_info->Name);
			$detail_info_new['Brand'] = iconv('UTF-8', 'WINDOWS-1251', (string)$detail_info->Brand);
			$detail_info_new['StateName'] = iconv('UTF-8', 'WINDOWS-1251', (string)$detail_info->StateName);
			$detail_info_new['ClientComment'] = iconv('UTF-8', 'WINDOWS-1251', (string)$detail_info->ClientComment);
			$detail_info_new['AdminComment'] = iconv('UTF-8', 'WINDOWS-1251', (string)$detail_info->AdminComment);
			$detail_info_new['Currency'] = (string)$detail_info->Currency;
			$detail_info_new['Price'] = (float)$detail_info->Price;

			$details[] = $detail_info_new;
		}

		return $details;
	}

}



?>
