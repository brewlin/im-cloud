<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Im\Logic;

/**
 */
class LogicClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Ping Service
     * @param \Im\Logic\PingReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Ping(\Im\Logic\PingReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.logic.Logic/Ping',
        $argument,
        ['\Im\Logic\PingReply', 'decode'],
        $metadata, $options);
    }

    /**
     * Close Service
     * @param \Im\Logic\CloseReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function CClose(\Im\Logic\CloseReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.logic.Logic/CClose',
        $argument,
        ['\Im\Logic\CloseReply', 'decode'],
        $metadata, $options);
    }

    /**
     * Connect
     * @param \Im\Logic\ConnectReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Connect(\Im\Logic\ConnectReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.logic.Logic/Connect',
        $argument,
        ['\Im\Logic\ConnectReply', 'decode'],
        $metadata, $options);
    }

    /**
     * Disconnect
     * @param \Im\Logic\DisconnectReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Disconnect(\Im\Logic\DisconnectReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.logic.Logic/Disconnect',
        $argument,
        ['\Im\Logic\DisconnectReply', 'decode'],
        $metadata, $options);
    }

    /**
     * Heartbeat
     * @param \Im\Logic\HeartbeatReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Heartbeat(\Im\Logic\HeartbeatReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.logic.Logic/Heartbeat',
        $argument,
        ['\Im\Logic\HeartbeatReply', 'decode'],
        $metadata, $options);
    }

    /**
     * RenewOnline
     * @param \Im\Logic\OnlineReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function RenewOnline(\Im\Logic\OnlineReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.logic.Logic/RenewOnline',
        $argument,
        ['\Im\Logic\OnlineReply', 'decode'],
        $metadata, $options);
    }

    /**
     * Receive
     * @param \Im\Logic\ReceiveReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Receive(\Im\Logic\ReceiveReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.logic.Logic/Receive',
        $argument,
        ['\Im\Logic\ReceiveReply', 'decode'],
        $metadata, $options);
    }

    /**
     * ServerList
     * @param \Im\Logic\NodesReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Nodes(\Im\Logic\NodesReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.logic.Logic/Nodes',
        $argument,
        ['\Im\Logic\NodesReply', 'decode'],
        $metadata, $options);
    }

    /**
     * PushKeys push by key
     * @param \Im\Logic\PushKeysReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function PushKeys(\Im\Logic\PushKeysReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.logic.Logic/PushKeys',
        $argument,
        ['\Im\Logic\PushKeysReply', 'decode'],
        $metadata, $options);
    }

    /**
     * PushMids push by mids
     * @param \Im\Logic\PushMidsReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function PushMids(\Im\Logic\PushMidsReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.logic.Logic/PushMids',
        $argument,
        ['\Im\Logic\PushMidsReply', 'decode'],
        $metadata, $options);
    }

    /**
     * BroadcastRoom broadcast to one room
     * @param \Im\Logic\PushRoomReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function PushRoom(\Im\Logic\PushRoomReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.logic.Logic/PushRoom',
        $argument,
        ['\Im\Logic\PushRoomReply', 'decode'],
        $metadata, $options);
    }

    /**
     * Boradcast broadcast all of the server
     * @param \Im\Logic\PushAllReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function PushAll(\Im\Logic\PushAllReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.logic.Logic/PushAll',
        $argument,
        ['\Im\Logic\PushAllReply', 'decode'],
        $metadata, $options);
    }

}
