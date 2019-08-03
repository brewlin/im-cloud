<?php
/**
 * Created by PhpStorm.
 * User: brewlin
 * Date: 2019/8/2 0002
 * Time: 下午 5:38
 */

namespace App\Packet;


class Protocol
{
    /**
     * heartbeat
     */
    const Heartbeat = 2;
    /**
     * heartbeat reply
     */
    const HeartbeatReplyOk = 3;
    /**
     * auth to connect the server
     */
    const Auth = 7;
    /**
     * auth
     */
    const AuthReplyOk = 8;

    /**
     * push to client
     */
    const PushClient = 9;
    /**
     * batch push
     */
    const BatchPushClient = 10;

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

}