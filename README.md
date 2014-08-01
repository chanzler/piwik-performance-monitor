# Piwik PerformanceMonitor Plugin

## Description

This is a plugin for the Open Source Web Analytics platform Piwik. If enabled, it will add a new widget that you can add to your dashboard an a new link in the top navigation.

The widget will show the performance index of a site that auto-refresh every 30 seconds. It shows the number of visitors or visit time in a 30 minute period compared to the maximum number of visitors in any 30 minute period of the last 30 days.

This plugin is inspired by the [piwik barometer plugin](https://github.com/halfdan/piwik-barometer-plugin) and uses a lightly modified jQuery-Dynameter (original by [Tzechiu Lei](http://tze1.com/dynameter/).

## Documentation

1. Clone the plugin into the plugins directory of your Piwik installation.

   ```
   cd plugins/
   git clone https://github.com/chanzler/piwik-performance-monitor.git PerformanceMonitor
   ```

2. Login as superuser into your Piwik installation and activate the plugin under Settings -> Plugins

3. You will now find the widget under the Live! section.

## Contribute

If you are interested in contributing to this plugin, feel free to send pull requests!

