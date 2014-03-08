yii-sendgrid extension
============

YiiSendGrid is an application component used for sending email through [sendgrid](http://sendgrid.com).  
It's a wrapper for [SendGrid php library](https://github.com/sendgrid/sendgrid-php-example)  

You may configure it as below.  Check the attributes of YiiSendGrid class and YiiSendGridMail class for more options.  

## Requirements

+ PHP 5.3+  

## Instalattion

Download the file and extract to protected/extensions (or anywhere you like, but then adjust the example accordingly)

``` php
 return array(  
 	'components' => array(  
		//...
 		'sendgrid' => array(  
 			'class' => 'ext.yii-sendgrid.YiiSendGrid',  
 			'username'=>'myUsername', //replace with your actual username  
 			'password'=>'myP4s$w0rd', //replace with your actual password  
 			//'viewPath' => 'application.views.mail',  //alias to the layouts path. Optional  
 			//'enableLog' => YII_DEBUG, //wheter to log the emails sent. Optional  
 			//'dryRun' => false, //if enabled, it won't actually send the emails, only log. Optional  
			//'disableSslVerification'=>true,//ignore verification of SSL certificate  
 		),  
 		//...  
 	)  
 );  
```  
 
## How to use

Examples  
``` php  
 $message = Yii::app()->sendgrid->createEmail();  
//shortcut to $message=new YiiSendGridMail($viewsPath);
 $message->setHtml('<p>Message content here with HTML</p>')  
 	->setSubject('My Subject')  
 	->addTo('johnDoe@domain.com')  
   ->setFrom('myemail@mydomain.com');  
Yii::app()->mail->send($message);  
```  

 or just  
``` php  
 $message = Yii::app()->sendgrid->createEmail($htmlBody,$subject,$to,$from);  
 Yii::app()->mail->sendgrid->send($message);  
```  

A more real life and complete example using a view and optionally a layout  
``` php  
 $message = Yii::app()->sendgrid->createEmail();  
//set view variable $user  
$message  
	->setView('signup') //view located in YiiGridView::$viewPath  
	->setHtml(array(  
		'user'=>$user//my User model. Pass it to the view as $user, same way controller does  
	));  
//$message->layout='application.views.layouts.email';  // optionally you can use a layout  
 $message  
 	->setSubject('Welcome to '.Yii::app()->name).'!')  
 	->addTo($user->email)  
   ->setFrom(Yii::app()->params['adminEmail']);  
 Yii::app()->mail->sendgrid->send($message);  
```  
  
## Resources  

+[SendGrid php library](https://github.com/sendgrid/sendgrid-php-example)  