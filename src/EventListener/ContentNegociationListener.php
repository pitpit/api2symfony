<?php

namespace Creads\Api2Symfony\EventListener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Negotiation\FormatNegotiatorInterface;
use Negotiation\NegotiatorInterface;
use Negotiation\Decoder\DecoderProviderInterface;

/**
 * Add attributes to request to help for content negociation in controller
 *
 * @see https://github.com/willdurand/StackNegotiation/blob/master/src/Negotiation/Stack/Negotiation.php
 *
 * @author Damien Pitard <damien.pitard@gmail.com>
 * @author William Durand <william.durand1@gmail.com>
 */
class ContentNegociationListener implements EventSubscriberInterface
{
    /**
     * @var HttpKernelInterface
     */
    private $app;

    /**
     * @var FormatNegotiatorInterface
     */
    private $formatNegotiator;

    /**
     * @var array
     */
    private $defaultOptions = [
        'format_priorities' => []
    ];

    /**
     * @var array
     */
    private $options;

    /**
     * Constructor.
     *
     * @param HttpKernelInterface       $app              An HttpKernelInterface instance
     * @param FormatNegotiatorInterface $formatNegotiator A FormatNegotiatorInterface instance
     * @param array                     $options          Array of options
     */
    public function __construct(
        HttpKernelInterface $app,
        FormatNegotiatorInterface $formatNegotiator,
        array $options = []
    ) {
        $this->app                = $app;
        $this->formatNegotiator   = $formatNegotiator;
        $this->options = array_merge($this->defaultOptions, $options);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            //make sure it's run before Symfony\Component\HttpKernel\EventListener\ExceptionListener
            KernelEvents::REQUEST => array('onKernelRequest', 64),
        );
    }

    /**
     * Guess and set _format parameter from Accept header in request
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // handle `Accept` header
        if (null !== $accept = $request->headers->get('Accept')) {
            $priorities = $this->formatNegotiator->normalizePriorities($this->options['format_priorities']);
            $accept     = $this->formatNegotiator->getBest($accept, $priorities);
            $request->attributes->set('_accept', $accept);
            if (null !== $accept && !$accept->isMediaRange()) {
                $request->attributes->set('_mime_type', $accept->getValue());
                $request->attributes->set('_format', $this->formatNegotiator->getFormat($accept->getValue()));
            }
        }
    }

}
