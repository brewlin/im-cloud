<?php

namespace Discovery\Provider;

use Core\Co;
use Core\Console\Console;
use Core\Container\Mapping\Bean;
use Log\Helper\Log;
use Swlib\Saber;
use Swlib\SaberGM;
use Log\Helper\CLog;
use Swoole\Coroutine\Http\Client;

/**
 * Consul provider
 * @Bean()
 */
class ConsulProvider implements ProviderInterface
{
    /**
     * Register path
     */
    const REGISTER_PATH = '/v1/agent/service/register';

    /**
     * Discovery path
     */
    const DISCOVERY_PATH = '/v1/health/service/';

    /**
     * Specifies the address of the consul
     *
     * @var string
     */
    private $address = "http://127.0.0.1";

    /**
     * Specifies the prot of the consul
     *
     * @var int
     */
    private $port = 8500;


    /**
     * Specifies the address of the service
     *
     * @var string
     */
    private $registerAddress = 'http://127.0.0.1';

    /**
     * Specifies the port of the service
     *
     * @var int
     */
    private $registerPort = 88;


    /**
     * Specifies the datacenter to query. This will default to the datacenter of the agent being queried
     *
     * @var string
     */
    private $discoveryDc = "dc1";

    /**
     * Specifies a node name to sort the node list in ascending order based on the estimated round trip time from that node
     *
     * @var string
     */
    private $discoveryNear = "";

    /**
     * Specifies the tag to filter the list. This is specifies as part of the URL as a query parameter.
     *
     * @var string
     */
    private $discoveryTag = "";

    /**
     * Specifies that the server should return only nodes with all checks in the passing state
     *
     * @var bool
     */
    private $discoveryPassing = true;

    /**
     * register data config
     * @var array
     */
    private $registerParam;

    /**
     * discovery data config
     * @var array
     */
    private $discoveryParam;
    /**
     * register service id
     * @var string
     */
    const ServerId = "service-%s-%s-%s";

    /**
     * get service list
     *
     * @param string $serviceName
     * @param array  $params
     *
     * @return array
     */
    public function getServiceList(string $serviceName, ...$params)
    {
        $this->initService(config("discovery"));
        $url        = $this->getDiscoveryUrl($serviceName);
        try{
                $cli = new Client($this->address,$this->port);
                $cli->setHeaders([
                ]);
                $cli->get($url);
                $services = $cli->body;
                $cli->close();
                $services = json_decode($services,true);
                if(!is_array($services))return [];
//            $result = SaberGM::get(sprintf("http://%s:%d%s",$this->address,$this->port,$url));
//            $services = $result->getParsedJsonArray();
        }catch (\Throwable $e){
            return [];
        }

        // 数据格式化
        $nodes = [];
        foreach ($services as $service) {
            if (!isset($service['Service'])) {
                Log::warning("consul[Service] 服务健康节点集合，数据格式不不正确，Data=" . $service);
                continue;
            }
            $serviceInfo = $service['Service'];
            if (!isset($serviceInfo['Address'], $serviceInfo['Port'])) {
                Log::warning("consul[Address] Or consul[Port] 服务健康节点集合，数据格式不不正确，Data=" . $result);
                continue;
            }
            $nodes[] = $serviceInfo;
//            $address = $serviceInfo['Address'];
//            $port    = $serviceInfo['Port'];

//            $uri     = implode(":", [$address, $port]);
//            $nodes[] = $uri;
        }

        return $nodes;
    }

    /**
     * register service
     *
     * @param array ...$params
     *
     * @return bool
     */
    public function registerService(...$params)
    {
        $this->initService(config("discovery"));
        try{
            $res = SaberGM::put(
                sprintf("%s:%d%s",$this->address,$this->port,self::REGISTER_PATH),
                json_encode($this->registerParam)
            );
        }catch (\Throwable $e){
            CLog::error(
                sprintf(
                    '<error>RPC service register failed by consul ! tcp=%s:%d</error>',
                    $this->registerAddress,
                    $this->registerPort).
                " exception:".
                $e->getMessage()
            );
            return false;
        }
        if($res->success){
            Console::writeln(sprintf('<success>RPC service register success by consul ! tcp=%s:%d</success>', $this->registerAddress, $this->registerPort));
            return true;
        }

        Console::writeln(sprintf('<error>RPC service register failed by consul ! tcp=%s:%d</error>', $this->registerAddress, $this->registerPort));
        return false;
    }

    /**
     * init config
     * @param array ...$param
     */
    public function initService(...$param){
        if(empty($param["consul"])){
            $param = config("discovery");
        }
        if(empty($param)){
            CLog::error("consul register service faile ,config is empty");
            throw new \Exception("consul reigster fail");
        }
        $config = $param["consul"];
        $register = $config["register"];
        $discovery = $config["discovery"];

        if(empty($register) || empty($discovery)){
            CLog::error("provider confi is wrong");
            throw new \Exception();
        }
        $hostName = gethostname();
        if(empty($register['ID'])){
            $register['ID'] = sprintf('service-%s-%s-%s', $register["Name"], $hostName,$register["Port"]);
        }
        $this->address = $config["address"];
        $this->port = $config["port"];
        $this->registerParam = $register;
        $this->discoveryParam = $discovery;
        $this->registerAddress = $register["Address"];
        $this->registerPort = $register["Port"];

    }

    /**
     * @param string $serviceName
     *
     * @return string
     */
    private function getDiscoveryUrl(string $serviceName): string
    {
        $query = [
            'passing' => $this->discoveryPassing,
            'dc'      => $this->discoveryDc,
            'near'    => $this->discoveryNear,
        ];

        if (!empty($this->discoveryTag)) {
            $query['tag'] = $this->discoveryTag;
        }

        $queryStr    = http_build_query($query);
        $path        = sprintf('%s%s', self::DISCOVERY_PATH, $serviceName);

        return sprintf('%s?%s', $path, $queryStr);
    }

    /**
     * @return array|mixed
     */
    public function checks()
    {
        return $this->request('get', '/v1/agent/checks');
    }

    /**
     * @param mixed ...$param
     * @return array
     */
    public function members(...$param)
    {
        list($option) = $param;
        $params = [
            'query' => $this->resolveOptions($option, ['wan']),
        ];

        return $this->request('get', '/v1/agent/members', $params);
    }

    /**
     * @return array
     */
    public function self():array
    {
        return $this->request('get', '/v1/agent/self');
    }

    /**
     * @param $address
     * @param array $options
     * @return array
     */
    public function join($address, ...$param): array
    {
        list($options) = $param;
        $params = [
            'query' => $this->resolveOptions($options, ['wan']),
        ];

        return $this->request('get', '/v1/agent/join/' . $address, $params);
    }

    /**
     * @param $node
     * @return array|mixed
     */
    public function forceLeave($node)
    {
        return $this->request('get', '/v1/agent/force-leave/' . $node);
    }

    /**
     * @param $check
     * @return array
     */
    public function registerCheck($check): array
    {
        $params = [
            'body' => json_encode($check),
        ];

        return $this->request('put', '/v1/agent/check/register', $params);
    }

    /**
     * @param $checkId
     * @return array
     */
    public function deregisterCheck($checkId): array
    {
        return $this->request('put', '/v1/agent/check/deregister/' . $checkId);
    }

    /**
     * @param $checkId
     * @param array $options
     * @return array
     */
    public function passCheck($checkId,...$param): array
    {
        list($options) = $param;
        $params = [
            'query' => $this->resolveOptions($options, ['note']),
        ];

        return $this->request('put', '/v1/agent/check/pass/' . $checkId, $params);
    }

    /**
     * @param $checkId
     * @param array $options
     * @return array
     */
    public function warnCheck($checkId, ...$param)
    {
        list($options) = $param;
        $params = [
            'query' => $this->resolveOptions($options, ['note']),
        ];

        return $this->request('put', '/v1/agent/check/warn/' . $checkId, $params);
    }

    /**
     * @param $checkId
     * @param array $options
     * @return array
     */
    public function failCheck($checkId, ...$param): array
    {
        list($options) = $param;
        $params = [
            'query' => $this->resolveOptions($options, ['note']),
        ];

        return $this->request('put', '/v1/agent/check/fail/' . $checkId, $params);
    }

    /**
     * @param string
     * @return string
     */
    private function getServiceId(string $service):string
    {
        return sprintf(self::ServerId,$service,gethostname(),$this->registerPort);
    }

    /**
     * del one service
     * @param $serviceId
     * @return array
     */
    public function deregisterService($serviceId): array
    {
        return $this->curlput(sprintf("%s:%d%s",$this->address,$this->port,'/v1/agent/service/deregister/' . $this->getServiceId($serviceId)),[]);
    }


    /**
     * @param array $options
     * @param array $availableOptions
     * @return array
     */
    protected function resolveOptions(array $options, array $availableOptions): array
    {
        return array_intersect_key($options, array_flip($availableOptions));
    }

    /**
     * @param string $method
     * @param string $url
     * @return array
     */
    private function request(string $method,string $url,$options = [],$body = []):array {
        try{
            if (! isset($options['base_uri'])) {
                $options['base_uri'] = $this->address.":".$this->port;
            }
            $saber = Saber::create($options);
            $res = $saber->{$method}($url,$body);
            return $res->getParsedJsonArray();
        }catch (\Throwable $e){
            Clog::error("consul request failed:method{$method},url:{$url}");
            Clog::error($e->getMessage());
            return [];
        }
    }

    /**
     * @param string $method
     * @param string $url
     * @param $param
     */
    private function curlput(string $url,$param){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-type:application/json']);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"PUT");
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($param));
        $ouput = curl_exec($ch);
        curl_close($ch);
        $res =  json_decode($ouput,true);
        if(empty($res)){
            return [];
        }
        return $res;

    }

}
