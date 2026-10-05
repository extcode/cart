.. include:: ../../Includes.rst.txt

========
Commands
========

order:cleanup
=============

The order:cleanup command requires the date before which all orders are to be
set to deleted (cutOffDate) as an argument. The format for this date only
accepts the format YYYY-MM-DD.

..  code-block:: bash

   # Remove all orders created before 1st January 2025
   vendor/bin/typo3 order:cleanup 2025-01-01

The DataHandler is then used to process all orders before this date.

.. IMPORTANT::
   Orders are only set to deleted, but not removed from the database. The
   database must be cleaned up afterwards using `vendor/bin/typo3 cleanup:deletedrecords`.

Restrictions:
   No special cases such as belonging to a frontend user are taken into account.
   There is no interaction with the user of the command.
   No execution via the scheduler is planned.

.. WARNING::
   No Backup?
   No Mercy!
