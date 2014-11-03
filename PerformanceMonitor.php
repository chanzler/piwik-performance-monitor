<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PerformanceMonitor;

use Piwik\WidgetsList;
use Piwik\Common;
use \Exception;

/**
 */
class PerformanceMonitor extends \Piwik\Plugin
{
    /**
     * @see Piwik\Plugin::getListHooksRegistered
     */
    public function getListHooksRegistered()
    {
        return array(
            'AssetManager.getJavaScriptFiles' => 'getJsFiles',
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'WidgetsList.addWidgets' => 'addWidget',
        );
    }

    public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = 'plugins/PerformanceMonitor/javascripts/performancemonitor.js';
        $jsFiles[] = 'plugins/PerformanceMonitor/javascripts/jquery.dynameter.js';
        $jsFiles[] = 'plugins/PerformanceMonitor/javascripts/odometer.min.js';
    }

    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = "plugins/PerformanceMonitor/stylesheets/performancemonitor.css";
        $stylesheets[] = "plugins/PerformanceMonitor/stylesheets/odometer-theme-default.css";
    }

    /**
     * Add Widget to Live! >
     */
    public function addWidget()
    {
        WidgetsList::add( 'Live!', 'PerformanceMonitor_WidgetName', 'PerformanceMonitor', 'index');
    }

    public function install()
    {
        try {
            $sql = "CREATE TABLE " . Common::prefixTable('performancemonitor_maxvisits') . " (
                        idsite INT( 10 ) NOT NULL ,
                        maxvisits INT( 11 ) NOT NULL
                    )";
            \Piwik\Db::exec($sql);
        } catch (Exception $e) {
            // ignore error if table already exists (1050 code is for 'table already exists')
            if (!\Piwik\Db::get()->isErrNo($e, '1050')) {
                throw $e;
            }
        }
       	foreach (API::getSites() as $site)
        {
	        $idSite = $site['id'];
	        $lastMinutes = 30;
	        $histPeriodOfTime = 30;
	        $settings = new Settings('PerformanceMonitor');
	        if ($settings != null) {
	        	if ($settings->histPeriodOfTime->getValue()){
	        		$histPeriodOfTime = (int)$settings->histPeriodOfTime->getValue();
	        	} else {
	        		$histPeriodOfTime = 30;
	        	}
	        	if ($settings->currPeriodOfTime->getValue()){
	        		$lastMinutes = (int)$settings->currPeriodOfTime->getValue();
	        	} else {
	        		$lastMinutes = 30;
	        	}
	        }
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
//        	\Piwik\Db::query($insert, array(
//        			$idSite, $maxvisits
//        	));
        }        
    }

    public function uninstall()
    {
        \Piwik\Db::dropTables(Common::prefixTable('performancemonitor_maxvisits'));
    }

}
