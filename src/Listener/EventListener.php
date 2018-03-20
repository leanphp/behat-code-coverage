<?php
/**
 * Event Listener
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use LeanPHP\Behat\CodeCoverage\Service\ReportService;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class EventListener implements EventSubscriberInterface
{
    /**
     * @var CodeCoverage
     */
    private $coverage;

    /**
     * @var \LeanPHP\Behat\CodeCoverage\Service\ReportService
     */
    private $reportService;

    /**
     * Constructor
     *
     * @param CodeCoverage                                      $coverage
     * @param \LeanPHP\Behat\CodeCoverage\Service\ReportService $reportService
     */
    public function __construct(CodeCoverage $coverage, ReportService $reportService)
    {
        $filter = new Filter();
        $config = $reportService->getConfig();

        $this->addDirectoryToWhitelist($config, $filter);
        $this->addFileToWhitelist($config, $filter);

        $cc = new CodeCoverage(null, $filter);

        $this->coverage = $cc;
        $this->reportService = $reportService;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ExerciseCompleted::BEFORE => 'beforeExercise',
            ScenarioTested::BEFORE => 'beforeScenario',
            ExampleTested::BEFORE  => 'beforeScenario',
            ScenarioTested::AFTER  => 'afterScenario',
            ExampleTested::AFTER   => 'afterScenario',
            ExerciseCompleted::AFTER => 'afterExercise',
        );
    }

    /**
     * Before Exercise hook
     *
     * @param \Behat\Testwork\EventDispatcher\Event\ExerciseCompleted $event
     */
    public function beforeExercise(ExerciseCompleted $event)
    {
        $this->coverage->clear();
    }

    /**
     * Before Scenario/Outline Example hook
     *
     * @param \Behat\Behat\EventDispatcher\Event\ScenarioTested $event
     */
    public function beforeScenario(ScenarioTested $event)
    {
        $node = $event->getScenario();
        $id   = $event->getFeature()->getFile() . ':' . $node->getLine();

        $this->coverage->start($id);
    }

    /**
     * After Scenario/Outline Example hook
     *
     * @param \Behat\Behat\EventDispatcher\Event\ScenarioTested $event
     */
    public function afterScenario(ScenarioTested $event)
    {
        $this->coverage->stop();
    }

    /**
     * After Exercise hook
     *
     * @param \Behat\Testwork\Tester\Event\ExerciseCompleted $event
     */
    public function afterExercise(ExerciseCompleted $event)
    {
        $this->reportService->generateReport($this->coverage);
    }

    /**
     * @param array  $config
     * @param Filter $filter
     */
    private function addDirectoryToWhitelist(array $config, Filter $filter): void
    {
        foreach ($config['filter']['whitelist']['include']['directories'] as $directory) {
            $filter->addDirectoryToWhitelist($directory['prefix']);
        }
    }

    /**
     * @param array  $config
     * @param Filter $filter
     */
    private function addFileToWhitelist(array $config, Filter $filter): void
    {
        foreach ($config['filter']['whitelist']['include']['files'] as $file) {
            $filter->addFileToWhitelist($file['prefix']);
        }
    }
}
