# Page Source System

## Description

Library generate html readable by seo robots and api json for React page.

## Instalation

```
composer require grzegorz-jamroz/page-source-system
```

## Usage

```php
use PageSourceSystem\Generator\PageHtmlGenerator;
use PageSourceSystem\Storage\ComponentInfoStorage;
use PageSourceSystem\Utility\Asset;
use PageSourceSystem\Domain\Page;

(new PageHtmlGenerator(
    Page::createFromArray([]),
    new ComponentInfoStorage('path/to/your/app/data'),
    new Asset('path/to/your/js/file', 'file-name', 'js'),
    new Asset('path/to/your/css/file', 'file-name', 'css'),
    'path/to/your/app/data',
    'path/to/your/render/data',
))->generate();
```
