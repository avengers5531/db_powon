<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response; // To get the withJson method.

$app->get('/api/v1/member', function (Request $request, Response $response) {
    /**
     * @var $sessionService \Powon\Services\SessionService
     */
    $sessionService = $this->sessionService;
    if (!$sessionService->isAuthenticated()) {
        return $response->withStatus(403);
    }
    /**
     * @var $memberService \Powon\Services\MemberService
     */
    $memberService = $this->memberService;

    $params = $request->getQueryParams();
    if (isset($params['username'])) {
        $member = $memberService->getMemberByUsername($params['username']);
        if ($member) {
            // Send the bare minimum data to avoid security breach
            return $response->withJson([
                'username' => $member->getUsername(),
                'member_id' => $member->getMemberId(),
                'user_email' => $member->getUserEmail()
            ]);
        } else {
            return $response->withStatus(404);
        }
    } else {
        return $response->withStatus(400);
    }
});
