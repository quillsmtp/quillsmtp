<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace QuillSMTP\Vendor\Monolog\Handler;

use QuillSMTP\Vendor\Monolog\Formatter\FormatterInterface;
use QuillSMTP\Vendor\Monolog\Formatter\NormalizerFormatter;
use QuillSMTP\Vendor\Monolog\Level;
use QuillSMTP\Vendor\Monolog\LogRecord;
/**
 * Handler sending logs to Zend Monitor
 *
 * @author  Christian Bergau <cbergau86@gmail.com>
 * @author  Jason Davis <happydude@jasondavis.net>
 */
class ZendMonitorHandler extends AbstractProcessingHandler
{
    /**
     * @throws MissingExtensionException
     */
    public function __construct(int|string|Level $level = Level::Debug, bool $bubble = \true)
    {
        if (!\function_exists('QuillSMTP\\Vendor\\zend_monitor_custom_event')) {
            throw new MissingExtensionException('You must have Zend Server installed with Zend Monitor enabled in order to use this handler');
        }
        parent::__construct($level, $bubble);
    }
    /**
     * Translates Monolog log levels to ZendMonitor levels.
     */
    protected function toZendMonitorLevel(Level $level) : int
    {
        return match ($level) {
            Level::Debug => \QuillSMTP\Vendor\ZEND_MONITOR_EVENT_SEVERITY_INFO,
            Level::Info => \QuillSMTP\Vendor\ZEND_MONITOR_EVENT_SEVERITY_INFO,
            Level::Notice => \QuillSMTP\Vendor\ZEND_MONITOR_EVENT_SEVERITY_INFO,
            Level::Warning => \QuillSMTP\Vendor\ZEND_MONITOR_EVENT_SEVERITY_WARNING,
            Level::Error => \QuillSMTP\Vendor\ZEND_MONITOR_EVENT_SEVERITY_ERROR,
            Level::Critical => \QuillSMTP\Vendor\ZEND_MONITOR_EVENT_SEVERITY_ERROR,
            Level::Alert => \QuillSMTP\Vendor\ZEND_MONITOR_EVENT_SEVERITY_ERROR,
            Level::Emergency => \QuillSMTP\Vendor\ZEND_MONITOR_EVENT_SEVERITY_ERROR,
        };
    }
    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record) : void
    {
        $this->writeZendMonitorCustomEvent($record->level->getName(), $record->message, $record->formatted, $this->toZendMonitorLevel($record->level));
    }
    /**
     * Write to Zend Monitor Events
     * @param string       $type      Text displayed in "Class Name (custom)" field
     * @param string       $message   Text displayed in "Error String"
     * @param array<mixed> $formatted Displayed in Custom Variables tab
     * @param int          $severity  Set the event severity level (-1,0,1)
     */
    protected function writeZendMonitorCustomEvent(string $type, string $message, array $formatted, int $severity) : void
    {
        zend_monitor_custom_event($type, $message, $formatted, $severity);
    }
    /**
     * @inheritDoc
     */
    public function getDefaultFormatter() : FormatterInterface
    {
        return new NormalizerFormatter();
    }
}
