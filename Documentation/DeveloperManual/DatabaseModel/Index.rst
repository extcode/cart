.. include:: ../../Includes.txt

Datenbankmodell
===============

Die Datenbankmodelle sind entgegen der üblichen Extbase-Struktur noch einmal unterteilt, um eine bessere Übersicht
über die Modelle zu bekommen. Diese sind eingeteilt in Modelles des Warenkorbs (Cart), der Bestellungen (Order) und
die Produkte (Product). Durch die Verwendung von Namespaces können so auch in den Bestellungen (Order) und
Produkten (Product) Klassennamen wiederverwendet werden.

Warenkorb (Cart)
----------------

Die Modelle im Verzeichnis Cart werden für die Repräsentation der Daten im Warenkorb verwandt. Diese sind einfache
Klassen und werden nicht von \TYPO3\CMS\Extbase\DomainObject\AbstractEntity abgeleitet. Die Modelle im Verzeichnis
Cart werden auch nicht in der Datenbank sondern lediglich in der Session gespeichert. Das Ableiten von
\TYPO3\CMS\Extbase\DomainObject\AbstractEntity würde den Platz für die Sessionverwaltung unnötig aufblasen.
Die Funktionen werden im Warenkorb nicht benötigt.

*ER Diagram*

Bestellungen (Order)
--------------------

Die Modelle im Verzeichnis Order repräsentieren die einzelnen Bestellungen. Eine Bestellung (Item) hat dabei immer
Produkte (Product) eine Bestelladdresse (Address) und ggf. eine Versandadresse (Address). Weiterhin werden die
Steuerklassen (TaxClass) und errechneten Mehrwertsteuern (Tax) gespeichert. In einer Bestellung werden weiterhin die
verwendeten Coupons (Coupons) gespeicher.

*ER Diagram*
