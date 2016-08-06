<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Http\Response as Response;

$app->group('/messages', function(){
    /**
    * Inbox of the authenticated member
    */
    $this->get('', function(Request $request, Response $response){
        if ($this->sessionService->isAuthenticated()){
            $auth_member = $this->sessionService->getAuthenticatedMember();
            $inbox = $this->messageService->getMessagesForMember($auth_member);
            $response = $this->view->render($response, 'messages.html', [

            ]);
            return $response;
        }
        else{
            return $response->withRedirect('/');
        }
    })->setname('messages');

    /**
    * Send a message to other members.
    */
    $this->post('/send', function(Request $request, Response $response){
        if ($this->sessionService->isAuthenticated()){
            $auth_member = $this->sessionService->getAuthenticatedMember();
            $params = $request->getParsedBody();
            $send = $this->messageService->sendMessage($auth_member, $params);
        }
        return $response->withRedirect('/messages');
    })->setname('send_message');

});
