<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/30
 * Time: 22:51
 */

namespace App\Packet;
use Core\Container\Mapping\Bean;
use Core\Contract\PackerInterface;
use Im\Cloud\Proto;

/**
 * Class Packet
 * @package App\Packet
 * @Bean()
 */
class Packet implements PackerInterface
{
    /**
     * @var int
     */
	private $ver;
    /**
     * @var int
     */
	private $op;
    /**
     * @var array
     */
	private $body;
    /**
     * @var int
     */
	private $seq;

    /**
     * @param $data
     * @return string
     */
    public function pack($data):string
    {

    }


    /**
     * @param string $buf
     * @return $this|bool
     * @throws PacketException
     */
    public function unpack(string $buf)
    {
        if(strlen($buf) < Protocol::_rawHeaderSize)
            throw new PacketException("packet error hearder not enough",0);

        //big endian int32
        $packlen = unpack("N",substr($buf,Protocol::_packOffset,Protocol::_headerOffset))[1];
        //big endian int16
        $headerlen = unpack("n",substr($buf,Protocol::_headerOffset,Protocol::_verOffset))[1];
        //big endian int16
        $this->ver = unpack("n",substr($buf,Protocol::_verOffset,Protocol::_opOffset))[1];
        //big endian int32
        $this->op = unpack("N",substr($buf,Protocol::_opOffset,Protocol::_seqOffset))[1];
        //big endian int32
        $this->seq = unpack("N",substr($buf,Protocol::_seqOffset,Protocol::_heartOffset))[1];

        if($packlen > Protocol::_maxPackSize)
            throw new PacketException("packet body too max");

        if($headerlen != Protocol::_rawHeaderSize)
            return false;

        $body = substr($buf,$headerlen,$packlen);
        $this->body = json_decode($body,true);
        return $this;
    }

    /**
     * @return int
     */
    public function getOperation()
    {
        return $this->op;
    }

    /**
     * @param int $op
     */
    public function setOperation(int $op)
    {
        $this->op = $op;
    }
    /**
     * @return int
     */
    public function getVer()
    {
        return $this->ver;
    }

    /**
     * @param int $ver
     */
    public function setVer(int $ver)
    {
        $this->ver = $ver;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param array $body
     */
    public function setBody(array $body)
    {
        $this->body = $body;
    }

}