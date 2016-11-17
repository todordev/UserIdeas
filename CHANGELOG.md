UserIdeas Changelog
==========================

###v2.4
* [New] Added content events onContentBeforeDisplay and onContentAfterDisplay on view Items.
* [New] Added functionality for uploading files and attaching them to an item or comment.
* [New] Added functionality to select filesystem where you would like to upload the files - local filesystem or Amazon S3.
* [New] Added option to convert votes counter to a button. Users will be able to vote for an item clicking on the counter. You will find the option in plugin System - UserIdeas Votes and module UserIdeas Items.
* [New] Added subcategories on view Category.
* [New] Added view Categories.
* [Improvement] Improved all modules and plugins.
* [Improvement] Improved the performance. It was removed duplicated calls to database and some data was cached.
* [Fixed] Error occurs if there are no comments.
* [Fixed] User has permissions to edit an item but he cannot do it, if it is an item created by another user.
* [Fixed] Item tags are removed when an item be saved on front-end.
* [Fixed] Issues with records by anonymous users and given permissions.

###v2.3.4
* Improved items sorting.

###v2.3.3
* It was fixed an issue with preparing items parameters.

###v2.3.2
* It was fixed an issue with author's name when User Ideas is not integrated with third-party community extension.

###v2.3.1
* Added option to enter alternate page where the user will be redirected to log into the website.
* Fixed item deleting. Delete comments and votes when delete an item.
* Fixed the item form on the administration.
* It was made the helpers compatible with Prism Library 1.12.
* Fix and issue with PrepareTagsHelper.

###v2.3
* It was fixed compatibility issue with Prism Library 1.10.
* Added options in component global configuration.
* Added access options.
* It was implemented ACL functionality.

###v2.2.1
* Fixed bug with initialization of vote button in the module UserIdeas Items.

###v2.2
* Added tags to the categories and the items.
* Added options to the items on the form where create an item.
* Added class Userideas\Item\Items to the library.

###v2.1
* Improved code quality.
* Fixed bug with the library folder name (renamed from /libraries/userideas to /libraries/UserIdeas).

###v2.0
* Replaced ITPrism Library with Prism Library.
* It was done to use Bootstrap 3.
* Integrated with Easy Profile.
* Integrated with Community Builder.
* Added order by votes.

###v1.6.2
* Fixed issue [#22](https://github.com/ITPrism/UserIdeas/issues/22).
* Integrated with Easy Social.

###v1.6.1
* Fixed category manager permissions.

###v1.6
* Added option to select redirection when a user post an item.
* Added option to select what you would like to display as a name of item create - name or username.
* It was moved the items option to menu options of items list and category.
* Added option for description length on category and items list views.
* Added permissions. Now, you are able to manage actions of user groups.
* It is possible anonymous users to post items and to write comments.
* Added captcha to forms.

###v1.5
* Fixed some issues.
* Improved code quality.
* Improved UserIdeas Library ( the API ).
* Added option to enable and disable Chosen for drop down elements.

###v1.4
* Added option to the plugin "Content - User Ideas - Admin Mail", the administrator to receive notification e-mail when someone post a new comment.
* Added option for CSS style class to statuses. Now, they can be styled.
* Added statistical information on dashboard.

###v1.3

* Added content events "onAfterDisplay", "onBeforeDisplay", "onContentPrepare"
* Improved routers.
* Added a new view that lists all items.
* Added statuses.
* Ported to Joomla! 3
* Changed the type of the field description with editor.
* Fixed some issues.

###v1.2

* Added plugin Content - User Ideas - Admin Mail

###v1.1

* Fixed some issues