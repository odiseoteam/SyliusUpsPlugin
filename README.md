<h1 align="center">
    <a href="https://odiseo.com.ar/" target="_blank" title="Odiseo">
        <img src="https://github.com/odiseoteam/SyliusUpsPlugin/blob/master/logo_odiseo.png" alt="Odiseo" width="300px" />
    </a>
    <br />
    Odiseo Sylius UPS Plugin [WIP]
    <br />
    <a href="https://packagist.org/packages/odiseoteam/sylius-ups-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/odiseoteam/sylius-ups-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/odiseoteam/sylius-ups-plugin" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/odiseoteam/sylius-ups-plugin.svg" />
    </a>
    <a href="http://travis-ci.org/odiseoteam/SyliusUpsPlugin" title="Build status" target="_blank">
        <img src="https://img.shields.io/travis/odiseoteam/SyliusUpsPlugin/master.svg" />
    </a>
    <a href="https://scrutinizer-ci.com/g/odiseoteam/SyliusUpsPlugin/" title="Scrutinizer" target="_blank">
        <img src="https://img.shields.io/scrutinizer/g/odiseoteam/SyliusUpsPlugin.svg" />
    </a>
    <a href="https://packagist.org/packages/odiseoteam/sylius-ups-plugin" title="Total Downloads" target="_blank">
        <img src="https://poser.pugx.org/odiseoteam/sylius-ups-plugin/downloads" />
    </a>
</h1>

## Description

This plugin add UPS shipping method to the Sylius project.

<img src="https://github.com/odiseoteam/SyliusUpsPlugin/blob/master/screenshot_1.png" alt="Ups calculator">

This is a WIP plugin, so we aprecciate your feedback and your colaboration. Thanks!

#### Features

- Add a UPS Shipping method calculator.

#### Upcoming features

- Add label printing for the shipments.
- Add tracking system.

## Demo

You can see this plugin in action in our Sylius Demo application.

- Frontend: [sylius-demo.odiseo.com.ar](https://sylius-demo.odiseo.com.ar). 
- Administration: [sylius-demo.odiseo.com.ar/admin](https://sylius-demo.odiseo.com.ar/admin) with `odiseo: odiseo` credentials.

## Installation

1. Run `composer require odiseoteam/sylius-ups-plugin`

2. Enable the plugin in bundles.php:

```php
<?php

return [
    // ...
    Odiseo\SyliusUpsPlugin\OdiseoSyliusUpsPlugin::class => ['all' => true],
];
```
 
3. Import the plugin configurations
 
```yml
imports:
    - { resource: "@OdiseoSyliusUpsPlugin/Resources/config/config.yaml" }
```

## Test the plugin

You can follow the instructions to test this plugins in the proper documentation page: [Test the plugin](doc/tests.md).
    
## Credits

This plugin is maintained by <a href="https://odiseo.com.ar">Odiseo</a>. Want us to help you with this plugin or any Sylius project? Contact us on <a href="mailto:team@odiseo.com.ar">team@odiseo.com.ar</a>.
