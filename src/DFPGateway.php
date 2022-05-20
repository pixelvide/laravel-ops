<?php

namespace Pixelvide\Ops;

use Aws\Lambda\LambdaClient;
use Aws\Sqs\SqsClient;
use Pixelvide\Ops\Exceptions\DFPActionNotSetException;
use Pixelvide\Ops\Exceptions\DFPAppIdNotSetException;
use Pixelvide\Ops\Exceptions\DFPVisitorIpNotSetException;
use Pixelvide\Ops\Exceptions\DFPVisitorTokenNotSetException;
use Pixelvide\Ops\Exceptions\DFPVisitorUaNotSetException;
use Pixelvide\Ops\Exceptions\VariableMissingException;

class DFPGateway
{
    /**
     * @var mixed
     */
    protected $dfpGatewayEndpoint;
    /**
     * @var LambdaClient
     */
    protected $lambdaClient;

    /**
     * AWSGateway constructor.
     */
    public function __construct()
    {
        $this->dfpGatewayEndpoint = env('DFP_GATEWAY_ENDPOINT');
        $awsConfig                = [
            'region'  => env('DFP_GATEWAY_REGION', 'ap-south-1'),
            'version' => '2015-03-31',
        ];
        if (env('AWS_PROFILE')) {
            $awsConfig['profile'] = env('AWS_PROFILE');
        }
        $this->lambdaClient = new LambdaClient($awsConfig);
    }

    private function isNullOrEmptyString($str): bool
    {
        return ($str === null || trim($str) === '');
    }

    /**
     * @param  DFPRequest  $dfpRequest
     * @return array
     *
     * @throws VariableMissingException
     */
    private function gatewayPayload(DFPRequest $dfpRequest): array
    {
        if ($this->isNullOrEmptyString($this->dfpGatewayEndpoint)) {
            throw new VariableMissingException('DFP_GATEWAY_ENDPOINT variable is either unspecified or empty');
        }
        return [
            'FunctionName' => $this->dfpGatewayEndpoint,
            'Payload'      => json_encode($dfpRequest->buildPayload()),
        ];
    }

    /**
     * @throws DFPActionNotSetException
     * @throws DFPAppIdNotSetException
     * @throws DFPVisitorIpNotSetException
     * @throws DFPVisitorTokenNotSetException
     * @throws DFPVisitorUaNotSetException
     * @throws VariableMissingException
     */
    public function send(DFPRequest $dfpRequest)
    {
        $dfpRequest->validate();
        $response = $this->lambdaClient->invoke($this->gatewayPayload($dfpRequest));
        $result   = $response->get('Payload')
            ->getContents();
        return json_decode($result, true);
    }
}