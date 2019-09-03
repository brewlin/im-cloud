<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/2
 * Time: 21:32
 */

require "./Protocol.php";
require "./Operation.php";
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
        $revData = $this->protocol->unpack($data);
        var_dump($revData);
    }
    /**
     * @param $cli
     */
    public function clientConnect($cli)
    {
        echo "client  is connect\n";
        $this->auth();
        \Swoole\Timer::after(10000,function (){
            Swoole\Timer::tick($this->heartbeat,[$this,"heartbeat"]);
        });
    }

    /**
     * auth
     * @return void
     */
    public function auth()
    {
        $this->send(Operation::OpAuth,$this->auth);
    }

    /**
     * hearte
     */
    public function heartbeat()
    {
        $this->send(Operation::OpHeartbeat,"heartbeat");
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
        $this->protocol = new Protocol();
        $this->connection();
    }

    /**
     * connection the cloud server,register
     */
    public function connection(){
        $client = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);
        $client->on('connect',[$this,'clientConnect']);
        $client->on('receive',[$this,'clientReceive']);
        $client->on('error',[$this,'clientError']);
        $client->on('close',[$this,'clientClose']);
        $this->client = $client;
        $this->client->connect(IP,PORT);
    }

    /**
     * @param $buf
     */
    public function send($op ,$buf){

        $this->protocol->setOperation($op);
        $this->protocol->setBody($buf);
        $buf = $this->protocol->pack();
        var_dump($buf);
        /** Client */
        $this->trigger($this->client);
        $this->trigger($this->client->send($buf));

    }

    /**
     * @param $res
     */
    public function trigger($res)
    {
        if($res instanceof \Swoole\Client){
            var_dump("debug check client is connect");
            if(!$res->isConnected()){
                $this->connection();
            }
        }
    }
}
