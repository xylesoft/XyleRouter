<?php

//if (preg_match_all(
//    '#(?P<simplePattern>{([a-zA-Z0-9]+)})|(?P<complexPattern>{(?:[\/a-z0-9A-Z\-]+)?\(([a-zA-Z0-9]+)\)(?:[\/a-z0-9A-Z\-]+)?})#',
//    '/users/{name}/statistics/{statistic}{/(sort)}{/special-offer-for-(clientsForeName)-only-today}',
//    $matches)
//) {
//    var_dump($matches);
//}
//
//
//if (preg_match_all(
//    '#(?P<simplePattern>{([a-zA-Z0-9]+)})|(?P<complexPattern>{(?:[\/a-z0-9A-Z\-]+)?\(([a-zA-Z0-9]+)\)(?:[\/a-z0-9A-Z\-]+)?})#',
//    '/users/{name}/statistics/{statistic}',
//    $matches)
//) {
//    var_dump($matches);
//}
//
//
//function moo() {
//    foreach (['a','b','c','d','e'] as $l) {
//        yield $l => md5($l);
//    }
//}
//
//foreach (moo() as $key=>$value) {
//    echo $key . ' => ' . $value . PHP_EOL;
//
//}

$a = '{/special-offer-for-(clientsForeName)-only-today}';


echo 'End!.' . PHP_EOL;