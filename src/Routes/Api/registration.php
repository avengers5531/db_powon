<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/api/v1/login', function (Request $request, Response $response) {
    $params['username'] = $request->getHeader('username');
    $params['password'] = $request->getHeader('password');
    if (isset($params['username']) && isset($params['password'])) {
        $response->getBody()->write(json_encode($params));
        return $response->withHeader('Content-Type','application/json')->withStatus(200);
    } else {
        return $response->withStatus(403, 'Forbidden');
    }
});

$app->get('/api/v1/members', function (Request $request, Response $response) {
    /**
     * @var $logger \Psr\Log\LoggerInterface
     */
    $logger = $this->logger;

    /**
     * @var $memberDAO \Powon\Dao\MemberDAO
     */
    $memberDAO = $this->daoFactory->getMemberDAO();

    $logger->info("Member json list");
    $members = $memberDAO->getAllMembers();
    $memberObjects = array_map(function(\Powon\Entity\Member $each) {
        return $each->toObject();
    }, $members);
    $response->getBody()->write(json_encode($memberObjects));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

$app->post('/api/v1/register', function(Request $request, Response $response) {
    $params = $request->getParsedBody();
});