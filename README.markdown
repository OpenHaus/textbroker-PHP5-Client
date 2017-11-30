# textbroker-php-client
PHP5 Wrapper for the Textbroker API with composer support and namespaces. Original repo [here](https://github.com/OpenHaus/textbroker-PHP5-Client)

## Installation
Copy the files (the directory textbroker-PHP5-Client) somewhere in your executable path or include the path at runtime.

## Requirements
You need at least PHP 5.2. A version of PHP>=5.6 is recommended.

## Documentation
You can generate a phpdoc from the code. See the [code examples](http://www.open-haus.de/community/textbroker-php5-client/ "Usage examples on open haus"). Also have a look in the [official textbroker API documentation](http://www.textbroker.com/us/client-api.php "Look for the PDF").

## Changelog

### 1.1
+ Added support for composer and namespaces

### 1.0
+ Added TextbrokerBudgetOrder::getCopyscapeResults function
+ Added TextbrokerBudgetOrder::getTeams function
+ Added TextbrokerBudgetOrder::getCostsTeamOrder function
+ Added TextbrokerBudgetOrder::createTeamOrder function
+ Added TextbrokerBudgetOrder::getOrdersByStatus function
+ Added TextbrokerBudgetOrderChange::setSEO function
+ Added TextbrokerBudgetProofreading class
+ Added TextbrokerBudgetProofreading::create function
+ Added TextbrokerBudgetProofreading::getCosts function
+ Added TextbrokerBudgetProofreading::preview function
+ Added TextbrokerBudgetProofreading::accept function
+ Added TextbrokerBudgetProofreading::getStatus function
+ Added TextbrokerBudgetProofreading::delete function
+ Added TextbrokerBudgetProofreading::pickUp function
+ Added TextbrokerBudgetProofreading::revise function
+ Added TextbrokerBudgetProofreading::reject function

### 0.2
+ Made it possible to decide which server (.de/.com/.fr/etc.) to use
+ Bugfixes
+ Better exception handling

### 0.1
+ Initial release

## License

	+---------------------------------------------------------------------------+
	| Copyright (c) 2012, Fabio Bacigalupo, open haus                           |
	| All rights reserved.                                                      |
	|                                                                           |
	| Redistribution and use in source and binary forms, with or without        |
	| modification, are permitted provided that the following conditions        |
	| are met:                                                                  |
	|                                                                           |
	| o Redistributions of source code must retain the above copyright          |
	|   notice, this list of conditions and the following disclaimer.           |
	| o Redistributions in binary form must reproduce the above copyright       |
	|   notice, this list of conditions and the following disclaimer in the     |
	|   documentation and/or other materials provided with the distribution.    |
	| o Neither the name of Seagull Systems nor the names of its contributors   |
	|   may be used to endorse or promote products derived from this software   |
	|   without specific prior written permission.                              |
	|                                                                           |
	| THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS       |
	| "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT         |
	| LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR     |
	| A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
	| OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,     |
	| SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT          |
	| LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,     |
	| DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
	| THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
	| (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE     |
	| OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.      |
	+---------------------------------------------------------------------------+
