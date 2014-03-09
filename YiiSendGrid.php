<?php
/**
* YiiSendGrid class file.
*
* @author Gustavo Salomé Silva <gustavonips@gmail.com>
*/

/**
* YiiSendGrid is an application component used for sending email through sendgrid.
*
* You may configure it as below.  Check the public attributes and setter
* methods of this class for more options.
* <pre>
* return array(
* 	'components' => array(
* 		'sendgrid' => array(
* 			'class' => 'ext.yii-sendgrid.YiiSendGrid',
* 			'username'=>'sendgridUsername',//replace with your actual username
* 			'password'=>'myP4s$w0rd',//replace with your actual password
* 			'viewPath' => 'application.views.mail',
* 			'enableLog' => YII_DEBUG,//wheter to log the emails sent
* 			'dryRun' => false,//if enabled, it won't actually send the emails, only log
* 			'disableSslVerification'=>true,// {@link https://github.com/sendgrid/sendgrid-php#ignoring-ssl-certificate-verification}
* 		),
* 		...
* 	)
* );
* </pre>
* 
* Example usage:
* <pre>
* $email = Yii::app()->sendgrid->createEmail();
* $email->setHtml('<p>Message content here with HTML</p>')
* 	->setSubject('My Subject')
* 	->addTo('johnDoe@domain.com')
*   ->setFrom(Yii::app()->params['adminEmail']);
* Yii::app()->mail->send($email);
* </pre>
* or just
* <pre>
* $email = Yii::app()->sendgrid->createEmail($htmlBody,$subject,$to,$from);
* Yii::app()->mail->sendgrid->send($email);
* </pre>
* 
* @property SendGrid $mailer
* @property string $username
* @property string $password
*/
class YiiSendGrid extends CApplicationComponent
{
	/**
	 * whether to trace emails sent using {@link Yii::log()}. Uses CLogger::LEVEL_TRACE
	 * @var bool Defaults to YII_DEBUG. 
	 */
	public $enableLog = YII_DEBUG;
	
	/**
	 * whether to disable sending mail. If set to true, it will only log the email
	 * @var bool Defaults to false.
	 */
	public $dryRun = false;
	
	/**
	 * the path or alias to the location where mail views are stored.
	* @var string Defaults to 'application.views.mail'.
	*/
	public $viewPath='application.views.mail';
	
	/**
	 * wheter to disable ssl verification
	 * @boolean defaults to true
	 */
	public $disableSslVerification=true;

	/**
	* @var SendGrid SendGrid instance
	*/
	private $_mailer;
	
	/**
	 * sendgrid username
	 * @var string
	 */
	private $_username;
	
	/**
	 * sendgrid password
	 * @var string
	 */
	private $_password;
	
	/**
	 * YiiSendGridMail instance
	 * @var YiiSendGridMail
	 */
	private $_mail;
	
	/**
	 * wheter the librarys are registered, so we are able to create many instances and register only once
	 * @var boolean
	 */
	private static $_registered=false;
	
	/**
	 * list of last error messsages
	 * @var string[]
	 */
	public $lastErrors=array();

	/**
	 * Calls the {@link registerScripts()} method.
	 */
	public function init()
	{
		self::registerLibs();
		return parent::init();	
	}
	
	/**
	* Gets the SendGrid adapter instance
	* @return SendGrid
	*/
	public function getMailer()
	{
		if ($this->_mailer===null)
		{
			$this->_mailer=new SendGrid($this->getUsername(), $this->getPassword(), array(
				"turn_off_ssl_verification" => $this->disableSslVerification
			));
		}
		return $this->_mailer;
	}
	/**
	 * get sendgrid username
	 * @throws Exception if username is not defined
	 * @return string
	 */
	public function getUsername()
	{
		if($this->_username===null)
			throw new Exception('Username must be defined');
		return $this->_username;
	}
	/**
	 * sendgrid username
	 * @param string $value
	 */
	public function setUsername($value)
	{
		$this->_username=$value;
		return $this;
	}
	/**
	 * get sendgrid password
	 * @throws Exception if password is not defined
	 * @return string
	 */
	public function getPassword()
	{
		if($this->_password===null)
			throw new Exception('Password must be defined');
		return $this->_password;
	}
	
	/**
	 * sendgrid password
	 * @param string $value
	 */
	public function setPassword($value)
	{
		$this->_password=$value;
		return $this;
	}
	
	/**
	 * /**
	 * helper to create a new email
	 * @param string $html html email body
	 * @param string $subject email subject
	 * @param string $to to
	 * @param string $from from
	 * @return YiiSendGridMail
	 */
	public function createEmail($html=null,$subject=null,$to=null,$from=null)
	{
		$mail=new YiiSendGridMail($this->viewPath);
		if($html!==null)
			$mail->setHtml($html);
		if($subject!==null)
			$mail->setSubject($subject);
		if($to!==null)
			$mail->addTo($to);
		if($from!==null)
			$mail->setFrom($from);
		return $mail;
	}
	
	/**
	 * sends the mail and return the result
	 * @return boolean wheter it was successful
	 * if not, you can check the errors in {@link $lastErrors}
	 */
	public function send(YiiSendGridMail $mail)
	{
		$this->lastErrors=array();
		//logs if needed
		if($this->enableLog)
			$this->log($mail);
		
		//dry run
		if($this->dryRun)
			return true;
		
		$result=$this->getMailer()->send($mail);
		//not a json response, something is wrong. Use it as error
		if(!$result instanceof stdClass)
		{
			$this->lastError=array($result);
			return false;
		}

		if(isset($result->message) && $result->message==='success')
			return true;
		elseif(isset($result->message)  && $result->message==='error')
		{
			$this->lastErrors=$result->errors;
			return false;
		}
			
		//what happened?
		return true;
	}
	
	/**
	 * register swiftMail and SendGrid librarys autoloaders
	 */
	protected static function registerLibs()
	{
		if(!self::$_registered)
		{
			self::$_registered=true;
			//register sendgrid autoloader
			require __DIR__.'/vendors/unirest-php/lib/Unirest.php';
			require __DIR__.'/vendors/sendgrid-php/lib/SendGrid.php';
			require __DIR__.'/vendors/smtpapi-php/lib/Smtpapi.php';
			
			SendGrid::register_autoloader();
			Smtpapi::register_autoloader();
			
			//preload extension mail class
			require_once __DIR__.'/YiiSendGridMail.php';
		}
	}
	
	/**
	 * Logs a YiiSendGridMail using Yii::log.
	 * @return string log message
	 */
	protected function log(YiiSendGridMail $mail)
	{
		$tos=implode(', ', $mail->smtpapi->to);
		$headers=implode('', $mail->getHeaders());
		$content=$mail->getHtml();
		Yii::log("Sending email to {$tos}\n{$headers}\n{$content}", CLogger::LEVEL_TRACE, 'ext.sendgrid.YiiSendGrid');
	}
}