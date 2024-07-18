<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        
        //We check if our object is an HTTP exception
        if($exception instanceof HttpException) {
            $data = [
                //If that's the case, we are getting the status code and the associated message.
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage()
            ];
        } else {
            $data = [
                'status' => 500, //Status does not exist, therefore it's not an HTTP excep => 500 by default
                'message' => $exception->getMessage()
            ];
            $event->setResponse(new JsonResponse($data));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
