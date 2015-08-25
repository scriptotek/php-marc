<?php

include 'vendor/autoload.php';

use Scriptotek\Marc\Collection;

$source = '<?xml version="1.0" encoding="UTF-8" ?>
  <marc:record format="MARC21" type="Bibliographic"  xmlns:marc="info:lc/xmlns/marcxchange-v1">
    <marc:leader>99999cam a2299999 u 4500</marc:leader>
    <marc:controlfield tag="001">98218834x</marc:controlfield>
    <marc:controlfield tag="003">NO-TrBIB</marc:controlfield>
    <marc:controlfield tag="005">20150710210939.0</marc:controlfield>
    <marc:controlfield tag="007">ta</marc:controlfield>
    <marc:controlfield tag="008">150710s1998    xx#|||||||||||000|u|nob| </marc:controlfield>
    <marc:datafield tag="015" ind1=" " ind2=" ">
      <marc:subfield code="a">9906972</marc:subfield>
      <marc:subfield code="2">nbf</marc:subfield>
    </marc:datafield>
    <marc:datafield tag="020" ind1=" " ind2=" ">
      <marc:subfield code="a">8200424421</marc:subfield>
      <marc:subfield code="q">h.</marc:subfield>
      <marc:subfield code="c">Nkr 98.00</marc:subfield>
    </marc:datafield>
    <marc:datafield tag="035" ind1=" " ind2=" ">
      <marc:subfield code="a">(NO-TrBIB)98218834x</marc:subfield>
    </marc:datafield>
    <marc:datafield tag="650" ind1=" " ind2=" ">
      <marc:subfield code="a">Sang og sånt</marc:subfield>
      <marc:subfield code="2">noubomn</marc:subfield>
    </marc:datafield>
  </marc:record>';

$collection = Collection::from($source);

// foreach ($collection->records as $record) {
// 	echo $record->getField('020')->getSubfield('a') . "\n";
// }
// echo "---\n";
// foreach ($collection->records as $record) {
//   echo $record->getField('020')->getSubfield('a') . "\n";
// }
//echo "---\n";
foreach ($collection->records as $record) {
    foreach ($record->subjects as $subject) {
        echo "- $subject->vocabulary : $subject \n";
    }
}

// $record = Record::from($source);
// echo $record->isbn();
