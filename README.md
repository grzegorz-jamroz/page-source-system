# Html Creator

## Description

Library allow to generate html for React pages readable by seo robots.

## Instalation

```
composer require grzegorz-jamroz/html-creator
```

## Usage

```php
use HtmlCreator\ContentBuilder;
use HtmlCreator\Helmet;
use HtmlCreator\PageBuilder;use HtmlCreator\PageFactory;

$data = [
    'seo' => [],
    'header' => 'Page title',
    'navbar' => [
        'items' => [
            [
                'name' => 'Home',
                'url' => '/home',
            ],
            [
                'name' => 'About',
                'url' => '/about',
            ],
            [
                'name' => 'Contact',
                'url' => '/contact',
            ],
        ],
    ],
    'sections' => [],
    'footer' => [
        'text' => 'Copyright ©'
    ],
];

$pageBuilder = new PageBuilder(
    'en',
    '/path/to/your/app.js',
    '/path/to/your/styles.css',
    Helmet::createFromArray($data['seo'] ??= []),
    ContentBuilder::createFromArray($data),
);
$html = (new PageFactory($pageBuilder))->getHtml();
```
