.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

Signal Slots
============

================================== ================================== ==================================
Signal Class Name                  Signal Name                        Description
================================== ================================== ==================================
Extcode\Cart\Utility\OrderUtility  handlePaymentAfterOrder            Anbindung von Payment Providern
Extcode\Cart\Utility\OrderUtility  addProductBeforeSetAdditionalData  TODO
Extcode\Cart\Utility\OrderUtility  addVariantBeforeSetAdditionalData  TODO
================================== ================================== ==================================

handlePaymentAfterOrder
-----------------------

Dieser Signal Slot dient der Anbindung von Payment Providers wie PayPal, Amazon Chackout oder anderen.
An dieser Stelle kann die normale Abarbeitung der Bestellung (Versand der E-Mails, Weiterleitung auf die Dankeseite)
unterbrochen und zur Seite des Anbieters für die Zahlungsabwicklung weitergeleitet werden.

*Übergabeparameter*
