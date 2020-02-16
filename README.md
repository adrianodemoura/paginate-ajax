# PaginateAjax

## Considerations
Plugin to repagination.

Use the force, read the code.

## Requirements

cakePHP 3

## Installation
```
$ composer require adrianodemoura/paginate-ajax
```
### In `src/Application.php`:

```
parent::bootstrap();
$this->addPlugin('PaginateAjax');
```

## Usage

### In Controller:

```
public function initialize()
{
    parent::initialize();
    $this->loadComponent('PaginateAjax.Paginator');
}
```

## Check

### In `vendor/cakephp-plugins.php`:
```
'PaginateAjax' => $baseDir . '/vendor/adrianodemoura/paginate-ajax/',
```

### In `vendor/composer/autoload_psr4.php`:
```
'PaginateAjax\\' => array($vendorDir . '/adrianodemoura/paginate-ajax/src'),
'PaginateAjax\\Test\\' => array($vendorDir . '/adrianodemoura/paginate-ajax/tests'),
```

## Test

access http://localhost/youcake3/paginate-ajax/painel

