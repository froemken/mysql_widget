..  include:: /Includes.rst.txt

..  _introduction:

============
Introduction
============

..  _what-it-does:

What does it do?
================

The :t3ext:`mysql_widget` extension provides two monitoring widgets for the TYPO3 backend
:t3ext:`dashboard`. Both widgets analyze MySQL and MariaDB runtime metrics to monitor database
performance directly inside TYPO3.

..  _innodb-buffer-pool-widget:

InnoDB buffer pool widget
-------------------------

The :guilabel:`MySQL InnoDB Buffer Pool` widget renders a doughnut chart dividing buffer pool
pages into three categories:

*   Used data pages
*   Miscellaneous pages
*   Free pages

The footer displays total allocated memory (used plus miscellaneous pages) alongside total
configured buffer pool memory in human-readable bytes.

..  _innodb-status-widget:

InnoDB status widget
--------------------

The :guilabel:`MySQL InnoDB Status` widget evaluates three critical database health metrics:

*   Wait-free counter: Evaluates whether InnoDB had to wait for clean pages. A counter greater
    than zero indicates that the buffer pool is too small.
*   Buffer pool read-hit ratio: Measures the percentage of read requests served directly from
    RAM. A ratio below 99.9% flags a potential memory bottleneck.
*   Handler read ratio: Calculates the proportion of index lookups against full table scans.
    A ratio exceeding 95% indicates frequent full table scans.

..  _screenshots:

Screenshots
===========

..  figure:: /Images/MySQLInnoDBBufferPoolWidget.png
    :alt: MySQL InnoDB Buffer Pool widget
    :class: with-shadow
    :width: 500px

..  figure:: /Images/MySQLInnoDBStatusWidget.png
    :alt: MySQL InnoDB Status widget
    :class: with-shadow
    :width: 500px
