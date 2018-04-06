# ujamii-dsgvo
Data privacy/DSGVO compliance extension for TYPO3

Description
-------------------------

On 25th of May 2018 the new laws regarding data privacy protection will be in place. With fines up to 20 Mio. €, or 4% of
global revenue for international corporations, every organization (commercial AND non-commercial) operating in the European
Union must comply with the regulations described in the linked documents.

For TYPO3 websites this has several implications:

* private data in the database needs to be deleted (instead of setting `deleted=1`)  
* transmissions of the data to the webserver or a mail service needs to be encrypted
* imprint and data privacy information needs to be on separate website (at least in Germany)
* users must be notified about permanent cookies and have a possibility to opt out for tracking services
* ...

This extension aims to help identify possible issues as well as perform regular tasks like cleaning up the database
and delete "deleted" records as well as older data submitted in forms. 

### Links

* [German DSGVO on Wikipedia](https://de.wikipedia.org/wiki/Datenschutz-Grundverordnung)
* [European law](http://eur-lex.europa.eu/legal-content/DE/TXT/?uri=uriserv:OJ.L_.2016.119.01.0001.01.DEU&toc=OJ:L:2016:119:TOC)

Installation
-------------------------

* install via composer: `composer require ujamii/ujamii-dsgvo`
* activate the extension in the backend
* a new submodule "DSGVO" in the main module "web" will appear

Usage
-------------------------

### In the TYPO3 backend

Click on the module icon in the main backend menu (inside "web"). You will see an overview for each configured
extension and database table. Green rows mean nothing to delete, red rows mean something would be deleted if
you click the red "delete" button at the bottom of the page. The amount of rows for deletion is also shown on
the overview page. 

### As CommandController on the shell

A call like `typo3/cli_dispatch.phpsh extbase cleanup:cleandatabase 1` will output something like:

```bash
2018-04-06 17:27:01
FALSE means Extension not installed, integer is amount of deleted records.
array(5 items)
	core => array(2 items)
	   fe_users => 0 (integer)
	   be_users => 0 (integer)
	powermail => array(1 item)
	   tx_powermail_domain_model_mail => 0 (integer)
	formhandler => FALSE
	tt_address => FALSE
	comments => FALSE
```

Extend the database cleaning
-------------------------

To extend the database cleaning which could be done in the backend or via CLI/Scheduler, you just need to write
some typoscript and add it into the **pageTS** of your website.

You can add several tables, grouped by extension. Starting level is `module.tx_ujamiidsgvo_dsgvocheck.settings.db`
and the next level is the extension name (e.g. `powermail`) and then the table name.

The option `allDeleted` can be 0 (default) or 1. If set to 1, all deleted=1 records in this table will be deleted
on every run of the process. The `delete` field name is read from the TCA.

Setting the `andWhere` option will limit the rows in the database which are covered in the deletion process. So if you leave
this empty **ALL THE RECORDS IN THIS TABLE WILL BE DELETED!!!** So you better think about it beforehand ;-) 

Example for powermail extension:
```typo3_typoscript
module.tx_ujamiidsgvo_dsgvocheck {
    settings {
        db {
            powermail {
                tx_powermail_domain_model_mail {
                    allDeleted = 1
                    andWhere = crdate < DATE_SUB(now(), INTERVAL 6 MONTH)
                }
            }
        }
    }
}
```

To see which tables are covered by default, take a look into `ujamii_dsgvo/Configuration/TypoScript/pagets.ts`.

TODOs / Known issues
-------------------------

* linked records (FAL, categories, ...) are ignored as the deletion process does it directly in the database, 
not via extbase models.
* add checks that could be done directly in the backend
	* check if the sentry extension or other logging extensions are installed and if, check if data transmission 
	is done via secure connection
	* check whether all the logging processes omit or anonymize the IP address
	* check whether secure mail sending config is used
* add a crawler like guzzle to really check the frontend of the website for several things:
	* SSL secured connection if forms are used
	* are permanent cookies used for tracking services like GA, etracker, piwik a.s.o.


Icon credits
-------------------------

Icon downloaded from https://www.flaticon.com/free-icon/database-protection_1825

Icons made by [Freepik](http://www.freepik.com) from [www.flaticon.com](https://www.flaticon.com/) is licensed by 
[CC 3.0 BY](http://creativecommons.org/licenses/by/3.0/)