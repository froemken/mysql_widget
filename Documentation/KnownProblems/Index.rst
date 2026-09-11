..  include:: /Includes.rst.txt

..  _known-problems:

==============
Known problems
==============

..  _supported-database-servers:

Supported database servers
==========================

The extension :t3ext:`mysql_widget` supports only MySQL and MariaDB servers.

The data provider executes MySQL-specific runtime queries:

*   :sql:`SHOW GLOBAL STATUS LIKE 'Innodb_%'`
*   :sql:`SHOW GLOBAL STATUS LIKE 'Handler_%'`

These status variables represent InnoDB memory allocation and internal database engine
operations. Database engines such as PostgreSQL or SQLite do not provide these runtime
variables. For this reason, other database platforms cannot be supported.
