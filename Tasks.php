<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PerformanceMonitor;

use Piwik\Site;

class Tasks extends \Piwik\Plugin\Tasks
{
    public function schedule()
    {
        $this->hourly('myTask');  // method will be executed once every hour
        //$this->daily('myTask');   // method will be executed once every day
        //$this->weekly('myTask');  // method will be executed once every week
        //$this->monthly('myTask'); // method will be executed once every month

        // pass a parameter to the task
        //$this->weekly('myTaskWithParam', 'anystring');

        // specify a different priority
        //$this->monthly('myTask', null, self::LOWEST_PRIORITY);
        //$this->monthly('myTaskWithParam', 'anystring', self::HIGH_PRIORITY);
    }

    public function myTask()
    {
		$idSite = 1;
		$settings = new Settings('PerformanceMonitor');
        $histPeriodOfTime = (int)$settings->histPeriodOfTime->getValue();
        $lastMinutes = (int)$settings->currPeriodOfTime->getValue();
		$sql = "SELECT MAX(g.concurrent) AS maxvisit
                FROM (
                  SELECT    COUNT(idvisit) as concurrent
                  FROM      ". \Piwik\Common::prefixTable("log_visit") . "
                  WHERE     DATE_SUB(NOW(), INTERVAL ? DAY) < visit_last_action_time
                  AND       idsite = ?
                  GROUP BY  round(UNIX_TIMESTAMP(visit_last_action_time) / ?)
        ) g";

        $maxvisits = \Piwik\Db::fetchOne($sql, array(
            $histPeriodOfTime, $idSite, $lastMinutes * 60
        ));

		$insert = "INSERT INTO ". \Piwik\Common::prefixTable("performancemonitor_maxvisits") . "
                     (idsite, maxvisits) VALUES (?, ?)";
		\Piwik\Db::query($insert, array(
            $idSite, $maxvisits
        ));
    }

    public function myTaskWithParam($param)
    {
        // do something
    }
}