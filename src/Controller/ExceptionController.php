<?php
namespace App\Controller;

// use Symfony\Component\HttpKernel\Controller\ErrorController as Controller;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExceptionController extends Controller
{

    protected $kernel;
    protected $twig;
    protected $container;

    public function __construct(HttpKernelInterface $kernel, \Twig_Environment $twig)
    {
        $this->kernel = $kernel;
        $this->twig = $twig;
    }

    /**
     * Converts an Exception to a Response.
     *
     * A "showException" request parameter can be used to force display of an error page (when set to false) or
     * the exception page (when true). If it is not present, the "debug" value passed into the constructor will
     * be used.
     *
     * @param Request              $request   The request
     * @param FlattenException     $exception A FlattenException instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     *
     * @return Response
     *
     * @throws \InvalidArgumentException When the exception template does not exist
     */
    public function show(ParameterBagInterface $params, Request $request, FlattenException $exception, \Swift_Mailer $mailer, DebugLoggerInterface $logger = null, UrlGeneratorInterface $router)
    {
        $code = $request->attributes->get('exception')->getStatusCode();
        $serveur = $request->getSchemeAndHttpHost();
        $ERREUR = "<h1>Alerte:</strong> Erreur rencontrée !</h1>";
        $date = 'Erreur survenue le '.date('Y-m-d à H:i:s');
        $messageOutput = $ERREUR . 'Merci pour la correction de cette erreur dans les meilleurs délais.<br><br>Code: ' .$code. '<br>Host: ' .$serveur. '<br>' .$date. '';

        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $templateData = $this->getTemplateData($currentContent, $code, $exception, $logger);

        if ($request->server->get('APP_ENV') == 'dev'){
            return new Response($this->twig->render('@Twig/Exception/exception_full.html.twig', $templateData ));
        }

        if($code!=404) {
            $dossier = __DIR__."/../../public/uploads/bugs/".date("Y_m_d");
            if(!file_exists($dossier)) {
                mkdir($dossier, 0775, true);
            }

            $file = "bug-".Date("His").".html";
            $chemin = $dossier."/".$file;
            file_put_contents($chemin, $this->twig->render('@Twig/Exception/exception_full.html.twig', $templateData));
            // mailer
            //contact admin
            $to = array("mohamed.sall@orange-sonatel.com", "abdoulaye.fall3@orange-sonatel.com", "fatoukine.ndao1@orange-sonatel.com");
            $cc = array("madiagne.sylla@orange-sonatel.com", "moctarthiam.mbodj@orange-sonatel.com");
            $to = array("abdoulaye.fall3@orange-sonatel.com");
            $cc = array("abdoulaye.fall3@orange-sonatel.com");
            $subject = 'Erreur de Traitement';
            $this->sendMailError($mailer, $subject, $messageOutput, $to, $cc, $chemin);
        }
        return new Response($this->twig->render('exception/error.html.twig',['code' => $code ]));
    }

    /**
     * Determines the template parameters to pass to the view layer.
     *
     * @param string               $currentContent
     * @param int                  $code
     * @param DebugLoggerInterface $logger
     *
     * @return array
     */
    private function getTemplateData($currentContent, $code, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        return [
            'exception' => $exception,
            'status' => 'error',
            'status_code' => $code,
            'status_text' => array_key_exists($code, Response::$statusTexts) ? Response::$statusTexts[$code] : 'error',
            'currentContent' => $currentContent,
            'logger' => $logger,
        ];
    }

    public function sendMailError($mailer, $subject, $body, $to, $cc, $attach):bool
    {
        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom(array('eboard@orange-sonatel.com'=>'EBOARD WEB'))
            ->setContentType('text/html')
            ->setTo($to)
            ->setCc($cc)
            //->setBcc($bcc)
            ->setBody($body)
            ->attach(\Swift_Attachment::fromPath($attach));
        try{
            $mailer->send($message);
            return true;
        } catch(\Swift_TransportException $e) {
            // $isSend = false;
            return false;
        }
    }
}
