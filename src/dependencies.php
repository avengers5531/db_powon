<?php

use \Powon\Dao\DAOFactory as DAOFactory;
use \Powon\Services\Implementation\MemberServiceImpl;
use \Powon\Services\Implementation\SessionServiceImpl;
use \Powon\Services\Implementation\RegistrationServiceImpl;

$container = $app->getContainer();

// view renderer
$container['renderer'] = function($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

$container['view'] = function($c) {
    $settings = $c['settings']['renderer'];
    $view = new \Slim\Views\Twig($settings['template_path'], [
        'cache' => false
    ]);
   $view->addExtension(new \Slim\Views\TwigExtension(
       $c['router'],
       $c['request']->getUri()
   ));
   return $view;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// PDO
$container['db'] = function($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host='.$db['host'].';port='. $db['port'] .';dbname='.$db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_TIMEOUT, 30);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

// DAO factory
$container['daoFactory'] = function ($c) {
    $pdo = $c['db'];
    $dao = new DAOFactory($pdo);
    return $dao;
};

// Member Service
$container['memberService'] = function ($c) {
    /**
     * @var \Powon\Dao\MemberDAO
     */
    $memberDAO = $c['daoFactory']->getMemberDAO();

    /**
     * @var \Psr\Log\LoggerInterface
     */
    $logger = $c['logger'];
    
    $memberService = new MemberServiceImpl($logger, $memberDAO);
    return $memberService;
};

// Session Service
$container['sessionService'] = function ($c) {
    /**
     * @var DAOFactory $daoFactory
     */
    $daoFactory = $c['daoFactory'];

    /**
     * @var \Psr\Log\LoggerInterface $log
     */
    $log = $c['logger'];

    $sessionService = new SessionServiceImpl($log,$daoFactory->getMemberDAO(), $daoFactory->getSessionDAO());
    // ADDITIONAL optional CONFIGURATION BELOW
    
    return $sessionService;
};

// Mock Group Service //DO NOT COMMIT
$container['groupService'] = function($c) {
    return new \Powon\Services\Implementation\GroupServiceMock();
};
