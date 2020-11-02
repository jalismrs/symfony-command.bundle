<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Common;

/**
 * Class EventCommandAbstract
 *
 * @package Jalismrs\Symfony\Common
 *
 * @codeCoverageIgnore
 */
abstract class EventCommandAbstract extends
    CommandAbstract
{
    /**
     * initConsoleEventSubscriber
     *
     * @param \Jalismrs\Symfony\Common\ConsoleEventSubscriberAbstract $consoleEventSubscriber
     *
     * @return void
     */
    protected function initConsoleEventSubscriber(
        ConsoleEventSubscriberAbstract $consoleEventSubscriber
    ) : void {
        $consoleEventSubscriber->setStyle($this->style);
    }
}
