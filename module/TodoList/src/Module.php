<?php
namespace TodoList;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;


class Module implements ConfigProviderInterface
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * This method returns an array of factories that are all merged together by the ModuleManager
     * before passing them to the ServiceManager
     * @return array of Factories
     */
    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\TaskTable::class => function($container) {
                    $tableGateway = $container->get(Model\TaskTableGateway::class);
                    return new Model\TaskTable($tableGateway);
                },
                Model\TaskTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Task());
                    return new TableGateway('task', $dbAdapter, null, $resultSetPrototype);
                },
                Model\UserTable::class => function($container) {
                    $tableGateway = $container->get(Model\UserTableGateway::class);
                    return new Model\UserTable($tableGateway);
                },
                Model\UserTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\User());
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    /**
     * Factories for the Controller
     * @return array of Factories
     */
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\TaskController::class => function($container) {
                    return new Controller\TaskController(
                        $container->get(Model\TaskTable::class)
                    );
                },
                Controller\AuthController::class => function($container) {
                    return new Controller\AuthController(
                        $container->get(Model\UserTable::class)
                    );
                },
            ],
        ];
    }
}



