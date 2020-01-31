.. include:: ../../../Includes.txt

Formularfelder vorausfüllen
===========================

Vor allem wenn man mit Frontend Nutzern arbeitet, möchte man gern die Daten in die Felder für die Rechnungs- und Liefer
addresse eintragen.
Ein Vorausfüllen der Adressfelder mit Daten aus einem eingeloggten Frontend Benutzer wird es nicht geben.
Zum einen ist das nicht in jedem Fall gewünscht, zum anderen müsste das FrontendUser Model erweitert werden,
um alle relevanten Daten eines Nutzers speichern zu können. Oft werden diese Felder schon an anderer Stelle
bereitgestellt.

Frontend User
=============

Eine Möglichkeit die Daten für die Rechnungsadresse vorauszufüllen wäre ein (angepasster) Frontend User Datensatz.
Wie hier ggf. die Felder für die Versandadresse gespeichert werden, ist oft sehr individuell geregelt.

Ein Beispiel, dass sich leicht in die eigene SitePackage Erweiterung integrieren lässt, findet sich
in einem `GitHubGist <https://gist.github.com/extcode/4add957d9f43c223b8f80df3b6671535>`_ .

Contacts Datensätze
===================

Eine andere, und bevorzugte Variante, ist nicht die Verwendung des FrontendUser Models, sondern eine weitere
Erweiterung (contacts). Contacts bietet eine gute Verwaltung von Personen bezogenen Adressdaten. Diese Erweiterung
bietet nicht nur die Möglichkeit für einen eingeloggten Nutzer mehrere Adresse zu speichern, sondern diese auch
so mit einem Typ zu versehen, dass sich die Rechnungsadresse(n) von den Lieferadressen unterscheiden lässt.