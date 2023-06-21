<?php

namespace SgateShipFromStore\Components\Order\Subscriber;

use Doctrine\DBAL\Connection;
use Enlight\Event\SubscriberInterface;

class BackendOrderSubscriber implements SubscriberInterface
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Backend_Order' => 'onPostDispatchOrder',
        ];
    }

    public function onPostDispatchOrder(\Enlight_Event_EventArgs $args): void
    {
        $action = $args->getSubject()->Request()->getActionName();

        switch ($action) {
            case 'load':
                $this->onLoad($args);

                return;

            case 'getList':
                $this->onGetList($args);

                return;
        }
    }

    protected function onLoad(\Enlight_Event_EventArgs $args)
    {
        $view = $args->getSubject()->View();

        $view->extendsTemplate('backend/sgate_order/view/detail/overview.js');
        $view->extendsTemplate('backend/sgate_order/model/order.js');
    }

    protected function onGetList(\Enlight_Event_EventArgs $args)
    {
        $view = $args->getSubject()->View();
        $orders = $view->getAssign('data');
        $sgateOrderNumbers = $this->fetchSgateOrderNumbers(array_unique(array_column($orders, 'id')));

        foreach ($orders as &$order) {
            $order['sgateShipFromStoreOrderNumber'] = $sgateOrderNumbers[$order['id']] ?? null;
        }

        $view->assign('data', $orders);
    }

    private function fetchSgateOrderNumbers(array $orderIds): array
    {
        $result = $this->connection->fetchAll(
            'SELECT `orderID` as `orderId`, `sgate_ship_from_store_order_number` as `sgateOrderNumber`
            FROM `s_order_attributes` WHERE `orderID` IN (:orderIds)',
            [
                'orderIds' => $orderIds,
            ], [
                'orderIds' => Connection::PARAM_INT_ARRAY,
            ]
        );

        return array_combine(
            array_column($result, 'orderId'),
            array_column($result, 'sgateOrderNumber')
        );
    }
}
