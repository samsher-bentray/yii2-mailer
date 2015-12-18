<?php
namespace samsher\mailer;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\swiftmailer\Mailer;
use yii\helpers\BaseFileHelper;


/**
 * Mail represents the class for sending email by using both php and smtp mail in yii2.
 * Public variables are $_Mail_Type,$class,$host,$username,$emailPassword,$port_num,$encType
 * Public function are init(),configSet(),setMailType(),setSMTPPort(),setHost(),setUsername(),setPassword(),setEncType()
 * All the function are called for mail setting dynamically except init().
 * @author Samsher Bahadur Rana<samsher@bentraytech.com>
 */        
class Mail extends Mailer{
    
    /**
     * @var string the mail type is represented
     */
    public $_Mail_Type="PHPMAIL";

    /**
     * @var string the class name of swiftmailer
     */
    public $class='Swift_SmtpTransport';

    /**
     * @var string the SMTP server address.
     */
    public $host=NULL;

    /**
     * @var string the username
     */
    public $username=NULL;

    /**
     * @var string the password.
     */
    public $emailPassword=NULL;
    
    /**
     * @var string the protocol for encryption.
     */
    public $encType=NULL;

    /**
     * @var string the port.
     */
    public $port_num=NULL;
    
    /**
     * @var string the SMTP server address.
     */
    public $attachments=NULL;
    


    /**
     * A public function for initialization of configuration
     * @param No parameters
     */ 
    public function init()
    {
       $this->configSet();
    }

    /**
     * Represents the main configuration function for mail setting.
     */
    public function configSet()
    {
        $configArray=[
                        'class' =>$this->class,
                        'host' =>$this->host,
                        'username' =>$this->username,
                        'password' =>$this->emailPassword,
                        'port' =>$this->port_num,
                        'encryption' =>$this->encType,
                      ];
         $this->setTransport($configArray);
    }  

        
    /**
     * Function for setting mail type.
     * <li><b>Default setting:</b> PHP mail.</li>
     */    
    public function setMailType($mailType)
    {
        $this->_Mail_Type = strtoupper($mailType);
       
        switch(strtoupper($this->_Mail_Type))
        {
           
           
            case 'SMTP':
               
                $this->class='Swift_SmtpTransport';
                $this->configSet();
                break;
            
        
        }
    }
    
    /**
     * Function for setting of port number.
     * <li><b>Default setting:</b> NULL value.</li>
     */
    public function setSMTPPort($port_num)
    {
       
        $this->port_num=$port_num;
        $this->configSet();
                
    }
    
    /**
     * Function for setting of SMTP server address.
     * <li><b>Default setting:</b> NULL value</li>
     */
    public function setHost($mailHost){
        $this->host=$mailHost;
        $this->configSet();
    }
    
    /**
     * Function for setting of username.
     * <li><b>Default setting:</b> NULL value</li>
     */
    public function setUname($username){
         $this->username=$username;
        $this->configSet();
    }
    
    /**
     * Function for setting of password.
     * <li><b>Default setting:</b> NULL value</li>
     */
    public function setPassd($emailPassword){
        $this->emailPassword=$emailPassword;
        $this->configSet();
    }
    
    /**
     * Function for setting of encryption type.
     */
    public function setEncType($encType){
      
       $this->encType=$encType;
        $this->configSet();
    }
    

   
    /**
     * Returns a boolean.
     * <li></li>
     * <li><b>Syntax:</b></li>
     * SendMail($from, $to,$subject,$message_body,$cc=null,$bcc=null,$actualFile=null)
     * <li></li>
     * <li><b>$to</b> contains a string of multiple email address with comma separated format or null value.</li>
     * <li><b>$cc</b> contains a string of multiple email address with comma separated format or null value.</li>
     * <li><b>$bcc</b> contains a string of multiple email address with comma separated format or null value.</li>
     * <li><b>$actualFile</b> must be as array of UploadedFile objects with <b>multiple file upload</b>.</li>
     * <li><b>For attachment</b>you can save attachments manually by sending UploadedFile objects to SaveAttach function.</li>
     * <li>Then You can call SendMail function with comma separated value with attachment locations.</li>
     */ 
    public function SendMail($from, $to,$subject,$message_body,$cc=null,$bcc=null,$actualFile=null)
    {
        
       
        if(is_array ($actualFile )){
           
                if($actualFile)
                {
                    
                    $attach = $actualFile;

                }
        }
        
        if($this->_Mail_Type=="PHPMAIL")
        {
            //creating boundary with unique id
            $boundary = md5(uniqid(time()));

            //Building the headers for attachment and html
            $headers = "From: $from\r\n";
            $headers .= "To: $to\r\n";
            $headers .= "Reply-To: $from\r\n";
            $headers .= "Return-Path: $from\r\n";
            $headers .= "CC: $cc\r\n";
            $headers .= "BCC: $bcc\r\n"; 

            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n\r\n";
            $headers .= "--".$boundary."\r\n";
            $headers .= "Content-type:text/html; charset=iso-8859-1\r\n";
            $headers .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $headers .= $message_body."\r\n\r\n";
            
            if(isset($attach)){
                //preparing attachments
                foreach ($attach as $att_location){
                    // read file into $data var
                    $headers .= "--".$boundary."\r\n";//boundary for separating multiple attachments
                    $file = fopen($att_location, "rb");
                    $data = fread($file,  filesize( $att_location ) );
                    fclose($file);
                    $content = chunk_split(base64_encode($data));
                    $headers .= "Content-Type: application/octet-stream; name=\"".basename($att_location)."\"\r\n";
                    $headers .= "Content-Transfer-Encoding: base64\r\n";
                    $headers .= "Content-Disposition: attachment; filename=\"".basename($att_location)."\"\r\n\r\n";
                    $headers .= $content."\r\n\r\n";

                }
            }


            $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
            $headers .= "--".$boundary."--";//boundary closing 

            // send mail
            return mail( NULL, $subject,NULL, str_replace("\r\n","\n",$headers) ) ;
            
        
        }
        else //If $this->_Mail_Type=="SMTP" or other type, use SMTP settings
        {
            $send = $this->compose();
            $send->setFrom($from);
            $send->setTo($to==NULL?Null:explode(',', $to));
            $send->setCc($cc==NULL?Null:explode(',', $cc));
            $send->setBcc($bcc==NULL?Null:explode(',', $bcc));
            $send->setSubject($subject);
            $send->setHtmlBody($message_body);
            $j=0;
            if(isset($attach)){
                foreach ($attach as $val){
                    $send->attach($attach[$j]);
                    $j++;
                }
            }  
            $send->send();
            
            if($send){
            
            return true;    
            }
            
        }
           
        
       
                    
    }
    
    /**
     * Returns a array of location of attachment files.
     * <li></li>
     * <li><b>Syntax:</b></li>
     * <li>SaveAttach($actualFile)</li>
     * <li></li>
     * <li><b>$actualFile</b>Arguement must be UploadedFile objects</li>
     */ 
    public function SaveAttach($actualFile){
        /*Initializing empty array for store atttachment file location with attachment name*/
        $attach=[];
        $attLocation = Yii::$app->basePath.'/web/EmailAttach/';
        BaseFileHelper::createDirectory($attLocation);
        $loc_count=0;
        foreach ($actualFile as $file)
            {
                $attLocation = $attLocation.rand().$file->name;
                $file->saveAs($attLocation);
                $attach []= $attLocation;
                $loc_count++;
            }
            
            return $attach;
    }
    /**
     * Returns a boolean.
     * <li></li>
     * <li><b>Syntax:</b></li>
     * <li>DeleteAttach($attach)</li>
     * <li></li>
     * <li><b>$attach</b>Arguement must be an array of attachment location</li>
     */ 
    public function DeleteAttach($attach){
        
        if(is_array ($attach ))
        {
           
            foreach ($attach as $file)
            {
                if (file_exists($file)){
                    
                    @unlink($file);
                }
                
            }
        }
        return TRUE;
    }
  
}
?>
