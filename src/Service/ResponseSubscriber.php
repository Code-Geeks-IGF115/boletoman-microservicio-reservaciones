<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseSubscriber implements EventSubscriberInterface
{
    private $allow_host;

    public function __construct( $allow_host)
    {
        $this->allow_host = $allow_host;
    }

    public static function getSubscribedEvents(): array
    {
        return [ResponseEvent::class => 'onKernelResponse'];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Origin',$this->allow_host );
    }
}