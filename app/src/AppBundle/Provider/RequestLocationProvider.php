<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 25/08/17
 * Time: 13:47
 */

namespace AppBundle\Provider;


use AppBundle\Model\RequestLocalisation;
use AppBundle\Utils\LocalisationUtils;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestLocationProvider
{
    /**
     * @var RequestStack
     */
    private $request;

    /**
     * RequestLocationProvider constructor.
     * @param RequestStack $request
     */
    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public function getRequestLocation(): RequestLocalisation
    {
        $current = $this->request->getCurrentRequest();

        $from    = $current->query->get("addressFrom");
        $to      = $current->query->get("addressTo");
        $from    = $from ?? $current->request->get("addressFrom");
        $to      = $to ?? $current->request->get("addressTo");

        if ($from === null || $to === null) {
            throw new \UnexpectedValueException("unable to find addressFrom or addressTo in request");
        }

        return (new RequestLocalisation())
            ->setAddressFrom($from)
            ->setPositionFrom(LocalisationUtils::getCoordonateByAddress($from))
            ->setAddressTo($to)
            ->setPositionTo(LocalisationUtils::getCoordonateByAddress($to));
    }
}