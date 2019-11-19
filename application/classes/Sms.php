<?php defined('SYSPATH') or die('No direct script access.');

class Sms {
	public static function send($text, $description, $recipient) {
		$recipient = '38'.preg_replace('([^0-9])', '', $recipient);
		$text = htmlspecialchars($text);
		$description = htmlspecialchars($description);
		$start_time = "AUTO"; //��������� ����������
		$end_time = "AUTO"; // ������������� ���������� ��������
		$rate = 1; // �������� �������� ��������� (1 = 1 ��� ������). ��������� ��� ��������� ������������ ������ � ������������ ���������.
		$lifetime = 4; // ���� ����� ��������� 4 ����
		$source = 'InfoCentr'; // Alfaname
		$user = '380986405500';
		$password = '950667817282';

		$myXML 	 = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$myXML 	.= "<request>";
		$myXML 	.= "<operation>SENDSMS</operation>";
		$myXML 	.= '		<message start_time="'.$start_time.'" end_time="'.$end_time.'" lifetime="'.$lifetime.'" rate="'.$rate.'" desc="'.$description.'" source="'.$source.'">'."\n";
		$myXML 	.= "		<body>".$text."</body>";
		$myXML 	.= "		<recipient>".$recipient."</recipient>";
		$myXML 	.=  "</message>";
		$myXML 	.= "</request>";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERPWD , $user.':'.$password);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, 'http://sms-fly.com/api/api.php');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Accept: text/xml"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $myXML);
		$response = curl_exec($ch);
		curl_close($ch);
	}
	public static function send_to_many($text, $description, $recipients) {
		$text = htmlspecialchars($text);
		$description = htmlspecialchars($description);
		$start_time = "AUTO"; //��������� ����������
		$end_time = "AUTO"; // ������������� ���������� ��������
		$rate = 120; // �������� �������� ��������� (1 = 1 ��� ������). ��������� ��� ��������� ������������ ������ � ������������ ���������.
		$lifetime = 4; // ���� ����� ��������� 4 ����
		$source = 'InfoCentr'; // Alfaname
		$user = '380986405500';
		$password = '950667817282';

		$myXML 	 = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$myXML 	.= "<request>";
		$myXML 	.= "<operation>SENDSMS</operation>";
		$myXML 	.= '		<message start_time="'.$start_time.'" end_time="'.$end_time.'" lifetime="'.$lifetime.'" rate="'.$rate.'" desc="'.$description.'" source="'.$source.'">'."\n";
		$myXML 	.= "		<body>".$text."</body>";
		foreach($recipients as $recipient) {
			$recipient = '38'.preg_replace('([^0-9])', '', $recipient);
			$myXML 	.= "		<recipient>".$recipient."</recipient>";
		}
		$myXML 	.=  "</message>";
		$myXML 	.= "</request>";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERPWD , $user.':'.$password);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, 'http://sms-fly.com/api/api.php');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Accept: text/xml"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $myXML);
		$response = curl_exec($ch);
		curl_close($ch);
	}
}
