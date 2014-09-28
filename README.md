# Piwik PerformanceMonitor Plugin

## Description

This is a plugin for the Open Source Web Analytics platform Piwik. If enabled, it will add a new widget that you can add to your dashboard an a new link in the top navigation.

The widget will show the performance index of a site that auto-refresh every 30 seconds. It shows the number of visitors or visit time in a 30 minute period compared to the maximum number of visitors in any 30 minute period of the last 30 days.

This plugin is inspired by the [piwik barometer plugin](https://github.com/halfdan/piwik-barometer-plugin) and uses a lightly modified jQuery-Dynameter (original by [Tzechiu Lei](http://tze1.com/dynameter/).

**This plugin should run fine with installations with up to 100.000 page impressions per day. If you run a very large piwik installation and have performance issues with this plugin, please contact me - there is a solution for this. I have it up and running in an installation with more than 5 million visits per day.**

(Tested with piwik 2.7.0, but supposed to run with older versions)

## Installation

Install it via Piwik Marketplace OR install manually:

1. Clone the plugin into the plugins directory of your Piwik installation.

   ```
   cd plugins/
   git clone https://github.com/chanzler/piwik-performance-monitor.git PerformanceMonitor
   ```

2. Login as superuser into your Piwik installation and activate the plugin under Settings -> Plugins

3. You will now find the widget under the Live! section.

## FAQ

Here is a list of features that are included in this project:

* Define new widget ("Performance Monitor")
* Add an item to the top navigation ("Performance overview") which displays the performance monitor widget for all your configured sites.

## Changelog

### 0.2.0 Second Beta
* fixed the timezone bug
* fixed several minor bugs
* overview has now links to the dashboards
* added settings for configuration
* 
### 0.1.0 First Beta
* initial release

## License

GPL v3 or later

## Support

* Please direct any feedback to [frank@intersolve.de](mailto:frank@intersolve.de)

## Contribute

If you are interested in contributing to this plugin, feel free to send pull requests!

