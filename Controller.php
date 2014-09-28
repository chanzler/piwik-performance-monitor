<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PerformanceMonitor;

use Piwik\View;
use Piwik\Piwik;
use Piwik\Common;
use Piwik\Settings\SystemSetting;
use Piwik\Settings\UserSetting;
use Piwik\Settings\Manager as SettingsManager;

/**
 *
 */
class Controller extends \Piwik\Plugin\Controller
{

    private function getPluginSettings()
    {
        $pluginsSettings = SettingsManager::getPluginSettingsForCurrentUser();
        ksort($pluginsSettings);
        return $pluginsSettings;
    }
    public function index()
    {
		$settings = new Settings('PerformanceMonitor');

        $view = new View('@PerformanceMonitor/index.twig');
        $this->setBasicVariablesView($view);
        $view->idSite = $this->idSite;
        $view->visits = API::getCurrentVisitors($this->idSite);
        $view->maxVisits = API::getMaxVisitors($this->idSite);
        $view->refreshInterval = (int)$settings->refreshInterval->getValue();
		$view->pluginSettings = $settings;

        return $view->render();
    }
    public function summary()
    {
        Piwik::checkUserHasSomeViewAccess();

        $date   = Common::getRequestVar('date', 'today');
        $period = Common::getRequestVar('period', 'day');

        $view = new View('@PerformanceMonitor/summary.twig');
        $this->setGeneralVariablesView($view);
        $view->sites = API::getSites();
        $view->pluginName = Piwik::translate('PerformanceMonitor_WidgetName');
        $view->description = Piwik::translate('PerformanceMonitor_Description');

        return $view->render();
    }
}
