<?php

namespace Dustin\ImpEx\Transaction;

use Dustin\ImpEx\Encapsulation\ReflectionEncapsulation;

class Action extends ReflectionEncapsulation
{
    private string $action;

    private string $entity;

    private array $payload = [];

    public static function create(string $action, string $entity, array $payloads): self
    {
        return new self([
            'action' => $action,
            'entity' => $entity,
            'payload' => array_map(function (PayloadEmergeInterface $p) { return $p->emergePayload(); }, $payloads),
        ]);
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): void
    {
        $this->entity = $entity;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = [];

        foreach ($payload as $p) {
            $this->addPayload($p);
        }
    }

    public function addPayload(Payload $payload): void
    {
        $this->payload[] = $payload;
    }
}
