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

/**
 * Class Packet
 * @package App\Packet
 * @Bean()
 */
class Packet implements PackerInterface
{
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

    public function pack($data):string
    {

    }

    /**
     * @param string $buf
     */
    public function unpack(string $buf)
    {
        if(strlen($buf) < self::_rawHeaderSize)
            throw new PacketException("packet error hearder not enough",0);
        //big endian int32
        $packlen = unpack("N",substr($buf,self::_packOffset,self::_headerOffset))[1];
        //big endian int16
        $headerlen = unpack("n",substr($buf,self::_headerOffset,self::_verOffset))[1];
        //big endian int16
        $ver = unpack("n",substr($buf,self::_verOffset,self::_opOffset))[1];
        //big endian int32
        $op = unpack("N",substr($buf,self::_opOffset,self::_seqOffset))[1];
        //big endian int32
        $seq = unpack("N",substr($buf,self::_seqOffset,self::_heartOffset))[1];
        if($packlen > self::_maxPackSize)
            throw new PacketException("packet body too max");
        if($headerlen != self::_rawHeaderSize)
            return false;
        return substr($buf,$headerlen,$packlen);
    }

}