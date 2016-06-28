<?php

namespace Powon\Middleware;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Powon\Services\SessionService;
use Psr\Log\LoggerInterface;

/**
 * Class SessionLoader
 * @package Powon\Middleware
 * For each request, it interacts with the request and response headers via cookies to
 * handle the session with the client.
 */
class SessionLoader
{
    const COOKIE_NAME = 'powon_token';
    /**
     * @var SessionService $sessionService
     */
    private $sessionService;

    /**
     * @var LoggerInterface $log
     */
    private $log;

    public function __construct(LoggerInterface $log, SessionService $ss)
    {
        $this->sessionService = $ss;
        $this->log = $log;
    }

    /**
     * Middleware invoke function taking request and response
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $response = $this->loadSessionFromRequest($request, $response);
        $response = $next($request, $response);
        $response = $this->setSessionResponse($response);
        
        // TODO garbage collect sometimes
        
        return $response;
    }

    /**
     * Populates the session service
     * @param $request \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function loadSessionFromRequest($request, $response) {
        $cookie = FigRequestCookies::get($request, self::COOKIE_NAME);
        $token = $cookie->getValue();
        //$this->log->debug("Token is: $token");
        if (!$token) {
            return $response;
        }
        $result = $this->sessionService->loadSession($token);
        if ($result == SessionService::SESSION_EXPIRED || $result == SessionService::SESSION_DOES_NOT_EXIST) {
            return FigResponseCookies::expire($response, self::COOKIE_NAME);
        }
        return $response;
    }

    /**
     * Sets the correct cookie headers in the response
     * @param $reponse \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function setSessionResponse($response) {
        if (!$this->sessionService->isAuthenticated()) {
            if ($this->sessionService->userHasJustLoggedOut()) {
                $response = FigResponseCookies::expire($response, self::COOKIE_NAME);
            }
            return $response;
        } else { // update or set
            $session = $this->sessionService->getSession();
            $expires = $session->getLastAccess() + $this->sessionService->getTokenValidityPeriod();
            $response = FigResponseCookies::remove($response, self::COOKIE_NAME);
            $response = FigResponseCookies::set($response, SetCookie::create(self::COOKIE_NAME)
            ->withValue($session->getToken())
            ->withExpires($expires)
            ->withHttpOnly(true)); // so that it isn't accessible via javascript.
            return $response;
        }
    }



}