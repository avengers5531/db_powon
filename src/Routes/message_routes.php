<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;

$app->group('/messages', function(){
    /**
    * Inbox of the authenticated member
    */
    $this->get('', function(Request $request, Response $response){
        $auth_member = $this->sessionService->getAuthenticatedMember();
        $this->messageService->getMessagesForMember($auth_member);
        $response = $this->view->render($response, 'inbox.html', [

        ]);
        return $response;
    })->setname('inbox');
});
