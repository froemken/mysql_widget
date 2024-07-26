# TYPO3 Extension `mysql_widget`

[![Latest Stable Version](https://poser.pugx.org/stefanfroemken/mysql-widget/v/stable.svg)](https://packagist.org/packages/stefanfroemken/mysql-widget)
[![TYPO3 12.4](https://img.shields.io/badge/TYPO3-12.4-green.svg)](https://get.typo3.org/version/12)
[![TYPO3 13.2](https://img.shields.io/badge/TYPO3-13.2-green.svg)](https://get.typo3.org/version/13)
[![License](https://poser.pugx.org/stefanfroemken/mysql-widget/license)](https://packagist.org/packages/stefanfroemken/mysql-widget)
[![Total Downloads](https://poser.pugx.org/stefanfroemken/mysql-widget/downloads.svg)](https://packagist.org/packages/stefanfroemken/mysql-widget)
[![Monthly Downloads](https://poser.pugx.org/stefanfroemken/mysql-widget/d/monthly)](https://packagist.org/packages/stefanfroemken/mysql-widget)
![Build Status](https://github.com/froemken/mysql_widget/actions/workflows/tests.yml/badge.svg)

MySQL Widget is an extension for TYPO3 CMS. It shows you various status of
the MySQL server of your TYPO3 instance.

## 1 Features

* DashBoard widget to show usage of InnoDB Buffer
* DashBoard widget to show general MySQL status

## 2 Usage

### 2.1 Installation

#### Installation using Composer

The recommended way to install the extension is using Composer.

Run the following command within your Composer based TYPO3 project:

```
composer require stefanfroemken/mysql-widget
```

#### Installation as extension from TYPO3 Extension Repository (TER)

Download and install `mysql_widget` with the extension manager module.

### 2.2 Minimal setup

1) Visit DashBoard module
2) Click the + icon in the lower right
3) Choose tab `system information`
4) Choose one of the MySQL widgets
