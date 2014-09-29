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


/**
 * API for plugin PerformanceMonitor
 *
 * @method static \Piwik\Plugins\PerformanceMonitor\API getInstance()
 */
class API extends \Piwik\Plugin\API {

    /**
     * Retrieves visit count from lastMinutes and peak visit count from lastDays
     * in lastMinutes interval for site with idSite.
     *
     * @param int $idSite
     * @param int $lastMinutes
     * @param int $lastDays
     * @return int
     */
    public static function getVisitorCounter($idSite, $lastMinutes = 30)
    {
        \Piwik\Piwik::checkUserHasViewAccess($idSite);
		$settings = new Settings('PerformanceMonitor');
        $lastMinutes = (int)$lastMinutes;
        $periodOfTime = (int)$settings->periodOfTime->getValue();
        if (ini_get('date.timezone')) {
			$dateTimeZoneDEF = new \DateTimeZone(ini_get('date.timezone'));
		} else {
			$dateTimeZoneDEF = new \DateTimeZone(date_default_timezone_get());
		}
		$dateTimeZoneUTC = new \DateTimeZone("UTC");
		$dateTimeUTC = new \DateTime("now", $dateTimeZoneUTC);
		$dateTimeDEF = new \DateTime("now", $dateTimeZoneDEF);
		$timeZoneDiff = $dateTimeZoneDEF->getOffset($dateTimeDEF)-$dateTimeZoneUTC->getOffset($dateTimeUTC);

        $sql = "SELECT MAX(g.concurrent) AS maxvisit
                FROM (
                  SELECT    COUNT(idvisit) as concurrent
                  FROM      ". \Piwik\Common::prefixTable("log_visit") . "
                  WHERE     DATE_SUB(NOW(), INTERVAL ? DAY) < visit_last_action_time
                  AND       idsite = ?
                  GROUP BY  round(UNIX_TIMESTAMP(visit_last_action_time) / ?)
        ) g";

        $maxvisits = \Piwik\Db::fetchOne($sql, array(
            $periodOfTime, $idSite, $lastMinutes * 60
        ));

        $sql = "SELECT COUNT(*)
                FROM " . \Piwik\Common::prefixTable("log_visit") . "
                WHERE idsite = ?
                AND DATE_SUB(NOW(), INTERVAL ? MINUTE) < visit_last_action_time";

        $visits = \Piwik\Db::fetchOne($sql, array(
            $idSite, $lastMinutes+($timeZoneDiff/60)
        ));

        return array(
            'maxvisits' => (int)$maxvisits,
            'visits' => (int)$visits
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
