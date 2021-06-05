<?php

namespace Orange\MainBundle\Services;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class Mailer {
	
	/**
	 *
	 * @var Swift_Mailer
	 */
	protected $mailer;
	
	/**
	 *
	 * @var EngineInterface
	 */
	protected $templating;
	const FROM = "orange@orange.sn";
	const FROM_NAME = "CORIS ARQ"; 
	
	/**
	 *
	 * @param \Swift_Mailer $mailer        	
	 * @param EngineInterface $templating        	
	 */
	public function __construct($mailer, EngineInterface $templating) {
		$this->mailer = $mailer;
		$this->templating = $templating;
	}
	
	/**
	 * 
	 * @param array $to
	 * @param string $subject
	 * @param array $body
	 * @param string $template
	 */
	public function send($to, $subject, $body, $template = null) {
		$mail = \Swift_Message::newInstance ();
		
		$mail
			->setFrom ( array(self::FROM => self::FROM_NAME) )
			->setTo ( $to )
			->setSubject ( $subject )
			->setBody ( $this->templating->render ( $template ? $template : 'OrangeMainBundle:Extra:email.html.twig', array (
					'body' => $body 
			) ) )
			->setContentType ( 'text/html' );
		
		return $this->mailer->send ( $mail );
	}
	public function sendBug($to, $cc = null, $subject, $body,$chemin,$file) {
		$mail = \Swift_Message::newInstance();
		$mail->setFrom(array(self::FROM => self::FROM_NAME) )
		->setTo($to)
		->setCc($cc)
		->setSubject($subject)
		->setBody($body)
		->setContentType('text/html')
		->attach(\Swift_Attachment::fromPath($chemin.'/'.$file))
		;
		return $this->mailer->send($mail);
	}
	
	public function sendMailChargement($to, $cc = null, $subject, $body) {
		$mail = \Swift_Message::newInstance();
		$mail->setFrom(array(self::FROM => self::FROM_NAME) )
		->setTo($to)
		->setCc($cc)
		->setSubject($subject)
		->setBody($body)
		->setContentType('text/html');
		return $this->mailer->send($mail);
	}
	
	/**
	 * 
	 * @param array $mailArray
	 * @return bool
	 */
	public function sendArray($mailArray) {
		return $this->send($mailArray['recipients'], $mailArray['subject'], $mailArray['body']);
	}
}
