<?php

namespace App\Controller;

use App\Event\CartoEvent;
use App\MainBundle\Twig\AssetsExtension;
use App\Service\Actions;
use App\Service\Core;
use App\Service\Loader;
use App\Service\Status;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    private $paginator;
    protected $service_status;
    protected $service_action;
    protected $service_assets_extension;
    protected $orange_main_loader;
    protected $orange_main_core;
    protected $cartoEvent;
    static $ids;
    static $states;

    public function __construct(ParameterBagInterface $params, PaginatorInterface $paginator, Status $status, Actions $action, AssetsExtension $assets_extension, Loader $orange_main_loader, Core $orange_main_core, CartoEvent $cartoEvent)
    {
        $this->paginator = $paginator;
        $this->service_status = $status;
        $this->service_action = $action;
        $this->service_assets_extension = $assets_extension;
        $this->orange_main_loader = $orange_main_loader;
        $this->orange_main_core = $orange_main_core;
        $this->cartoEvent = $cartoEvent;
        self::$ids = $params->get('ids');
        self::$states = $params->get('states');
    }

     public static function getSubscribedServices(): array  //ON surcharge cette fonction pour ajouter nos services aux services existants #Spécialité Symfony4
     {
         return array_merge(parent::getSubscribedServices(), [ // on merge le tableau des services par defaut avec notre tableau personnalisé
             //'orange_main.actions' => 'App\Service\Actions',
             //'monolog.logger.trace' => 'Psr\Log\LoggerInterface',
             //'orange_main.status' => 'App\Service\Status',
             //'orange_ca.mailer' => 'App\Service\Mailer'
             'event_dispatcher' => EventDispatcherInterface::class

         ]);
     }

    /**
     * @param Mixed $entity
     * @param string $formName
     * @return \Symfony\Component\Form\Form
     */
    protected function createCreateForm($entity, $formName, $options = array()) {
        //$type = '\App\Form\\'.$formName.'Type';
        $type = $formName;
        $form = $this->createForm($type , $entity, $options);
        return $form;
    }

    /**
     * @param Request $request
     * @param QueryBuilder $queryBuilder
     * @param string $rendererMethod
     * @return JsonResponse
     */
    protected function paginate($request, QueryBuilder $queryBuilder, $rendererMethod = 'addRowInTable', $orderMethod = 'setOrder', $rootColumnName='id') {
        $query = $queryBuilder->getQuery();
        $paginator  = $this->paginator;
        $numberPage = ((int)$request->query->get('iDisplayStart')/(int)$request->query->get('iDisplayLength'))+1;
        $pagination = $paginator->paginate($query, $request->query->get('page', 1), 10);
        $this->setFilter($queryBuilder, array(), $request);
        $this->{$orderMethod}($queryBuilder, array(), $request);
        $query = $this->customResultsQuery($queryBuilder);
        $this->get('session')->set('query_client', $query->getDQL());
        $query->setHint('knp_paginator.count', $this->getLengthResults($queryBuilder,$rootColumnName));
        $pagination = $paginator->paginate($query, $numberPage, 10, array('distinct' => false));
        $params = $pagination->getParams();
        // parameters to template
        $aaData = array();
        foreach ($pagination->getItems() as $entity) {
            $aaData[] = $this->{$rendererMethod}($entity);
        }
        $output = array(
            "sEcho" => $params['sEcho'],
            "iTotalRecords" => $pagination->getTotalItemCount(),
            "iTotalDisplayRecords" => $pagination->getTotalItemCount(),
            "aaData" => $aaData
        );
        $response = new JsonResponse($output);
        return $response;
    }

    /**
     * @param Request $request
     */
    protected function setOrder(QueryBuilder $queryBuilder, $aColumns, Request $request) {
        if($request->query->has('iSortCol_0')) {
            for($i=0;$i<intval($request->query->get('iSortingCols'));$i++) {
                if($request->query->get('bSortable_'.intval($request->query->get('iSortCol_'.$i)))=="true" && count($aColumns)) {
                    $queryBuilder->orderBy($aColumns[intval($request->query->get('iSortCol_'.$i))], $request->query->get('sSortDir_'.$i));
                }
            }
        }
    }

    /**
     * @param Request $request
     * @param array $data
     * @param Form $form
     */
    public function modifyRequestForForm($request, $data, $form, $method = 'POST') {
        $arrData = $request->request->all();
        foreach($data as $key => $value) {
            $arrData[$form->getName()][$key] = $value;
        }
        $request->request->replace($arrData);
        $request->setMethod($method);
        $form->handleRequest($request);
    }

    /**
     * @param Request $request
     */
    protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
        if($request->query->has('sSearch') && $request->query->get('sSearch')!="") {
            $search = '%'.str_replace("'", "\'", $request->query->get('sSearch')).'%';
            for($i=0; $i<count($aColumns); $i++) {
                $tag = substr($aColumns[$i], 2);
                $queryBuilder = $i==0 ?
                    $queryBuilder->andWhere($aColumns[$i]." LIKE :".$tag)->setParameter($tag, $search) :
                    $queryBuilder->orWhere($aColumns[$i]." LIKE :".$tag)->setParameter($tag, $search);
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    protected function customResultsQuery(QueryBuilder $queryBuilder) {
        return $queryBuilder->getQuery();
    }


    /**
     * @param QueryBuilder $queryBuilder
     * @return integer
     */
    protected function getLengthResults(QueryBuilder $queryBuilder, $rootColumnName) {
        return count($queryBuilder->select(sprintf('PARTIAL %s.{ %s }', $queryBuilder->getRootAlias(), $rootColumnName))
            ->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)->getArrayResult());
    }

    protected function getMyParameter($name, $path = array()) {
        //$data = $this->container->getParameter($name);
        $data = $this->getParameter($name);
        foreach($path as $key) {
            $data = $data[$key];
        }
        return $data;
    }

    /**
     * @param Mixed $entity
     * @param string $column
     * @return string
     */
    public function showEntityStatus($entity, $column) {
        $reflect = new \ReflectionClass($entity);
        $template = $this->get('twig')->loadTemplate($this->getMyParameter('template_status'));
        return $template->renderBlock('status_'.strtolower($reflect->getShortName()).'_'.$column, array(
            'entity' => $entity, 'ids' => $this->getMyParameter('ids'), 'states' => $this->getMyParameter('states')
        ));
    }

    public function replaceSpecialChars($libelle) {
        $this->special_char = array(
            'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î',
            'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß',
            'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î',
            'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A',
            'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd',
            'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G',
            'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i',
            'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L',
            'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O',
            'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's',
            'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U',
            'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z',
            'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o',
            'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?',
            '?', '_', '-', ':', '/', '!', '|', '=', '[', ']', '~', '{', '}', '(', ')',
            '\\\\',' ', '"',',',"\'"
        );
        $this->replacement_char = array(
            'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I',
            'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's',
            'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i',
            'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a',
            'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd',
            'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g',
            'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i',
            'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l',
            'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R',
            'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't',
            'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y',
            'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I',
            'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o',
            ' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','','','',''
        );
        for($i = 0; $i < count($this->special_char); $i++) {
            $libelle = str_replace($this->special_char[$i], $this->replacement_char[$i], $libelle);
        }
        return $libelle;
    }
}
