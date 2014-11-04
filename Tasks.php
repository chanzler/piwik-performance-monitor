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
        $this->hourly('getMaxVisits');  // method will be executed once every hour
    }

    public function getMaxVisits()
    {
       	foreach (API::getSites() as $site)
        {
			$idSite = $site['id'];
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

			$update = "UPDATE ". \Piwik\Common::prefixTable("performancemonitor_maxvisits") . "
		               SET maxvisits = ? WHERE idsite = ?";
			\Piwik\Db::query($update, array(
	        			($maxvisits)?$maxvisits:0, $idSite
			));


		}
    }
}
