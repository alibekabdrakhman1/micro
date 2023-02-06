<?php


namespace Centras\Layers\Infrastructure\Logger\Graylog;

use Illuminate\Support\Str;

class RoundTrip extends Graylog
{
    /**
     * в данном массиве будет храниться уникальный ID каждого запроса
     *
     * @var array
     */
    protected array $requestIDS = [];

    /**
     * Тело запроса Payload - а для запросов в GRAYLOG
     *
     * @var array
     */
    protected array $payload = [
        "version" => "1.1",
        "host" => null,
        "title" => null,
        "partner_id" => null,
        "global_id" => null,
        "request_id" => null,
        "payment_id" => null,
        "product_id" => null,
        "payload" => []
    ];

    /**
     *
     */
    public function build()
    {
        $this->payload['host'] = gethostname();
        $this->payload['title'] = 'запросы с микросервиса: ' . gethostname();
    }

    /**
     * @param string $id
     * @return RoundTrip
     */
    public function setGlobalId(string $id): static
    {
//        $this->payload['global_id'] = $id;
        $this->payload['order_id'] = $id;

        return $this;
    }

    /**
     * @param string $id
     * @return RoundTrip
     */
    public function setPartnerId(string $id): static
    {
        $this->payload['partner_id'] = $id;

        return $this;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setPaymentId(string $id): static
    {
        $this->payload['payment_id'] = $id;

        return $this;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setProductId(string $id): static
    {
        $this->payload['product_id'] = $id;

        return $this;
    }

    /**
     *
     */
    public function request(array $data)
    {
        $this->payload['request_id'] = $this->generateRequestId();
        $this->payload['payload'] = $data;
    }

    /**
     * @param array $data
     */
    public function response(array $data)
    {
        $this->payload['request_id'] = $this->getLastId();
        $this->payload['payload'] = $data;
    }

    /**
     *
     */
    protected function generateRequestId(): string
    {
        $requestId = 'req_' . Str::uuid();

        $this->requestIDS[] = $requestId;

        return $requestId;
    }

    /**
     * @return string
     */
    protected function getLastId(): string
    {
        return array_pop($this->requestIDS);
    }
}
