<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/9/2
 * Time: 21:37
 */

/**
 * Class Protocol
 */
class Protocol
{
    //max size
    const MaxBodySize = 1 << 12;
    // size
    const	 _packSize      = 4;
    const   _headerSize    = 2;
    const   _verSize       = 2;
    const   _opSize        = 4;
    const   _seqSize       = 4;
    const   _heartSize     = 4;
    const   _rawHeaderSize = self::_packSize + self::_headerSize + self::_verSize + self::_opSize + self::_seqSize;
    const   _maxPackSize   = self::MaxBodySize + self::_rawHeaderSize;
    // offset
    const   _packOffset   = 0;
    const   _headerOffset = self::_packOffset + self::_packSize;
    const   _verOffset    = self::_headerOffset + self::_headerSize;
    const _opOffset     = self::_verOffset + self::_verSize;
    const   _seqOffset    = self::_opOffset + self::_opSize;
    const   _heartOffset  = self::_seqOffset + self::_seqSize;
    /**
     * @var
     */
    private $buf;
    /**
     * @var
     */
    private $op;
    /**
     * @var
     */
    private $ver = 1;
    /**
     * @var
     */
    private $seq = 1;
    /**
     * @param $data
     * @return string
     */
    public function pack()
    {
        if(!is_string($this->buf)){
            $buf = json_encode($this->buf);
        }
        $packLen = Protocol::_rawHeaderSize + strlen($buf);
        $header = pack("NnnNN",$packLen,Protocol::_rawHeaderSize,$this->ver,$this->op,$this->seq);
        $buf = $header.$buf;
        return $buf;
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
        return $this->buf;
    }

    /**
     * @param $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

}