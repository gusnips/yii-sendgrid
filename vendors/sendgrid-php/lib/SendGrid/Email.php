<?php
namespace SendGrid;

class Email extends \CComponent
{

	public $to;

	public $from;

	public $from_name;

	public $reply_to;

	public $cc_list;

	public $bcc_list;

	public $subject;

	public $text;

	public $html;

	public $attachments;

	public $headers=array();

	/**
	 *
	 * @var Smtpapi\Header
	 */
	public $smtpapi;

	/**
	 */
	public function __construct()
	{
		$this->from_name=false;
		$this->reply_to=false;
		$this->smtpapi=new \Smtpapi\Header();
	}

	/**
	 * _removeFromList
	 * Given a list of key/value pairs, removes the associated keys
	 * where a value matches the given string ($item)
	 *
	 * @param Array $list
	 *- the list of key/value pairs
	 * @param String $item
	 *- the value to be removed
	 */
	private function _removeFromList(&$list,$item,$key_field=null)
	{
		foreach($list as $key=>$val)
		{
			if($key_field)
			{
				if($val[$key_field] == $item)
				{
					unset($list[$key]);
				}
			} else
			{
				if($val == $item)
				{
					unset($list[$key]);
				}
			}
		}
		// repack the indices
		$list=array_values($list);
	}

	/**
	 *
	 * @param string $email
	 * @param string $name
	 * @return \SendGrid\Email
	 */
	public function addTo($email,$name=null)
	{
		$this->smtpapi->addTo($email,$name);
		return $this;
	}

	/**
	 *
	 * @param array $emails
	 * @return \SendGrid\Email
     * @see \Smtpapi\Header::setTos()
	 */
	public function setTos(array $emails)
	{
		$this->smtpapi->setTos($emails);
		return $this;
	}

	/**
	 *
	 * @param string $email
	 * @return \SendGrid\Email
	 */
	public function setFrom($email)
	{
		$this->from=$email;
		return $this;
	}

	/**
	 *
	 * @param string $as_array
	 * @return string:string[]
	 */
	public function getFrom($as_array=false)
	{
		if($as_array && ($name=$this->getFromName()))
		{
			return array(
				"$this->from"=>$name
			);
		}
		return $this->from;
	}

	/**
	 *
	 * @param string $name
	 * @return \SendGrid\Email
	 */
	public function setFromName($name)
	{
		$this->from_name=$name;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getFromName()
	{
		return $this->from_name;
	}

	/**
	 *
	 * @param string $email
	 * @return \SendGrid\Email
	 */
	public function setReplyTo($email)
	{
		$this->reply_to=$email;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getReplyTo()
	{
		return $this->reply_to;
	}

	/**
	 *
	 * @param string $email
	 * @return \SendGrid\Email
	 */
	public function setCc($email)
	{
		$this->cc_list=array(
			$email
		);
		return $this;
	}

	/**
	 *
	 * @param array $email_list
	 * @return \SendGrid\Email
	 */
	public function setCcs(array $email_list)
	{
		$this->cc_list=$email_list;
		return $this;
	}

	/**
	 *
	 * @param string $email
	 * @return \SendGrid\Email
	 */
	public function addCc($email)
	{
		$this->cc_list[]=$email;
		return $this;
	}

	/**
	 *
	 * @param string $email
	 * @return \SendGrid\Email
	 */
	public function removeCc($email)
	{
		$this->_removeFromList($this->cc_list,$email);
		
		return $this;
	}

	/**
	 *
	 * @return string[]
	 */
	public function getCcs()
	{
		return $this->cc_list;
	}

	/**
	 *
	 * @param string $email
	 * @return \SendGrid\Email
	 */
	public function setBcc($email)
	{
		$this->bcc_list=array(
			$email
		);
		return $this;
	}

	/**
	 *
	 * @param string[] $email_list
	 * @return \SendGrid\Email
	 */
	public function setBccs($email_list)
	{
		$this->bcc_list=$email_list;
		return $this;
	}

	/**
	 *
	 * @param string $email
	 * @return \SendGrid\Email
	 */
	public function addBcc($email)
	{
		$this->bcc_list[]=$email;
		return $this;
	}

	/**
	 *
	 * @param string $email
	 * @return \SendGrid\Email
	 */
	public function removeBcc($email)
	{
		$this->_removeFromList($this->bcc_list,$email);
		return $this;
	}

	/**
	 *
	 * @return string[]
	 */
	public function getBccs()
	{
		return $this->bcc_list;
	}

	/**
	 * set mail subject
	 * 
	 * @param string $subject
	 * @return \SendGrid\Email
	 */
	public function setSubject($subject)
	{
		$this->subject=$subject;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 *
	 * @param string $text
	 * @return \SendGrid\Email
	 */
	public function setText($text)
	{
		$this->text=$text;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 *
	 * @param string $html
	 * @return \SendGrid\Email
	 */
	public function setHtml($html)
	{
		$this->html=$html;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getHtml()
	{
		return $this->html;
	}

	/**
	 *
	 * @param string[] $files
	 * @return \SendGrid\Email
	 */
	public function setAttachments(array $files)
	{
		$this->attachments=array();
		
		foreach($files as $filename=>$file)
		{
			if(is_string($filename))
			{
				$this->addAttachment($file,$filename);
			} else
			{
				$this->addAttachment($file);
			}
		}
		
		return $this;
	}

	/**
	 * replace all attachments with a new one
	 * 
	 * @param string $file
	 *path to the file
	 * @param string $custom_filename
	 *new name for the file
	 * @return \SendGrid\Email
	 */
	public function setAttachment($file,$custom_filename=null)
	{
		$this->attachments=array(
			$this->_getAttachmentInfo($file,$custom_filename)
		);
		return $this;
	}

	/**
	 * add a new attachment
	 * 
	 * @param string $file
	 *path to the file
	 * @param string $custom_filename
	 * @return \SendGrid\Email
	 */
	public function addAttachment($file,$custom_filename=null)
	{
		$this->attachments[]=$this->_getAttachmentInfo($file,$custom_filename);
		return $this;
	}

	/**
	 *
	 * @return string[]
	 */
	public function getAttachments()
	{
		return $this->attachments;
	}

	/**
	 *
	 * @param string $file
	 *path to the attachment
	 * @return \SendGrid\Email
	 */
	public function removeAttachment($file)
	{
		$this->_removeFromList($this->attachments,$file,"file");
		return $this;
	}

	/**
	 *
	 * @param unknown $file
	 * @param string $custom_filename
	 * @return string
	 */
	private function _getAttachmentInfo($file,$custom_filename=null)
	{
		$info=pathinfo($file);
		$info['file']=$file;
		if(! is_null($custom_filename))
		{
			$info['custom_filename']=$custom_filename;
		}
		
		return $info;
	}

	/**
	 *
	 * @param unknown $categories
	 * @return \SendGrid\Email
     * @see \Smtpapi\Header::setCategories()
	 */
	public function setCategories($categories)
	{
		$this->smtpapi->setCategories($categories);
		return $this;
	}

	/**
	 *
	 * @param string $category
	 * @return \SendGrid\Email
     * @see \Smtpapi\Header::setCategory()
	 */
	public function setCategory($category)
	{
		$this->smtpapi->setCategory($category);
		return $this;
	}

	/**
	 *
	 * @param string $category
	 * @return \SendGrid\Email
     * @see \Smtpapi\Header::addCategory()
	 */
	public function addCategory($category)
	{
		$this->smtpapi->addCategory($category);
		return $this;
	}

	/**
	 *
	 * @param string $category
	 * @return \SendGrid\Email
     * @see \Smtpapi\Header::removeCategory()
	 */
	public function removeCategory($category)
	{
		$this->smtpapi->removeCategory($category);
		return $this;
	}

	/**
	 *
	 * @param string[] $key_value_pairs
	 *an array in the following format "%word%"=>"My Replacement Word"
	 * @return \SendGrid\Email
	 * @see \Smtpapi\Header::setSubstitution()
	 */
	public function setSubstitutions($key_value_pairs)
	{
		$this->smtpapi->setSubstitutions($key_value_pairs);
		return $this;
	}

	/**
	 *
	 * @param string $from_value
	 * @param string[] $to_values
	 * @return \SendGrid\Email
	 * @see \Smtpapi\Header::addSubstitution()
	 */
	public function addSubstitution($from_value,array $to_values)
	{
		$this->smtpapi->addSubstitution($from_value,$to_values);
		return $this;
	}

	/**
	 *
	 * @param string[] $key_value_pairs
	 * @return \SendGrid\Email
	 * @see \Smtpapi\Header::setSections()
	 */
	public function setSections(array $key_value_pairs)
	{
		$this->smtpapi->setSections($key_value_pairs);
		return $this;
	}

	/**
	 *
	 * @param string $from_value
	 * @param string $to_value
	 * @return \SendGrid\Email
	 * @see \Smtpapi\Header::addSection()
	 */
	public function addSection($from_value,$to_value)
	{
		$this->smtpapi->addSection($from_value,$to_value);
		return $this;
	}

	/**
	 *
	 * @param string[] $key_value_pairs
	 * @return \SendGrid\Email
     * @see \Smtpapi\Header::setUniqueArgs()
	 */
	public function setUniqueArgs(array $key_value_pairs)
	{
		$this->smtpapi->setUniqueArgs($key_value_pairs);
		return $this;
	}

	/**
	 * synonym method
	 *
	 * @see setUniqueArgs()
	 */
	public function setUniqueArguments(array $key_value_pairs)
	{
		return $this->setUniqueArgs($key_value_pairs);
	}

	/**
	 *
	 * @param string $key
	 * @param string $value
	 * @return \SendGrid\Email
     * @see \Smtpapi\Header::addUniqueArg()
	 */
	public function addUniqueArg($key,$value)
	{
		$this->smtpapi->addUniqueArg($key,$value);
		return $this;
	}

	/**
	 * synonym method
	 *
	 * @see addUniqueArg()
	 */
	public function addUniqueArgument($key,$value)
	{
		return $this->addUniqueArg($key,$value);
	}

	/**
	 *
	 * @param unknown $filter_settings
	 * @return \SendGrid\Email
     * @see \Smtpapi\Header::setFilters()
	 */
	public function setFilters($filter_settings)
	{
		$this->smtpapi->setFilters($filter_settings);
		return $this;
	}

	/**
	 * synonym method
	 *
	 * @see setFilters()
	 */
	public function setFilterSettings($filter_settings)
	{
		return $this->setFilters($filter_settings);
	}

	/**
	 *
	 * @param string $filter_name
	 * @param string $parameter_name
	 * @param string $parameter_value
	 * @return \SendGrid\Email
     * @see \Smtpapi\Header::addFilter()
	 */
	public function addFilter($filter_name,$parameter_name,$parameter_value)
	{
		$this->smtpapi->addFilter($filter_name,$parameter_name,$parameter_value);
		return $this;
	}

	/**
	 * synonym method
	 *
	 * @see addFilter()
	 */
	public function addFilterSetting($filter_name,$parameter_name,$parameter_value)
	{
		return $this->addFilter($filter_name,$parameter_name,$parameter_value);
	}

	/**
	 *
	 * @return string[]
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 *
	 * @return string
	 */
	public function getHeadersJson()
	{
		if(count($this->getHeaders()) <= 0)
		{
			return "{}";
		}
		return $this->jsonify($this->getHeaders());
	}

	/**
	 * replace all headers
	 *
	 * @param string[] $key_value_pairs
	 * @return \SendGrid\Email
	 */
	public function setHeaders($key_value_pairs)
	{
		$this->headers=$key_value_pairs;
		return $this;
	}

	/**
	 * add or replace a header
	 *
	 * @param string $key
	 * @param string $value
	 * @return \SendGrid\Email
	 */
	public function addHeader($key,$value)
	{
		$this->headers[$key]=$value;
		return $this;
	}

	/**
	 * removes a header
	 *
	 * @param string $key
	 *header key to remove
	 * @return \SendGrid\Email
	 */
	public function removeHeader($key)
	{
		unset($this->headers[$key]);
		return $this;
	}

	/**
	 *
	 * @return string[]
	 */
	public function toWebFormat()
	{
		$web=array(
			'to'=>$this->to,
			'from'=>$this->getFrom(),
			'x-smtpapi'=>$this->smtpapi->jsonString(),
			'subject'=>$this->getSubject(),
			'text'=>$this->getText(),
			'html'=>$this->getHtml(),
			'headers'=>$this->getHeadersJson()
		);
		
		if($this->getCcs())
		{
			$web['cc']=$this->getCcs();
		}
		if($this->getBccs())
		{
			$web['bcc']=$this->getBccs();
		}
		if($this->getFromName())
		{
			$web['fromname']=$this->getFromName();
		}
		if($this->getReplyTo())
		{
			$web['replyto']=$this->getReplyTo();
		}
		if($this->smtpapi->to && (count($this->smtpapi->to) > 0))
		{
			$web['to']="";
		}
		
		$web=$this->updateMissingTo($web);
		
		if($this->getAttachments())
		{
			foreach($this->getAttachments() as $f)
			{
				$file=$f['file'];
				$extension=null;
				if(array_key_exists('extension',$f))
				{
					$extension=$f['extension'];
				}
				;
				$filename=$f['filename'];
				$full_filename=$filename;
				
				if(isset($extension))
				{
					$full_filename=$filename . '.' . $extension;
				}
				if(array_key_exists('custom_filename',$f))
				{
					$full_filename=$f['custom_filename'];
				}
				
				$contents='@' . $file;
				if(class_exists('CurlFile'))
				{ // php >= 5.5
					$contents=new \CurlFile($file,$extension,$filename);
				}
				
				$web['files[' . $full_filename . ']']=$contents;
			}
			;
		}
		
		return $web;
	}

	/**
	 * There needs to be at least 1 to address, or else the mail won't send.
	 * This method modifies the data that will be sent via either Rest
	 */
	public function updateMissingTo($data)
	{
		if($this->smtpapi->to && (count($this->smtpapi->to) > 0))
		{
			$data['to']=$this->getFrom();
		}
		return $data;
	}

	/**
	 * transforms a value to json
	 *
	 * @param mixed $value
	 * @return string
	 */
	protected function jsonify($value)
	{
		return json_encode($value,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
	}
}
