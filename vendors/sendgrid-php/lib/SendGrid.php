<?php

class SendGrid
{

    const VERSION="2.0.3";

    protected $namespace="SendGrid";

    protected $url="https://api.sendgrid.com/api/mail.send.json";

    protected $headers=array(
        'Content-Type'=>'application/json'
    );

    protected $username;

    protected $password;

    protected $options;

    protected $web;

    public function __construct($username,$password,$options=array("turn_off_ssl_verification" => false))
    {
        $this->username=$username;
        $this->password=$password;
        $this->options=$options;
    }

    /**
     *
     * @param SendGrid\Email $email            
     * @return string|stdObj
     */
    public function send(SendGrid\Email $email)
    {
        $form=$email->toWebFormat();
        $form['api_user']=$this->username;
        $form['api_key']=$this->password;
        
        // option to ignore verification of ssl certificate
        if(isset($this->options['turn_off_ssl_verification']) && isset($this->options['turn_off_ssl_verification']) && $this->options['turn_off_ssl_verification'] == true)
        {
            \Unirest::verifyPeer(false);
        }
        
        $response=\Unirest::post($this->url,array(),$form);
        
        return $response->body;
    }

    /**
     * register class autoloader via spl_autoload_register
     */
    public static function register_autoloader()
    {
        spl_autoload_register(array(
            'SendGrid',
            'autoloader'
        ));
    }

    /**
     * load a class
     *
     * @param string $class            
     */
    public static function autoloader($class)
    {
        // Check that the class starts with "SendGrid"
        if($class == 'SendGrid' || stripos($class,'SendGrid\\') === 0)
        {
            $file=str_replace('\\','/',$class);
            
            if(file_exists(dirname(__FILE__) . '/' . $file . '.php'))
            {
                require_once (dirname(__FILE__) . '/' . $file . '.php');
            }
        }
    }
}
