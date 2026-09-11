..  include:: /Includes.rst.txt

..  _installation:

============
Installation
============

Install :t3ext:`mysql_widget` using Composer. For legacy environments, use the
Extension Manager.

..  _installation-composer:

Installation with Composer
==========================

In Composer-based TYPO3 installations, install the extension by requiring the
package:

..  code-block:: bash

    composer req stefanfroemken/mysql-widget

If you work with DDEV, run:

..  code-block:: bash

    ddev composer req stefanfroemken/mysql-widget

..  _installation-extension-manager:

Installation in legacy environments
===================================

Composer is the recommended way to manage extensions. In legacy non-Composer
installations, install the extension using the Extension Manager:

..  rst-class:: bignums

1.  Log in

    Log in to the TYPO3 backend as an administrator.

2.  Open the Extension Manager

    Navigate to :guilabel:`Admin Tools > Extensions`.

3.  Update the extension list

    Select :guilabel:`Get Extensions` from the top dropdown menu. Click
    :guilabel:`Update now`.

4.  Install the extension

    Search for :t3ext:`mysql_widget`. Click the cloud icon to download and
    activate the extension.
