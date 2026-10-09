<?php
defined('BASEPATH') OR exit('No direct script access allowed');
function dropdown($name)
	{
		switch ($name) 
		{
		case "gender":
			return array('Male','Female','Other');
		    break;
		case "marital":
			return array('Married','Unmarried','Widowed');
		    break;
		case "nationality":
			return array('Indian','Other');
		    break;	
		case "status":
			return  array("ACTIVE","CONFIRMED","CLOSED");
		    break;
		case "category":
			return array('General','OBC','SC','ST');
		    break;
		case "religion":
			return array('Hindu','Muslim','Sikh','Christian','Other');
		    break;
		case "pay_mode":
			return array('Cash','Cheque','E-payment');
		    break;
		case "month":
			return array('April','May','June','July','August','September','October','November','December','January','February','March');
		    break;
		case "eligibility":
			return array('8th Pass','10th Pass','12th Pass','Graduate','Post Graduate');
		    break;	
		case "qualification":
			return array('Short Terms','Certificate Course','Diploma Course','Long Term');
		    break;
		case "followup":
			return  array("Call","SMS","Face To Face");
		    break;	
		case "course":
			return array("BCA","BBA","BLIS","BJMC","CCC");
		    break;	
		case "nature":
			return  array('INCOME','EXPENSE');
		    break;	
		case "source":
			return array('Newspaper', 'Internet', 'Hoarding', 'Handbill', 'Friends', 'Audio', 'Other');
		    break;	
		case "otp":
			return array('YES','NO');
		    break;
		case "general_status":
			return array('ACTIVE','PENDING','BLOCKED');
		    break;
		case "branch_user":
			return array('CLIENT'=>'Branch Admin','USER'=>'Staff');
			break;
			case "unit":
			return array('Months','Year(s)');
				break;
		default:
			echo "Sorry! This list is not available.";
		}
	}
function config($name)
	{
		switch ($name) 
		{
		case "app_name":
			return "brainware";
		    break;	
		case "sc_name":
			return "brainware computer academy";
		    break;
		 break;	
		case "sc_code":
			return "Brainware Computer Academy Pvt. Ltd.";
		    break;
		case "sc_address1":
			return "Dotnet Campus, Madho Bihari Lane, Chapra";
		    break;
		case "sc_address2":
			return "";
		    break;
		case "sc_district":
			return "Saran";
		    break;
		case "sc_state":
			return "Bihar";
		    break;
		case "sc_pin":
			return "841301";
		    break;
		case "sc_contact1":
			return "+91 9835 4266 91";
		    break;
		case "sc_contact2":
			return "";
		    break;	
		case "sc_email":
			return "info@brainwarecomputer.co.in";
		    break;
		case "sc_url":
			return "";
		    break;	
		case "noreply":
			return "Brainware Computer Academy Pvt. Ltd. <noreply@brainwarecomputer.co.in>";
		    break;
		case "sc_dir_name":
			return "";
		    break;
		case "director_photo":
			return "no_image.jpg";
		    break;
		}
	}
	
function curr_session()
{
if(date('m')<=3)
	{
	 return $sesion=date('Y',strtotime('-1 year'))." - ".date('Y');//table name
	}
	else
	{ 
	 return $sesion=date('Y')." - ".date('Y',strtotime('+1 year'));//table name
	}
}	
function gradeshow($ap)
		{
		if($ap< 100 && $ap >=85) { $grade ="A+";} 
		elseif($ap <85 && $ap >= 75) { $grade ="A";} 
		elseif($ap <75 && $ap >= 60) { $grade ="B";} 
		elseif($ap <60 && $ap >= 50) { $grade ="C";} 
		elseif($ap <50 && $ap >= 40) { $grade ="C";} 
		else { $grade ="F";} 
		
		return $grade;
		}
function my_mail($to,$mail_subject,$msg)
{
		$from = config('sc_email');
		$subject = "<h3 align='center'>".$mail_subject."</h3>";
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Create email headers
		$headers .= 'From: '.$from."\r\n".
			'Reply-To: '.$from."\r\n" .
			'X-Mailer: PHP/' . phpversion();
		$foot = "<br><br><hr><p style='float:left'><h4>Thanks & Regards</h4><p style='color:#000;font-style:italic'>". config('sc_name')."</p>";
		// Compose a simple HTML email message
		$message = "<html><body>";
		$message .= '<table width="690px"  Cellpadding="5" cellspacing="0" align="center"  style="border:solid 1px #eee">';
		$message .= "<tr style='background-color:#eeeeee6b;'><th><img src=". base_url()."web/images/mail_header.png></th></tr>" ;
		$message .= '<tr><td><div style="color:#1d262f;font-size:16px;font-family:arial;text-align:justify;"><div style="padding:10px">'.$subject.$msg . $foot.'</div></div></td></tr>';
		$message .= "<tr><td><img src='".base_url()."web/images/mail_footer.png'></td></tr>";
		$message .= '</table></body></html>';
		
		//Sending email
		if(mail($to, $mail_subject, $message, $headers)){
			return 1;
		} else{
			return 0;
		}
}
function sendsms($number,$sms)
		{
		$auth_key ="56a14784b802ba359dd89308b2fa345"; 
		$no =urlencode('91'.$number);
		$msg = substr(urlencode($sms),0,340);
		$sender_id = "BRNWRE";
		$sms_url = "http://msg.morg.in/rest/services/sendSMS/sendGroupSms?authkey=$auth_key&mobiles=$no&message=$msg&sender=$sender_id&route=4&country=0&unicode=1";
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$sms_url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		$res= curl_exec($ch);
		curl_close($ch);
		}
function sendsms_res($number,$sms)
		{
		$auth_key ="56a14784b802ba359dd89308b2fa345"; 
		$no =urlencode($number);
		$msg = substr(urlencode($sms),0,340);
		$sender_id = "BRNWRE";
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, "http://msg.morg.in/rest/services/sendSMS/sendGroupSms?authkey=$auth_key&mobiles=$no&message=$msg&sender=$sender_id&route=4&country=0&unicode=1");
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		$res= curl_exec($ch);
		curl_close($ch);
		}
		
function xss_clean($data)
{
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z])[\x00-\x20]=[\x00-\x20]([`\'"])[\x00-\x20]j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z])[\x00-\x20]=([\'"])[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z])[\x00-\x20]=([\'"])[\x00-\x20]-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]=[\x00-\x20][`\'"].?expression[\x00-\x20]\([^>]+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]=[\x00-\x20][`\'"].?behaviour[\x00-\x20]\([^>]+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]=[\x00-\x20][`\'"].?s[\x00-\x20]c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]:[^>]+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</\w+:\w[^>]+>#i', '', $data);

        do
        {
                // Remove really unwanted tags
                $old_data = $data;
                $data = preg_replace('#</(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]+>#i', '', $data);
        }
        while ($old_data !== $data);

        // we are done...
        return $data;
}

function post_clean($arr_data)
{
	foreach( $arr_data as $data)
	{
	    
	$key = array_search ($data, $arr_data);
    	if(is_array($data))
    	{
    	    post_clean($data);
    	}
    	else{
    	$arr_data[$key] =xss_clean($data);
    	}
	}
	return $arr_data;
}



?>