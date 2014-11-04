<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * 
 */
namespace Piwik\Plugins\PerformanceMonitor;

use Piwik\Menu\MenuTop;
use Piwik\Piwik;

class Menu extends \Piwik\Plugin\Menu
{
    public function configureTopMenu(MenuTop $menu)
    {
        $urlParams = array('module' => 'PerformanceMonitor', 'action' => 'summary', 'segment' => false);
        $tooltip   = Piwik::translate('PerformanceMonitor_TopLinkTooltip');

        $menu->add('PerformanceMonitor_PerformanceSummary', null, $urlParams, true, 3, $tooltip);
    }
}
