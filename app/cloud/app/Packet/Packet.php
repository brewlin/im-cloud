<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/7/30
 * Time: 22:51
 */

namespace App\Packet;
use Core\Container\Mapping\Bean;

/**
 * Class Packet
 * @package App\Packet
 * @Bean()
 */
class Packet
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

    public function pack()
    {

    }

    /**
     * @param string $buf
     */
    public function unpack(string &$buf)
    {

    }

}