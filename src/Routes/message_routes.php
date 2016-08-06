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
            $messages = $this->messageService->getMessagesForMember($auth_member);
            $response = $this->view->render($response, 'messages.html', [
                'menu' => [
                  'active' => 'profile'
                ],
                'view' => 'inbox',
                'current_member' => $auth_member,
                'messages' => $messages
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

    /**
    * View a message
    */
    $this->get('/{id}', function(Request $request, Response $response){
        $msg_id = $request->getAttribute('id');
        $msg = $this->messageService->getMessageById($msg_id);
        $auth_member = $this->sessionService->getAuthenticatedMember();
        $is_recipient = $this->messageService->isRecipient($auth_member, $msg);
        if ($msg->getAuthorId() === $auth_member->getMemberId() || $is_recipient){
            $response = $this->view->render($response, 'message_page.html', [
                'menu' => [
                  'active' => 'profile'
                ],
                'current_member' => $auth_member,
                'message' => $msg,
                'is_recipient' => $is_recipient
            ]);
            return $response;
        }
        return $response->withStatus(403);
    })->setname('see_message');

});
