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


/**
 *
 */
class Controller extends \Piwik\Plugin\Controller
{

    public function index()
    {
        $view = new View('@PerformanceMonitor/index.twig');
        $this->setBasicVariablesView($view);
        $view->idSite = $this->idSite;
        $view->visits = API::getCurrentVisitors($this->idSite);
        $view->maxVisits = API::getMaxVisitors($this->idSite);

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
        $view->siteName = Piwik::translate('PerformanceMonitor_WidgetName');
        $view->description = Piwik::translate('PerformanceMonitor_Description');

        return $view->render();
    }
}
