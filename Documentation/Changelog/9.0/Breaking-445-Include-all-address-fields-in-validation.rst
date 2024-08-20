.. include:: ../../Includes.rst.txt

=========================================================
Breaking: #445 - Include all address fields in validation
=========================================================

See :issue:`445`

Description
===========

All fields which exists for the billing address and for the shipping address
are now included within the validation array in TypoScript. The fields
`title`, `company`, `taxIdentificationNumber` and `country` were not included.
Including those fields makes it easier and especially more logic to manage
the validation and visibility of those fields.

Affected Installations
======================

By default the fields `title`, `company` and `taxIdentificationNumber` will
not be rendered, the field country will be rendered and required.

Migration
=========

See :ref:`validating-and-hiding-fields` how to manage the visibility and
validation of address fields.

.. index:: Template, Frontend, TypoScript
