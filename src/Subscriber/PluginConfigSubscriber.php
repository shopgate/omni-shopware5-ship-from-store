<?php

namespace SgateShipFromStore\Subscriber;

use Doctrine\DBAL\Connection;
use Enlight\Event\SubscriberInterface;

class PluginConfigSubscriber implements SubscriberInterface
{
    /**
     * TODO replace dummy urls.
     */
    private $urls = [
        'orderStatusUrl' => '/api/SgateShipFromStoreUpdateOrder',
    ];

    /**
     * @var string
     */
    private $pluginName;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $shops = [];

    public function __construct(
        string $pluginName,
        Connection $connection
    ) {
        $this->pluginName = $pluginName;
        $this->connection = $connection;

        $this->loadShopData();
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Backend_Config' => 'addUrls',
        ];
    }

    public function addUrls(\Enlight_Event_EventArgs $args): void
    {
        $action = $args->getSubject()->Request()->getActionName();

        if ($action !== 'getForm') {
            return;
        }

        $view = $args->getSubject()->View();
        $data = (array) $view->getAssign('data');

        if ($data['name'] !== $this->pluginName) {
            return;
        }

        foreach ($data['elements'] as &$element) {
            $name = $element['name'];

            if (!isset($this->urls[$name])) {
                continue;
            }

            $existingValues = (array) $element['values'];
            $existingValues = array_combine(
                array_column($existingValues, 'shopId'),
                array_column($existingValues, 'id')
            );

            $values = [];

            foreach ($this->shops as $shop) {
                $values[] = [
                    'id' => $existingValues[$shop['id']] ?? null,
                    'shopId' => $shop['id'],
                    'value' => $shop['url'].'/'.trim($this->urls[$name], '/'),
                ];
            }

            $element['values'] = $values;
        }

        $view->assign('data', $data);
    }

    private function loadShopData(): void
    {
        $data = $this->connection->fetchAllAssociative('
            SELECT 
                `shop`.`id` as `id`,
                IFNULL(`shop`.`host`, `main`.`host`) as `host`,
                IF(`main`.`id` IS NULL, `shop`.`base_path`, `main`.`base_path`) as `basePath`,
                IF(`main`.`id` IS NULL, `shop`.`secure`, `main`.`secure`) as `secure`
            FROM `s_core_shops` `shop`
            LEFT JOIN `s_core_shops` `main` ON `shop`.`main_id` = `main`.`id`
        ');

        $data = array_combine(
            array_column($data, 'id'),
            $data
        );

        foreach ($data as &$shop) {
            $url = $shop['secure'] ? 'https://' : 'http://';
            $url .= trim((string) $shop['host'], '/').'/'.trim((string) $shop['basePath'], '/');

            $shop['url'] = rtrim($url, '/');
        }

        $this->shops = $data;
    }
}
