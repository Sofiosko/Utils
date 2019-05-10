The whole library purpose is to simplify working with Czech VAT while it allows to extend and bend computing logic of every important operation.

```php
$cl = new \BiteIT\Taxes\CalcLogic();

$pl = new \BiteIT\Taxes\PriceList(\BiteIT\Taxes\Rates::HIGH_PERCENT, $cl);

$pl->addWithVat(121, 1);
$pl->addWithVat(200, 2, \BiteIT\Taxes\Rates::MEDIUM_PERCENT);
$pl->addWithVat(299, 4, \BiteIT\Taxes\Rates::LOW_PERCENT);

var_dump([
    $pl->getTotalsWithVat(),
    $pl->getTotalsWithoutVat()
]);
```