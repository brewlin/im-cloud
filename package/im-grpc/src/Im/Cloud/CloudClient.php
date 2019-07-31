<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Im\Cloud;

/**
 */
class CloudClient extends \Grpc\BaseStub {

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
     * @param \Im\Cloud\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Ping(\Im\Cloud\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.cloud.Cloud/Ping',
        $argument,
        ['\Im\Cloud\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * Close Service
     * @param \Im\Cloud\PBEmpty $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function CClose(\Im\Cloud\PBEmpty $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.cloud.Cloud/CClose',
        $argument,
        ['\Im\Cloud\PBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * PushMsg push by key or mid
     * @param \Im\Cloud\PushMsgReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function PushMsg(\Im\Cloud\PushMsgReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.cloud.Cloud/PushMsg',
        $argument,
        ['\Im\Cloud\PushMsgReply', 'decode'],
        $metadata, $options);
    }

    /**
     * Broadcast send to every enrity
     * @param \Im\Cloud\BroadcastReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Broadcast(\Im\Cloud\BroadcastReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.cloud.Cloud/Broadcast',
        $argument,
        ['\Im\Cloud\BroadcastReply', 'decode'],
        $metadata, $options);
    }

    /**
     * BroadcastRoom broadcast to one room
     * @param \Im\Cloud\BroadcastRoomReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function BroadcastRoom(\Im\Cloud\BroadcastRoomReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.cloud.Cloud/BroadcastRoom',
        $argument,
        ['\Im\Cloud\BroadcastRoomReply', 'decode'],
        $metadata, $options);
    }

    /**
     * Rooms get all rooms
     * @param \Im\Cloud\RoomsReq $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function Rooms(\Im\Cloud\RoomsReq $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/im.cloud.Cloud/Rooms',
        $argument,
        ['\Im\Cloud\RoomsReply', 'decode'],
        $metadata, $options);
    }

}
