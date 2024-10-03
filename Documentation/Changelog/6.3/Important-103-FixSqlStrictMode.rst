.. include:: ../../Includes.rst.txt

==================================================
Important: #103 - Fix exception in sql strict mode
==================================================

See `Issue 103 <https://github.com/extcode/cart/issues/103>`__

Description
===========

The strict mode of MySQL and MariaDB raises some errors. Removing
the NOT NULL from text fields and adding an passthrough for the
inline relation to tax allows to using the extension in strict mode too.

.. IMPORTANT::
   Some changes to the sql configuration file and TCA require a database update.

.. index:: Database, TCA
