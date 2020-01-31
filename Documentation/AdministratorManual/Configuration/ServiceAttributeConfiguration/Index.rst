.. include:: ../../../Includes.txt

Service Attribute Konfiguration
===============================

Die Service Attribute werden nicht automatisch aus der Datenbank geladen. Da diese oft nicht genutzt werden und damit
die Berechnung nicht erfolgen muss. Um ein oder mehrere Service Attribute zu laden muss die TypoScript-Konfiguration
erweitert werden.

::

   plugin.tx_cart {
      repository {
         fields {
            getServiceAttribute1 = getServiceAttribute1
            getServiceAttribute2 = getServiceAttribute2
            getServiceAttribute3 = getServiceAttribute3
         }
      }
   }

|


Dann stehen im Cart-Model folgende Getter zur Verfügung:

- getMaxServiceAttribute1()
- getMaxServiceAttribute2()
- getMaxServiceAttribute3()
- getSumServiceAttribute1()
- getSumServiceAttribute2()
- getSumServiceAttribute3()
