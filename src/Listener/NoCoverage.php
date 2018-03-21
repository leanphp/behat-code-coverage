<?php
/**
 * No Coverage Event Listener
 *
 * @copyright 2018 Danny Lewis
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Listener;

use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * No Coverage
 *
 * @author Danny Lewis
 */
class NoCoverage implements EventSubscriberInterface
{

    private $output;

    public function __construct(ConsoleOutput $output)
    {
        $this->output = $output;
    }

    public function showDisabledMessage(ExerciseCompleted $event)
    {
        $this->output->writeln('Code coverage is disabled, enable with "coverage" flag. e.g. bin/behat --coverage');
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ExerciseCompleted::BEFORE => array('showDisabledMessage', -10),
            ExerciseCompleted::AFTER => array('showDisabledMessage', -10),
        );
    }


}
