<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
// routes go here
$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");
    
    //return $this->renderer->render($response, 'index.phtml', ['name' => $name]);
    return $response;
});

$app->get('/members', function (Request $request, Response $response) {
    $this->logger->addInfo("Member list");
    $members = $this->memberService->getAllMembers();
    $response = $this->renderer->render($response, "members.phtml", ["members" => $members, "router" => $this->router]);
    return $response;
});

$app->get('/membersTwig', function (Request $request, Response $response) {
    $this->logger->addInfo("Member twig list");
    $memberDAO = $this->daoFactory->getMemberDAO();
    $members = $memberDAO->getAllMembers();
    $response = $this->view->render($response, "members.twig", ["members" => $members]);
    return $response;
});
