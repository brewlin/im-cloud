<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/2
 * Time: 21:32
 */

require "./Protocol.php";
/**
 * Class tcp client sdk
 */
class client
{
    /**
     * @var \Swoole\Client
     */
    private $client;
    /**
     * @var Protocol
     */
    private $protocol;

    /**
     * @var float|int ms
     */
    private $heartbeat = 1000 * 10;
    /**
     * @var string
     */
    private $auth = '{"mid":123, "room_id":"live://1000", "platform":"web", "accepts":[1000,1001,1002]}';
    /**
     * @param $cli
     * @param $data
     */
    public function clientReceive($cli,$data)
    {
        echo "recv data from cloud\n";
    }
    /**
     * @param $cli
     */
    public function clientConnect($cli)
    {
        echo "client  is connect\n";
        $this->auth();
        Swoole\Timer::tick($this->heartbeat,[$this,"heartbeat"]);
    }

    public function auth()
    {
        $this->protocol->setBody($this->auth);
        $this->protocol->setOperation(Operation::OpAuth);
        $buf = $this->protocol->pack();
        /** Client */
        $this->trigger($this->client);
        $this->trigger($this->client->send($buf));
    }

    /**
     * hearte
     */
    public function heartbeat()
    {
        $this->protocol->setOperation(Operation::OpHeartbeat);
        $this->protocol->setBody("heartbeat");
        $buf = $this->protocol->pack();
        /** Client */
        $this->trigger($this->client);
        $this->trigger($this->client->send($buf));
    }

    /**
     * @param $cli
     */
    public function clientError($cli)
    {
        echo "client 1 is error\n";
    }

    /**
     * @param $cli
     */
    public function clientClose($cli)
    {
        echo "client 1 is close\n";
    }

    /**
     * client constructor.
     */
    public function start()
    {
        $client = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);
        $client->on('connect',[$this,'clientConnect']);
        $client->on('receive',[$this,'clientReceive']);
        $client->on('error',[$this,'clientError']);
        $client->on('close',[$this,'clientClose']);
        $this->client = $client;
        $this->protocol = new Protocol();
        $this->client->connect(IP,PORT);
    }

    public function reconnection(){
        $client = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);
        $client->on('connect',[$this,'clientConnect']);
        $client->on('receive',[$this,'clientReceive']);
        $client->on('error',[$this,'clientError']);
        $client->on('close',[$this,'clientClose']);
        $this->client = $client;
        $this->client->connect(IP,PORT);
    }
    public function trigger($res)
    {
        if($res instanceof \Swoole\Client){
            if(!$res->isConnected()){
                $this->reconnection();
            }
        }
        var_dump($res);
    }
}
