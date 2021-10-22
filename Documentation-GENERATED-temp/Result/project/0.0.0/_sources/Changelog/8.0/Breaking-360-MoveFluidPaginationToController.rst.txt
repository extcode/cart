.. include:: ../../Includes.txt

====================================================
Breaking: #360 - Move Fluid Pagination to Controller
====================================================

See :issue:`360`

Description
===========

In TYPO3 v11 <f:paginate> has been removed and is implemented via the
controller.

Affected Installations
======================

All installations are affected by this change.

Migration
=========

If the templates for the lists of orders in the frontend or backend have been
overwritten, then these templates must also be adapted.

.. index:: Template, Frontend, Backend
