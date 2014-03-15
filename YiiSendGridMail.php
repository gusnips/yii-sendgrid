<?php
require __DIR__.'/vendors/sendgrid-php/lib/SendGrid/Email.php';
/**
* YiiSendGridMail class file.
*
* @author Gustavo Salomé Silva <gustavonips@gmail.com>
*/

/**
* Extends {@link SendGrid\Email} to render a view file if needed
*/
class YiiSendGridMail extends SendGrid\Email {
	
	/**
	 * the view to use for rendering the body, null if no view is 
	* used.  An extra variable $mail will be passed to the view .which you may 
	* use to set e.g. the email subject from within the view
	* @var string 
	*/
	public $view;
	
	/**
	 * layout to use to render files
	 * @var string
	 */
	public $layout;
	
	/**
	 * @var string the path to the location where mail views are stored.
	 * Defaults to 'application.views.mail'.
	 */
	public $viewPath='application.views.mail';
	
	/**
	 * set the view path, if avaliable
	 */
	public function __construct($viewPath=null)
	{
		if($viewPath!==null)
			$this->viewPath=$viewPath;
		
		return parent::__construct();
	}
	
	/**
	 * if set to null, no layout is used
	 * @param string $value path or alias to the layout
	 * @return YiiSendGridMail
	 */
	public function setLayout($value)
	{
		$this->layout=$value;
		return $this;
	}
	
	/**
	 * set a view to be used
	 * @param string $name view name
	 * @return YiiSendGridMail
	 */
	public function setView($name)
	{
		$this->view=$name;
		return $this;
	}

	/**
	 * Set the body of this entity, either as html or array of view parameters
	 * 
	 * @param string|array the body of the message.
	 * if view is not set, set  
	 * If a $this->view is set and this is a string, this is passed to the view as $content.  
	 * If $this->view is set and this is an array, the array values are passed to the view like in the 
	 * controller render() method
	 * @return SendGrid\Email
	 */
	public function setHtml($html = '') 
	{
		if ($this->view !== null) 
		{
			//if it's an array, we pass as properties to render the view, otherwise we use as the view $content variable
			if (!is_array($html))
				$data=array('content'=>$html);
			else
				$data=$html;
			
			// if Yii::app()->controller doesn't exist create a dummy 
			// controller to render the view (needed in the console app)
			if(isset(Yii::app()->controller))
				$controller = Yii::app()->controller;
			else
				$controller = new CController('SendGridMail');
			
			// renderPartial won't work with CConsoleApplication, so use 
			// renderInternal - this requires that we use an actual path to the 
			// view rather than the usual alias
			$viewPath = Yii::getPathOfAlias($this->viewPath.'.'.$this->view).'.php';
			
			if($viewPath===false)
				throw new Exception("Invalid mail view '{$this->view}' in path '{$this->viewPath}'");
			
			$html = $controller->renderInternal($viewPath, $data, true);
			
			//applys the layout if needed
			if($this->layout!==null)
			{
				$layout=$controller->getLayoutFile($this->layout);
				$html=$controller->renderInternal($layout,array_merge($data,array('content'=>$html)),true);
			}	
		}
		return parent::setHtml($html);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \SendGrid\Email::setTos()
	 */
	public function setTos(array $emails)
	{
		return $this->setTo($emails);
	}
	
	/**
	 * add a new recipients for the email
	 * you can use to add one or more
	 * example:
	 * <pre>
	 * 	$mail->setTo(array("john@email.com"=>"John","maria@email.com"));
	 *  $mail->setTo("joana@email.com","Joana");
	 * </pre>
	 * @param string|array $email
	 * @param string $name
	 * @return \SendGrid\Email
	 */
	public function setTo($email, $name=null)
	{
		if(!is_array($email))
			$email=array($email=>$name);
		foreach($email as $mail=>$nam)
		{
			if(is_int($mail))
			{
				$mail=$nam;
				$nam=null;
			}
			parent::addTo($mail,$nam);
		}
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see YiiSendGridMail::setTo()
	 */
	public function addTo($email,$name=null)
	{
		return $this->setTo($email,$name);
	}
	
	/**
	 * Get a list of recipients
	 * @return array
	 */
	public function getTo()
	{
		return $this->smtpapi->to;
	}
	
}