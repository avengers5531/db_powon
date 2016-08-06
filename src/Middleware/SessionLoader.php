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

    private $view;

    /**
     * @var LoggerInterface $log
     */
    private $log;

    public function __construct(LoggerInterface $log, SessionService $ss, $view)
    {
        $this->sessionService = $ss;
        $this->log = $log;
        $this->view = $view;
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
        $uri_first = substr($request->getUri()->getPath(), 0, 5); // "/api/"
        //$this->log->debug("Uri is $uri");
        if ($uri_first !== '/api/' && $response->getStatusCode() === 404) {
            return $this->view->render($response, 'not_found.html', [
                'current_member' => $this->sessionService->getAuthenticatedMember()
            ]);
        } elseif ($uri_first !== '/api/' && $response->getStatusCode() === 403) {
            return $this->view->render($response, 'forbidden.html', [
                'current_member' => $this->sessionService->getAuthenticatedMember()
            ]);
        }
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
            if ($this->sessionService->getSessionState() == SessionService::SESSION_ENDED) {
                $response = FigResponseCookies::expire($response, self::COOKIE_NAME);
            }
            return $response;
        } else { // update or set
            $session = $this->sessionService->getSession();
            $expires = $session->getLastAccess() + $this->sessionService->getTokenValidityPeriod();
            $response = FigResponseCookies::remove($response, self::COOKIE_NAME);
            $cookie = SetCookie::create(self::COOKIE_NAME)->withValue($session->getToken())
                ->withPath('/')
                ->withHttpOnly(true);
            $sessData = $session->getSessionData();
            if (isset($sessData['remember']) && $sessData['remember'] === true) {
                $cookie = $cookie->withExpires($expires);
            }
            $response = FigResponseCookies::set($response, $cookie); // so that it isn't accessible via javascript.
            // save session to update last access time and also in case there was some session data added during this request.
            $this->sessionService->saveSession();
            return $response;
        }
    }



}
