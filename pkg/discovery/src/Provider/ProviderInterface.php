<?php

namespace Discovery\Provider;

/**
 * Provier interface
 */
interface ProviderInterface
{
    /**
     * @param string $serviceName
     * @param array  ...$params
     *
     * @return mixed
     */
    public function getServiceList(string $serviceName, ...$params);

    /**
     * @param array ...$params
     *
     * @return mixed
     */
    public function registerService(...$params);

    /**
     * @return mixed
     */
    public function checks();

    /**
     * @param array $options
     */
    public function members(...$params);

    /**
     * @return mixed
     */
    public function self();

    /**
     * @param $address
     * @param array $options
     * @return mixed
     */
    public function join($address, ...$params);

    /**
     * @param $node
     * @return mixed
     */
    public function forceLeave($node);

    /**
     * @param $check
     */
    public function registerCheck($check);

    /**
     * @param $checkId
     */
    public function deregisterCheck($checkId);

    /**
     * @param $checkId
     * @param array $options
     * @return mixed
     */
    public function passCheck($checkId, ...$params);

    /**
     * @param $checkId
     * @param array $options
     * @return mixed
     */
    public function warnCheck($checkId, ...$params);

    /**
     * @param $checkId
     * @param array $options
     * @return mixed
     */
    public function failCheck($checkId, ...$params);

    /**
     * @param $serviceId
     * @return mixed
     */
    public function deregisterService($serviceId);
}
