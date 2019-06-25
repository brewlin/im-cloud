<?php

namespace Discovery\Provider;

use Core\Console\Console;
use Swlib\SaberGM;
use Swoft\Log\Helper\CLog;
use Swoole\Coroutine\Http\Client;

/**
 * Consul provider
 *
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
     * Specifies a unique ID for this service. This must be unique per agent. This defaults to the Name parameter if not provided.
     *
     * @var string
     */
    private $registerId = '';

    /**
     * Specifies the logical name of the service. Many service instances may share the same logical service name.
     *
     * @var string
     */
    private $registerName = APP_NAME;

    /**
     * Specifies a list of tags to assign to the service. These tags can be used for later filtering and are exposed via the APIs.
     *
     * @var array
     */
    private $registerTags = [];

    /**
     * Specifies to disable the anti-entropy feature for this service's tags
     *
     * @var bool
     */
    private $registerEnableTagOverride = false;

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
     * Specifies the checked ID
     *
     * @var string
     */
    private $registerCheckId = '';

    /**
     * Specifies the checked name
     *
     * @var string
     */
    private $registerCheckName = APP_NAME;

    /**
     * Specifies the checked tcp
     *
     * @var string
     */
    private $registerCheckTcp = '127.0.0.1:8099';

    /**
     * Specifies the checked interval
     *
     * @var int
     */
    private $registerCheckInterval = 10;

    /**
     * Specifies the checked timeout
     *
     * @var int
     */
    private $registerCheckTimeout = 1;

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
     * @Value(name="${config.provider.consul.discovery.tag}", env="${CONSUL_DISCOVERY_TAG}")
     * @var string
     */
    private $discoveryTag = "";

    /**
     * Specifies that the server should return only nodes with all checks in the passing state
     *
     * @Value(name="${config.provider.consul.discovery.passing}", env="${CONSUL_DISCOVERY_PASSING}")
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
     * get service list
     *
     * @param string $serviceName
     * @param array  $params
     *
     * @return array
     */
    public function getServiceList(string $serviceName, ...$params)
    {
        $url        = $this->getDiscoveryUrl($serviceName);
        $result = SaberGM::get(sprintf("http://%s:%d%s",$this->address,$this->port,$url));
        $services = $result->getParsedJsonArray();

        // 数据格式化
        $nodes = [];
        foreach ($services as $service) {
            if (!isset($service['Service'])) {
                CLog::warning("consul[Service] 服务健康节点集合，数据格式不不正确，Data=" . $result);
                continue;
            }
            $serviceInfo = $service['Service'];
            if (!isset($serviceInfo['Address'], $serviceInfo['Port'])) {
                CLog::warning("consul[Address] Or consul[Port] 服务健康节点集合，数据格式不不正确，Data=" . $result);
                continue;
            }
            $address = $serviceInfo['Address'];
            $port    = $serviceInfo['Port'];

            $uri     = implode(":", [$address, $port]);
            $nodes[] = $uri;
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

        $this->initService(...$params);

        $this->putService($this->registerParam, self::REGISTER_PATH);

        return true;
    }

    /**
     * init config
     * @param array ...$param
     */
    public function initService(...$param){
        if(empty($param["consul"])){
            $param = require_once ROOT."/config/discovery.php";
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
            $register['ID'] = sprintf('service-%s-%s', $register["Name"], $hostName);
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
     * CURL注册服务
     *
     * @param array  $service 服务信息集合
     * @param string $url     consulURI
     */
    private function putService(array $service, string $url)
    {
        try{
            $res = SaberGM::put(sprintf("%s:%d%s",$this->address,$this->port,$url),json_encode($service));
        }catch (\Throwable $e){
            CLog::error($e->getMessage());
        }
        if($res->success){
            Console::writeln(sprintf('<success>RPC service register success by consul ! tcp=%s:%d</success>', $this->registerAddress, $this->registerPort));
        }else{
            Console::writeln(sprintf('<error>RPC service register success by consul ! tcp=%s:%d</error>', $this->registerAddress, $this->registerPort));
        }
    }
}
