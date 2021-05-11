<?php
namespace App\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Util\ClassUtils;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class AnnotationListener
{
    /**
     * @var AnnotationReader
     */
    protected $reader;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var integer
     */
    protected $duration;

    /**
     * Constructor
     * @param ContainerInterface $container
     * @param AnnotationReader $reader
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->reader = new AnnotationReader();
    }

    public function onKernelController(FilterControllerEvent $event) {
        $this->duration = microtime(true);
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }
        list($controllerObject, $methodName) = $controller;
        $monologAnnotation = 'App\Annotation\QMLogger';
        $message = '';
        // Get class annotation
        // Using ClassUtils::getClass in case the controller is an proxy
        $classAnnotation = $this->reader->getClassAnnotation(new \ReflectionClass(ClassUtils::getClass($controllerObject)), $monologAnnotation);
        if($classAnnotation) { $message .=  $classAnnotation->message; }
        // Get method annotation
        $controllerReflectionObject = new \ReflectionObject($controllerObject);
        $reflectionMethod = $controllerReflectionObject->getMethod($methodName);
        $methodAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $monologAnnotation);
        if($methodAnnotation) { $message .=  $methodAnnotation->message; }
        // Override the response only if the annotation is used for method or class
        if($classAnnotation || $methodAnnotation) {
            $this->container->get('monolog.logger.trace')->log(Logger::INFO, $message, array(
                    'container' => $this->container, 'event' => 'REQUEST')
            );
        }
    }

    public function onKernelResponse(FilterResponseEvent $event) {
        $this->duration = microtime(true) - $this->duration;
        $controller = explode('::', $event->getRequest()->attributes->get('_controller'));
        if (!is_array($controller) || count($controller)<=1) {
            return;
        }
        list($controllerName, $methodName) = $controller;
        $monologAnnotation = 'App\Annotation\QMLogger';
        $message = '';
        // Get class annotation
        // Using ClassUtils::getClass in case the controller is an proxy
        if (!class_exists($controllerName)) {
            return;
        }
        $classAnnotation = $this->reader->getClassAnnotation(new \ReflectionClass($controllerName), $monologAnnotation);
        if($classAnnotation) { $message .=  $classAnnotation->message; }
        // Get method annotation
        $controllerReflectionObject = new \ReflectionObject($this->container->get($controllerName));
        $reflectionMethod = $controllerReflectionObject->getMethod($methodName);
        $methodAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $monologAnnotation);
        if($methodAnnotation) { $message .=  $methodAnnotation->message; }
        // Override the response only if the annotation is used for method or class
        if($classAnnotation || $methodAnnotation) {
            $this->container->get('monolog.logger.trace')->log(Logger::INFO, $message, array(
                'container' => $this->container,
                'event' => 'RESPONSE',
                'response' => $event->getResponse(),
                'duration' => $this->duration * 1000
            ));
        }
    }
}
?>