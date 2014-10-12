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
		Tasks::getMaxVisits();
    }

    public function uninstall()
    {
        \Piwik\Db::dropTables(Common::prefixTable('performancemonitor_maxvisits'));
    }

}
