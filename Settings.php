<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\PerformanceMonitor;

use Piwik\Settings\SystemSetting;
use Piwik\Settings\UserSetting;
use Piwik\Piwik;

/**
 * Defines Settings for PerformanceMonitorPlugin.
 *
 */
class Settings extends \Piwik\Plugin\Settings
{
    /** @var SystemSetting */
    public $refreshInterval;

    /** @var SystemSetting */
    public $periodOfTime;
	
    /** @var SystemSetting */
    public $sites;

    protected function init()
    {
        $this->setIntroduction(Piwik::translate('PerformanceMonitor_SettingsIntroduction'));

        // User setting --> textbox converted to int defining a validator and filter
        $this->createRefreshIntervalSetting();

        // User setting --> textbox converted to int defining a validator and filter
        $this->createPeriodOfTimeSetting();

        // System setting --> allows selection of multiple values
        $this->createSitesSetting();

    }

    private function createRefreshIntervalSetting()
    {
        $this->refreshInterval        = new SystemSetting('refreshInterval', Piwik::translate('PerformanceMonitor_SettingsRefreshInterval'));
        $this->refreshInterval->type  = static::TYPE_INT;
        $this->refreshInterval->uiControlType = static::CONTROL_TEXT;
        $this->refreshInterval->uiControlAttributes = array('size' => 3);
        $this->refreshInterval->description     = Piwik::translate('PerformanceMonitor_SettingsRefreshIntervalDescription');
        $this->refreshInterval->inlineHelp      = Piwik::translate('PerformanceMonitor_SettingsRefreshIntervalHelp');
        $this->refreshInterval->defaultValue    = '30';
        $this->refreshInterval->validate = function ($value, $setting) {
            if ($value < 1) {
                throw new \Exception('Value is invalid');
            }
        };

        $this->addSetting($this->refreshInterval);
    }

    private function createPeriodOfTimeSetting()
    {
        $this->periodOfTime        = new SystemSetting('periodOfTime', Piwik::translate('PerformanceMonitor_SettingsPOT'));
        $this->periodOfTime->type  = static::TYPE_INT;
        $this->periodOfTime->uiControlType = static::CONTROL_TEXT;
        $this->periodOfTime->uiControlAttributes = array('size' => 3);
        $this->periodOfTime->description     = Piwik::translate('PerformanceMonitor_SettingsPOTDescription');
        $this->periodOfTime->inlineHelp      = Piwik::translate('PerformanceMonitor_SettingsPOTHelp');
        $this->periodOfTime->defaultValue    = '1';
        $this->periodOfTime->validate = function ($value, $setting) {
            if ($value > 30 && $value < 1) {
                throw new \Exception('Value is invalid');
            }
        };

        $this->addSetting($this->periodOfTime);
    }

    private function createSitesSetting()
    {
    	$this->sites        = new SystemSetting('sites', Piwik::translate('PerformanceMonitor_SettingsSites'));
        $this->sites->type  = static::TYPE_ARRAY;
        $this->sites->uiControlType = static::CONTROL_MULTI_SELECT;
        $this->sites->availableValues = array();
        $this->sites->description     = Piwik::translate('PerformanceMonitor_SettingsSitesDescription');
        $this->sites->defaultValue    = array();
        foreach (API::getSites() as &$site)
        {
        	$this->sites->availableValues[$site["id"]] = $site["name"];
        	array_push ($this->sites->defaultValue, $site["id"]);
        }
        $this->sites->readableByCurrentUser = true;

        $this->addSetting($this->sites);
    }
}
