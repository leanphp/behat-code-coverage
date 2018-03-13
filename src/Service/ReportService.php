<?php
/**
 * Code Coverage Report Service
 *
 * @copyright 2013 Anthon Pang
 * @license BSD-2-Clause
 */

namespace LeanPHP\Behat\CodeCoverage\Service;

use LeanPHP\Behat\CodeCoverage\Common\Report\Factory;
use SebastianBergmann\CodeCoverage\CodeCoverage;

/**
 * Code coverage report service
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class ReportService
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var \LeanPHP\Behat\CodeCoverage\Common\Report\Factory
     */
    private $factory;

    /**
     * Constructor
     *
     * @param array                                      $config
     * @param \LeanPHP\Behat\CodeCoverage\Common\Report\Factory $factory
     */
    public function __construct(array $config, Factory $factory)
    {
        $this->config  = $config;
        $this->factory = $factory;
    }

    /**
     * Generate report
     *
     * @param CodeCoverage $coverage
     */
    public function generateReport(CodeCoverage $coverage)
    {
        if(!empty($this->config['report']['format']) && !empty($this->config['report']['options'])){

            $format = $this->config['report']['format'];
            $options = $this->config['report']['options'];

            $report = $this->factory->create($format, $options);
            $report->process($coverage);

        }elseif(!empty($this->config['report']['format']) && !empty($this->config['report']['output'])){

            foreach($this->config['report']['format'] AS $format){

                if(isset($this->config['report']['output'][$format])){

                    if(is_array($this->config['report']['output'][$format])) {

                        $report = $this->factory->create($format, $this->config['report']['output'][$format]);
                        $report->process($coverage);

                    }else{

                        $report = $this->factory->create($format, array('target' => $this->config['report']['output'][$format]));
                        $report->process($coverage);

                    }

                }

            }

        }

    }
}
