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
    public $currPeriodOfTime;
	
    /** @var SystemSetting */
    public $histPeriodOfTime;
	
    /** @var SystemSetting */
    public $sites;

    protected function init()
    {
        $this->setIntroduction(Piwik::translate('PerformanceMonitor_SettingsIntroduction'));

        // System setting --> textbox converted to int defining a validator and filter
        $this->createRefreshIntervalSetting();

        // System setting --> textbox converted to int defining a validator and filter
        $this->createCurrentPeriodOfTimeSetting();
        
        // System setting --> textbox converted to int defining a validator and filter
        $this->createHistoricalPeriodOfTimeSetting();
        
        // System setting --> allows selection of multiple values
        $this->createSitesSetting();

    }

    private function createRefreshIntervalSetting()
    {
        $this->refreshInterval        = new SystemSetting('refreshInterval', Piwik::translate('PerformanceMonitor_SettingsRefreshInterval'));
        $this->refreshInterval->readableByCurrentUser = true;
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

    private function createCurrentPeriodOfTimeSetting()
    {
        $this->currPeriodOfTime        = new SystemSetting('currPeriodOfTime', Piwik::translate('PerformanceMonitor_SettingsCPOT'));
        $this->currPeriodOfTime->readableByCurrentUser = true;
        $this->currPeriodOfTime->type  = static::TYPE_INT;
        $this->currPeriodOfTime->uiControlType = static::CONTROL_TEXT;
        $this->currPeriodOfTime->uiControlAttributes = array('size' => 3);
        $this->currPeriodOfTime->description     = Piwik::translate('PerformanceMonitor_SettingsCPOTDescription');
        $this->currPeriodOfTime->inlineHelp      = Piwik::translate('PerformanceMonitor_SettingsCPOTHelp');
        $this->currPeriodOfTime->defaultValue    = '20';
        $this->currPeriodOfTime->validate = function ($value, $setting) {
            if ($value > 30 && $value < 1) {
                throw new \Exception('Value is invalid');
            }
        };

        $this->addSetting($this->currPeriodOfTime);
    }

    private function createHistoricalPeriodOfTimeSetting()
    {
        $this->histPeriodOfTime        = new SystemSetting('histPeriodOfTime', Piwik::translate('PerformanceMonitor_SettingsHPOT'));
        $this->histPeriodOfTime->readableByCurrentUser = true;
        $this->histPeriodOfTime->type  = static::TYPE_INT;
        $this->histPeriodOfTime->uiControlType = static::CONTROL_TEXT;
        $this->histPeriodOfTime->uiControlAttributes = array('size' => 3);
        $this->histPeriodOfTime->description     = Piwik::translate('PerformanceMonitor_SettingsHPOTDescription');
        $this->histPeriodOfTime->inlineHelp      = Piwik::translate('PerformanceMonitor_SettingsHPOTHelp');
        $this->histPeriodOfTime->defaultValue    = '30';
        $this->histPeriodOfTime->validate = function ($value, $setting) {
            if ($value > 30 && $value < 1) {
                throw new \Exception('Value is invalid');
            }
        };

        $this->addSetting($this->histPeriodOfTime);
    }

    private function createSitesSetting()
    {
    	$this->sites        = new SystemSetting('sites', Piwik::translate('PerformanceMonitor_SettingsSites'));
		$this->sites->readableByCurrentUser = true;
        $this->sites->type  = static::TYPE_ARRAY;
        $this->sites->uiControlType = static::CONTROL_MULTI_SELECT;
        $this->sites->availableValues = array();
        $this->sites->description     = Piwik::translate('PerformanceMonitor_SettingsSitesDescription');
        $this->sites->defaultValue    = array();
        foreach (API::getSites() as $site)
        {
        	$this->sites->availableValues[$site["id"]] = $site["name"];
        	array_push ($this->sites->defaultValue, $site["id"]);
        }
        $this->sites->readableByCurrentUser = true;

        $this->addSetting($this->sites);
    }
}
