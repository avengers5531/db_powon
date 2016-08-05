<?php

use \Powon\Dao\DAOFactory as DAOFactory;
use Powon\Services\Implementation\GroupPageServiceImpl;
use Powon\Services\Implementation\GroupServiceImpl;
use Powon\Services\Implementation\InvoiceServiceImpl;
use Powon\Services\Implementation\MemberPageServiceImpl;
use \Powon\Services\Implementation\MemberServiceImpl;
use Powon\Services\Implementation\PostServiceImpl;
use \Powon\Services\Implementation\SessionServiceImpl;
use \Powon\Services\Implementation\RelationshipServiceImpl;

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
     * @var \Powon\Dao\InterestDAO
     */
    $interestDAO = $c['daoFactory']->getInterestDAO();

    $regionDAO = $c['daoFactory']->getRegionDAO();

    $professionDAO = $c['daoFactory']->getProfessionDao();

    /**
     * @var \Psr\Log\LoggerInterface
     */
    $logger = $c['logger'];

    $memberService = new MemberServiceImpl($logger, $memberDAO, $interestDAO, $professionDAO, $regionDAO);
    return $memberService;
};

// Member Page Service
$container['memberPageService'] = function ($c) {
    /**
     * @var \Powon\Dao\MemberDAO
     */
    $memberPageDAO = $c['daoFactory']->getMemberPageDAO();

    /**
     * @var \Psr\Log\LoggerInterface
     */
    $logger = $c['logger'];

    $memberPageService = new MemberPageServiceImpl($logger, $memberPageDAO);
    return $memberPageService;
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

    /**
     * @var $invoiceService \Powon\Services\InvoiceService
     */
    $invoiceService = $c['invoiceService'];

    $sessionService = new SessionServiceImpl($log,$daoFactory->getMemberDAO(), $daoFactory->getSessionDAO(), $invoiceService);
    // ADDITIONAL optional CONFIGURATION BELOW
    if (isset($c['settings']['session'])) {
        $settings = $c['settings']['session'];
        if (isset($settings['expiration'])) {
            $sessionService->setExpiration($settings['expiration']);
        }
    }
    return $sessionService;
};

// Group Service
$container['groupService'] = function ($c) {
    /**
     * @var \Powon\Dao\GroupDAO
     */
    $groupDAO = $c['daoFactory']->getGroupDAO();
    $isGroupMemberDAO = $c['daoFactory']->getIsGroupMemberDAO();

    /**
     * @var \Psr\Log\LoggerInterface
     */
    $logger = $c['logger'];

    $groupService = new GroupServiceImpl($logger, $groupDAO, $isGroupMemberDAO);
    return $groupService;
};


$container['groupPageService'] = function($c) {
    $logger = $c['logger'];
    $groupPageDao = $c['daoFactory']->getGroupPageDao();
    return new GroupPageServiceImpl($logger, $groupPageDao);
};
// Relationship Service
$container['relationshipService'] = function ($c) {
    /**
     * @var \Powon\Dao\MemberDAO
     */
    $relationshipDAO = $c['daoFactory']->getRelationshipDAO();

    /**
     * @var \Psr\Log\LoggerInterface
     */
    $logger = $c['logger'];

    $relationshipService = new RelationshipServiceImpl($logger, $relationshipDAO);
    return $relationshipService;

};

$container['postService'] = function($c) {
    $logger = $c['logger'];
    $daoFactory = $c['daoFactory'];

    return new PostServiceImpl($logger, $daoFactory->getPostDAO(), $daoFactory->getMemberDAO());
};

// Invoice Service
$container['invoiceService'] = function ($c) {
    /**
     * @var \Powon\Dao\InvoiceDAO
     */
    $invoiceDAO = $c['daoFactory']->getInvoiceDAO();

    /**
     * @var \Psr\Log\LoggerInterface
     */
    $logger = $c['logger'];

    $invoiceService = new InvoiceServiceImpl($logger, $invoiceDAO);
    // ADDITIONAL configuration
    if (isset($c['settings']['invoice'])) {
        $settings = $c['settings']['invoice'];
        if (isset($settings['grace_period'])) {
            $invoiceService->setGracePeriod($settings['grace_period']);
        }
        if (isset($settings['subscription_period'])) {
            $invoiceService->setSubscriptionPeriod($settings['subscription_period']);
        }

        if (isset($settings['subscription_fee'])) {
            $invoiceService->setSubscriptionFee($settings['subscription_fee']);
        }
    }
    return $invoiceService;
};
