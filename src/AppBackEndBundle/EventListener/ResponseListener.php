<?php

namespace AppBackEndBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class ResponseListener
 *
 * @package AppBackEndBundle\EventListener
 */
class ResponseListener
{
    /**
     * Provides additional common information (status, time, etc.) fore all of API responses.
     *
     * @param FilterResponseEvent $event
     *
     * @return bool False in case if it's not an API call.
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        // Only for api calls.
        if (false === strpos($request->getUri(), '/api')) {
            return false;
        }

        $content = json_decode($response->getContent(), true);

        if (null === $content) {
            return;
        }

        $content = [
            'status' => $response->getStatusCode(),
            'time' => date("Y-m-d H:i:s", time()),
        ] + $content;

        $response->setContent(json_encode($content));
    }
}