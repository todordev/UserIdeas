User Ideas for Joomla! 
==========================
( Version 2.3.3 )
- - -

User Ideas is an extension that provides functionality for creating and managing ideas pool, suggestions, issues, user feedbacks,...

##Documentation
You can find documentation on following pages.

[Documentation and FAQ](http://itprism.com/help/108-userideas-documentation-faq)

[API documentation](http://cdn.itprism.com/api/userideas/index.html)

##Download
You can [download UserIdeas package] (http://itprism.com/free-joomla-extensions/ecommerce-gamification/feedbacks-ideas-suggestions) and all payment plugins from the website of ITPrism.

[Distribution repository](https://github.com/ITPrism/UserIdeasDistribution)

##License
User Ideas is under [GPLv3 license] (http://www.gnu.org/licenses/gpl-3.0.en.html).

## About the code in this repository
This repository contains code that you should use to create a package. You will be able to install that package via [Joomla extension manager] (https://docs.joomla.org/Help25:Extensions_Extension_Manager_Install).

##How to create a package?
* You should install [ANT] (http://ant.apache.org/) on your PC.
* Download or clone [User Ideas distribution] (https://github.com/ITPrism/UserIdeasDistribution).
* Download or clone the code from this repository.
* Rename the file __build/example.txt__ to __build/antconfig.txt__.
* Edit the file __build/antconfig.txt__. Enter name and version of your package. Enter the folder where the source code is (Social Community distribution). Enter the folder where the source code of the package will be stored (the folder where you have saved this repository).
* Save the file __build/antconfig.txt__.
* Open a console and go in folder __build__.
* Type "__ant__" and click enter. The system will copy all files from distribution to the folder where you are going to build an installable package.

`ant`

##Contribute
If you would like to contribute to the project you should use [User Ideas distribution](https://github.com/ITPrism/UserIdeasDistribution). That repository provides Joomla CMS + User Ideas.
You can clone it on your PC and install it on your localhost. You should use it as development environment. You should use it to create branches, to add new features, to fix issues and to send pull request.