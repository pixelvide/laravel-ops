<?php

namespace Pixelvide\Ops;

use Pixelvide\Ops\Exceptions\DFPActionNotSetException;
use Pixelvide\Ops\Exceptions\DFPAppIdNotSetException;
use Pixelvide\Ops\Exceptions\DFPVisitorIpNotSetException;
use Pixelvide\Ops\Exceptions\DFPVisitorTokenNotSetException;
use Pixelvide\Ops\Exceptions\DFPVisitorUaNotSetException;

class DFPRequest
{
    /**
     * @var string
     */
    private $action = null;
    /**
     * @var string
     */
    private $appId;
    /**
     * @var string
     */
    private $visitorIp;
    /**
     * @var string
     */
    private $visitorToken;
    /**
     * @var string
     */
    private $visitorUa;
    private $extraParams = [];

    private function isNullOrEmptyString($str): bool
    {
        return ($str === null || trim($str) === '');
    }

    /**
     * @throws DFPActionNotSetException
     * @throws DFPAppIdNotSetException
     * @throws DFPVisitorIpNotSetException
     * @throws DFPVisitorTokenNotSetException
     * @throws DFPVisitorUaNotSetException
     */
    public function validate()
    {
        if ($this->isNullOrEmptyString($this->action)) {
            throw new DFPActionNotSetException('Action not set');
        }
        if ($this->isNullOrEmptyString($this->appId)) {
            throw new DFPAppIdNotSetException('AppId not set');
        }
        if ($this->isNullOrEmptyString($this->visitorIp)) {
            throw new DFPVisitorIpNotSetException('VisitorIp not set');
        }
        if ($this->isNullOrEmptyString($this->visitorToken)) {
            throw new DFPVisitorTokenNotSetException('VisitorToken not set');
        }
        if ($this->isNullOrEmptyString($this->visitorUa)) {
            throw new DFPVisitorUaNotSetException('VisitorUA not set');
        }
    }

    public function buildPayload(): array
    {
        $data = [
            "action"       => $this->action,
            "appId"        => $this->appId,
            "visitorToken" => $this->visitorToken,
            "visitorIp"    => $this->visitorIp,
            "visitorUa"    => $this->visitorUa,
        ];
        foreach ($this->extraParams as $key => $val) {
            $data[$key] = $val;
        }
        return $data;
    }

    public function addExtraParams(string $key, $val): DFPRequest
    {
        $this->extraParams[$key] = $val;
        return $this;
    }

    public function setAction(string $action): DFPRequest
    {
        $this->action = $action;
        return $this;
    }

    public function setAppId(string $appId): DFPRequest
    {
        $this->appId = $appId;
        return $this;
    }

    public function setVisitorIp(string $visitorIp): DFPRequest
    {
        $this->visitorIp = $visitorIp;
        return $this;
    }

    public function setVisitorToken(string $visitorToken): DFPRequest
    {
        $this->visitorToken = $visitorToken;
        return $this;
    }

    public function setVisitorUa(string $visitorUa): DFPRequest
    {
        $this->visitorUa = $visitorUa;
        return $this;
    }
}