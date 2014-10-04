<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PerformanceMonitor;

use Piwik\API\Request;
use \DateTimeZone;
use Piwik\Settings\SystemSetting;
use Piwik\Settings\UserSetting;
use Piwik\Settings\Manager as SettingsManager;
use Piwik\Site;
use Piwik\Plugins\VisitsSummary\API as VisitsSummaryAPI;


/**
 * API for plugin PerformanceMonitor
 *
 * @method static \Piwik\Plugins\PerformanceMonitor\API getInstance()
 */
class API extends \Piwik\Plugin\API {

	public static function get_timezone_offset($remote_tz, $origin_tz = null) {
    		if($origin_tz === null) {
        		if(!is_string($origin_tz = date_default_timezone_get())) {
            			return false; // A UTC timestamp was returned -- bail out!
        		}
    		}
    		$origin_dtz = new \DateTimeZone($origin_tz);
    		$remote_dtz = new \DateTimeZone($remote_tz);
    		$origin_dt = new \DateTime("now", $origin_dtz);
    		$remote_dt = new \DateTime("now", $remote_dtz);
    		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
    		return $offset;
	}
    /**
     * Retrieves visit count from lastMinutes and peak visit count from lastDays
     * in lastMinutes interval for site with idSite.
     *
     * @param int $idSite
     * @param int $lastMinutes
     * @param int $lastDays
     * @return int
     */
    public static function getVisitorCounter($idSite)
    {
        \Piwik\Piwik::checkUserHasViewAccess($idSite);
		$settings = new Settings('PerformanceMonitor');
        $timeZone = (int)$settings->currPeriodOfTime->getValue();
        $lastMinutes = (int)$settings->currPeriodOfTime->getValue();
        $histPeriodOfTime = (int)$settings->histPeriodOfTime->getValue();
		$timeZoneDiff = API::get_timezone_offset('UTC', Site::getTimezoneFor($idSite));

        $sql = "SELECT COUNT(*)
                FROM " . \Piwik\Common::prefixTable("log_visit") . "
                WHERE idsite = ?
                AND DATE_SUB(NOW(), INTERVAL ? MINUTE) < visit_last_action_time";

        $visits = \Piwik\Db::fetchOne($sql, array(
            $idSite, $lastMinutes+($timeZoneDiff/60)
        ));
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
        if ($maxvisits < $visits) $maxvisits = $visits;
		
		$visitsSummary   = VisitsSummaryAPI::getInstance()->get($idSite, "day", "today", false, array('avg_time_on_site','nb_actions_per_visit','bounce_rate'));
        $firstRow = $visitsSummary->getFirstRow();
        if (empty($firstRow)) {
        }
        $engagedTime = $firstRow->getColumn('avg_time_on_site');
        $actions = $firstRow->getColumn('nb_actions_per_visit');
        $bounceRate = $firstRow->getColumn('bounce_rate');
        return array(
            'maxvisits' => (int)$maxvisits,
            'visits' => (int)$visits,
            'time' => (int)$engagedTime/60,
            'actions' => $actions,
            'bouncerate' => (int)$bounceRate
        );
    }

    public static function getMaxVisitors($idSite, $lastMinutes = 30)
    {
	$tmp = API::getVisitorCounter($idSite, $lastMinutes);
        return $tmp['maxvisits'];
    }

    public static function getCurrentVisitors($idSite, $lastMinutes = 30)
    {
	$tmp = API::getVisitorCounter($idSite, $lastMinutes);
        return $tmp['visits'];
    }

    /**
     * Fetches the list of sites which names match the string pattern
     *
     * @param $pattern
     * @return array|string
     */
    public static function getSites()
    {
        $idSites = array();
        $sites = Request::processRequest('SitesManager.getSitesWithAtLeastViewAccess');
        if (!empty($sites)) {
            foreach ($sites as $site) {
                $idSites[] = array(
                	"id" => $site['idsite'],
			"name" =>  $site['name']
		);
            }
        }
        return $idSites;
    }

}
