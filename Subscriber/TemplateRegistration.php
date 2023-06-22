<?php

namespace SgateShipFromStore\Subscriber;

use Enlight\Event\SubscriberInterface;

class TemplateRegistration implements SubscriberInterface
{
    public const VIEWS_FOLDER = '/Resources/views';

    /**
     * @var string
     */
    private $pluginDirectory;

    public function __construct(string $pluginDir)
    {
        $this->pluginDirectory = $pluginDir;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch' => 'addTemplateDir',
        ];
    }

    public function addTemplateDir(\Enlight_Event_EventArgs $args): void
    {
        $view = $args->getSubject()->View();
        $view->addTemplateDir($this->pluginDirectory.self::VIEWS_FOLDER);
    }
}
