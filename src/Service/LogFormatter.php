<?php
namespace App\Service;

use Monolog\Formatter\FormatterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

class LogFormatter implements FormatterInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    private function JsonEncodeWithPassword($data) {
        foreach($data as $key => $value) {
            if(isset($data[$key]['_token'])) {
                unset($data[$key]['_token']);
            }
        }
        if(isset($data['_password'])) {
            $data['_password'] = md5($data['_password']);
        }
        if(isset($data['plainPassword'])) {
            $data['plainPassword']['first'] = md5($data['plainPassword']['first']);
            $data['plainPassword']['second'] = md5($data['plainPassword']['second']);
        }
        return json_encode($data);
    }

    /**
     * {@inheritDoc}
     * @see \Monolog\Formatter\FormatterInterface::format()
     */
    public function format(array $record) {
        $container = $record['context']['container'];
        $token = $container->get('security.token_storage')->getToken();
        $user = $token ? $token->getUser() : null;
        $request = $container->get('request_stack')->getCurrentRequest();
        $headers = $request->headers->all();
        $audit = array('@timestamp'=>date("Y-m-d H:i:s.v"), 'log.level'=>'INFO', 'service.name'=>'annuaire1212-web', 'logger'=>'LoggingAspect');
        $audit['message'] = array('event.type'=>'AUDIT', 'client.ip'=>$this->getUserIpAddr(), 'perimeter.description'=>$record['message']);
        $audit['message']['user.name'] = ($user && is_object($user)) ? $user->getUsername() : 'anonymoususer';
        $audit['message']['user_agent.original'] = $headers['user-agent'][0];
        $audit['message']['url.path'] = $request->getPathInfo();
        $audit['message']['http.request.method'] = strtoupper($request->getMethod());
        $audit['message']['source.name'] = 'localhost';
        if(!isset($record['context']['event'])) {
            $line = null;
        } elseif($record['context']['event']=='RESPONSE' && isset($record['context']['response'])) {
            $audit['message']['http.response.status'] = (int)$record['context']['response']->getStatusCode()==200 ? 'SUCCESS' : 'FAILED';
            $audit['message']['http.response.status_code'] = $record['context']['response']->getStatusCode();
            $audit['message']['event.duration'] = $record['context']['duration'];
            $line = json_encode($audit, JSON_UNESCAPED_SLASHES);
        } elseif($record['context']['event']=='REQUEST') {
            $audit['http.request.body.content'] = json_encode($request->request->all(), JSON_UNESCAPED_SLASHES);
            $line = json_encode($audit, JSON_UNESCAPED_SLASHES);
        }
        return $line.PHP_EOL;
    }

    public function getRealUser($securityContext, $container) {
        $impersonatorUser = null;
        if($securityContext->isGranted('ROLE_PREVIOUS_ADMIN')) {
            foreach($container->get('security.token_storage')->getToken()->getRoles() as $role) {
                if($role instanceof SwitchUserRole) {
                    $impersonatorUser = $role->getSource()->getUser();
                    break;
                }
            }
        }
        return $impersonatorUser;
    }

    /**
     * Formats a set of log records.
     * @param  array $records A set of records to format
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records) {
        foreach ($records as $key => $record) {
            $records[$key] = $this->format($record);
        }
        return $records;
    }

    function getUserIpAddr() {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}
?>
