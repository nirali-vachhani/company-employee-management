<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Penuel
 * Date: 30/5/13
 * Time: 1:33 PM
 * To change this template use File | Settings | File Templates.
 */

class Helpers_Mailer_Mandrill extends Helpers_Mailer_Abstract
{

    private $apiKey = '';
    private $tags = '';
    private $metadata = '';
    private $requestData = '';
    private $subAccount = '';


    public function setSubAccount($_subAccount)
    {
    	$this->subAccount = $_subAccount;
    }
    
    public function getSubAccount()
    {
    	return $this->subAccount;
    }
    
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    public function setMetaData($metadata)
    {
        if(!is_array($metadata)) trigger_error('meta data must be of type array with key and value pairs', E_USER_ERROR);
        else     $this->metadata = $metadata;
    }

    public function init()
    {
        parent::init();
    }

    public function getRequestData()
    {
        return $this->requestData;
    }

    public function send()
    {
        //echo "Sending..";

        $toEmails = json_encode($this->getRecipients());
        $bccEmails = "";
        $tmp  = $this->getBccEmails();

        if(count($tmp)>0) $bccEmails = $tmp[0];


        $attachments = $this->getAttachments();

        $mandrill_attachments = array();
        $mandirll_attachments_json = '';

        foreach($attachments as $attachment)
        {
            $file = $attachment[0];

            if(!empty($file) && file_exists($file))
            {
                $handle = fopen($file, "rb");
                $content = fread($handle, filesize($file));
                fclose($handle);

                if($content!==FALSE)
                {
                    $type = '';
                    if(function_exists('finfo_open'))
                    {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $type = finfo_file($finfo, $file);
                    }
                    else{
                        $type = mime_content_type($file);
                    }

                    $base64data=base64_encode($content);
                    array_push($mandrill_attachments, array(
                        'content' => $base64data,
                        'name' => basename($file),
                        'type' => $type
                    ));
                }
            }
        }

        if(!empty($mandrill_attachments))
        {
            $mandirll_attachments_json = json_encode($mandrill_attachments);
        }

        $htmlBody = preg_replace('/\t/', '', $this->getHtmlBody());
        $htmlBody = stripslashes($htmlBody);
        $htmlBody = preg_replace('/\r\n/', '', str_replace('"', '\"', $htmlBody));
		
		
        $plainText = preg_replace('/\t/', '', $this->getTextBody());
        $plainText = stripslashes($plainText);
        $plainText = preg_replace('/\r\n/', '',str_replace('"', '\"',  $plainText));
		
        

	//echo "//mandrill key ". $this->apiKey ."\n";

        $data = '{
            "key":"'. $this->apiKey .'",
            "message":{
                "html": "'.$htmlBody.'",
                "text": "'.$plainText.'",
                "subject": "'. $this->getSubject() .'",
                "from_email": "'. $this->getFromEmail() .'",
                "from_name": "'. $this->getFromName() .'",
                "to": '. $toEmails .',
                "headers":
                {
                    "Reply-To": "'. $this->getFromEmail() .'"                    
                },' ."\n";

                if(!empty($mandirll_attachments_json))
                {
                    $data .= '"attachments":'. $mandirll_attachments_json .',' ."\n";
                }
                if(!empty($this->tags))
                {
                    $data .= '"tags":'. json_encode(explode(",",$this->tags)) .','. "\n";
                }
                if(!empty($this->metadata))
                {
                    $data .= '"metadata":'. json_encode($this->metadata) .','. "\n";
                }
				$subAccount = $this->getSubAccount(); 
				if(!empty($subAccount))
                {

                    $data .= '"subaccount":"'. $this->getSubAccount() .'",'."\n";
                }				
                $data .= '"important": false,
                "track_opens": true,
                "track_clicks": true,
                "auto_text": null,
                "auto_html": null,
                "inline_css": null,
                "url_strip_qs": null,
                "preserve_recipients": false,
                "bcc_address": "'. $bccEmails .'",
                "merge": true,
                "tracking_domain": null,
                "signing_domain": "'. $this->getSigningDomain() .'"
            },
            "async": false
        }';

       // echo $data;
      //  die();
		// print_r(json_decode($data));

        $this->requestData = $data;

        $ch = curl_init('https://mandrillapp.com/api/1.0/messages/send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec($ch);
        curl_close($ch);

        $arr_output = json_decode($output);
        
       // var_dump($arr_output);

       // $response = $arr_output;
        if(is_array($arr_output)){
            foreach($arr_output as $response){
                if($response->status == "rejected")
                {
                    $this->addError($response->email ." rejected : ". $response->reject_reason);
                }
                elseif($response->status == "invalid")
                {
                    $this->addError($response->email ." invalid : ". $response->reject_reason);
                }
            }
        }
        else
        {

            if($arr_output->status == "error")
            {
                $this->addError($arr_output->name .": ". $arr_output->message);
            }

        }
        
       if($this->hasErrors())
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}
