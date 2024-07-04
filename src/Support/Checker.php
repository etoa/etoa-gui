<?php

namespace EtoA\Support;

use Symfony\Component\HttpFoundation\RequestStack;

class Checker
{
    public function __construct(
        private readonly RequestStack $requestStack,
    )
    {
    }

    public function checker_init(int $debug = 0):string
    {
        $session = $this->requestStack->getSession();
        $flashes = $session->getFlashBag();

        $session->set('checker',md5(mt_rand(0, 99999999) . time()));
        if ($session->get('checker_last')) {
            while ($session->get('checker_last') == $session->get('checker')) {
                $session->set('checker_last',md5(mt_rand(0, 99999999) . time()));
            }
        }
        $session->set('checker_last',$session->get('checker'));
        if ($debug == 1)
            $flashes->add(
                'warning',
                "Checker initialized with " . $session->get('checker')
            );

        return $session->get('checker');
    }

    /**
     * The form checker - verify
     */
    public function checker_verify($debug = 0):bool
    {
        $session = $this->requestStack->getSession();
        $request = $this->requestStack->getCurrentRequest()->request;
        $checker = $request->all(array_key_first($request->all()))['checker'];
        $flashes = $session->getFlashBag();

        if ($debug == 1)
            $flashes->add(
                'warning',
                "Checker-Session is: " . $session->get('checker') . ", Checker-POST is: " . $checker
            );
        if ($session->get('checker') && $checker && $session->get('checker') == $checker) {
            $session->set('checker',NULL);
            return true;
        } else {
            $flashes->add(
                'warning',
                'Seite kann nicht mehrfach aufgerufen werden!'
            );
            return false;
        }
    }
}