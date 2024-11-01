=== Ultimate WP DB Manager - WordPress Database Backup, Cleanup & Optimize ===

Contributors: darell
Tags: backup, cleanup, comments, cron, database, optimization, posts, quick cleanup, schedule, tools, transient, wordpress backup
Requires at least: 4.0
Tested up to: 6.6.1
Stable tag: 1.3.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ultimate WP DB Manager make it easy to create database backup on single click, allows you to clean database, optimize database, make these jobs schedule running hourly, daily and weekly.

== Description ==

https://www.youtube.com/watch?v=r0XfXVysVNI

Ultimate WP DB Manager not only backup and optimizes your WordPress sites/blogs but also cleans up the obsolete data from database.

It is an effective tool for automatically cleaning your database so that it runs at maximum efficiency.

It's simplicity of usage along with efficient functionality makes it a perfect choice for your WordPress site to clean all obsolete data.

You can schedule the process of Backup, Cleaning, Optimizing the database tables automatically without going to phpMyAdmin.

<h3>Presentation</h3>

<p><a href="https://1.envato.market/oQDB9">Premium</a> | <a href="http://database.wphobby.com/document/backup/">Documentation</a> | <a href="https://www.youtube.com/watch?v=r0XfXVysVNI&list=PLYbs7RFTMsuI2UYAvzr1cAVFNqjloQNc_&index=1" >Videos Guides</a></p>

<p><strong>Features:</strong></p>

<ul>
<li>Database backup with the selected tables</li>
<li>Database backup files list pagination</li>
<li>Sort backup lists (by Date/ Database Size)</li>
<li>Download the database backup file directly from your WordPress dashboard</li>
<li>Simple and Easy plugin configuration.</li>
</ul>

<blockquote><p>
  <strong>Ultimate WP DB Manager Pro Version</strong></p>
<p>Like Ultimate WP DB Manager Free Version? Here's you can get <a href="https://1.envato.market/oQDB9"> Ultimate WP DB Manager pro version</a> and have more features.</p>
</blockquote>

<p><strong>Pro Version Features:</strong></p>

<ul>
<li>Database tables cleanup manually</li>
<li>Schedule database tables clean up automatically</li>
<li>Create as many scheduled cleanup tasks as you need and specify what items and tables should be cleaned by the scheduled task</li>
<li>Scheduled tasks can be executed based on customized frequencies: Once, hourly, twice a day, daily, weekly or monthly</li>
<li>Delete the old revisions of posts and pages</li>
<li>Repair corrupted database tables or damaged ones</li>
<li>Display the active scheduled tasks list (scheduled tasks) with their information like name, status (published or draft), types, next run etc.</li>
<li>Clean and delete the scheduled tasks</li>
<li>Delete old auto drafts</li>
<li>Delete trash posts</li>
<li>Delete pending comments</li>
<li>Delete spam comments</li>
<li>Delete trash comments</li>
<li>Delete pingbacks</li>
<li>Delete trackbacks</li>
<li>Delete orphan post metadata</li>
<li>Delete orphan comment metadata</li>
<li>Delete orphan user metadata</li>
<li>Delete orphan term metadata</li>
<li>Delete orphan relationships</li>
<li>Delete expired transients</li>
<li>Database tables optimize</li>
<li>Email Support from professional WordPress developer for your custom requirement</li>
</ul>

**Remember: Please make sure to always back up your database before any cleanup and optimize!**

Get the [Ultimate WP DB Manager Pro](https://1.envato.market/oQDB9) Version with more features.

**Made by [WPHobby](https://wphobby.com) &middot; We love WordPress**

== Installation ==

1. Unzip the downloaded zip file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `Ultimate WP DB Manager` from Plugins page.

== Screenshots ==

== Frequently Asked Questions ==

= What is the requirements to use Ultimate WP DB Manager? =
Minimum Requirements
WordPress version 4.0 or greater.
PHP version 5.4 or greater.

= Recommended Requirements =
Latest version of WordPress.
PHP 5.4 or greater.

= What is Optimizing Database Tables ? =
Optimizing Database Tables helps reorganizing the physical storage of table data and associated index data, to reduce storage space and improve Input/output efficiency when accessing the table.

= It is safe to clean wordpress database? =
Yes, it is. We do not run any code that can break down your site or delete your posts, pages, comments, etc.
However, Please make sure to always back up your database before any cleanup.

= What does mean “clean my database”? =
As you use WordPress, your database generate a lot of extra data such as revisions, spam comments, trashed comments, etc.
Removing this unnecessary data will reduce your database size, speeds up your backup process and speeds up your site.

= What does mean “Revision”? What sql code is used to clean it? =
WordPress stores a record (called “revision”) of each saved draft or published update.
WordPress allows you to see what changes were made in each post and page over time.
However, this can generate a lot of unnecessary overhead in your WordPress database, which consumes a lot of space.
The sql query used by the plugin to clean all revisions is:
DELETE FROM posts WHERE post_type = ‘revision’

= What does mean “Auto-draft”? What sql code is used to clean it? =
WordPress automatically saves your post/page while you are editing it.
This is called an auto-draft. If you don’t hit the publish/update button, then the post/page will be saved as auto-draft and any modification to your post/page will not be visible in your public site. Over time, you could have multiple auto-drafts that you will never publish and hence you can clean them. The sql query used by the plugin to clean all auto-drafts is:
DELETE FROM posts WHERE post_status = ‘auto-draft’

= What does mean “Pending comment”? What sql code is used to clean it? =
Pending comments are comments published by users and which are waiting for your approval before appearing in your site. In some cases, you will have to clean all these comments. The sql query used by the plugin to clean all pending comments is:
DELETE FROM comments WHERE comment_approved = ‘0’

= What does mean “Spam comment”? What sql code is used to clean it? =
It is a comment that you (or a plugin) have marked as spam. The sql query used by the plugin to clean all spam comments is:
DELETE FROM comments WHERE comment_approved = ‘spam’

= What does mean “Trash comment”? What sql code is used to clean it? =
A trash comment is a comment that you have deleted from your WordPress and have been moved to the trash. A trash comment is not visible in your site and should be deleted forever. The sql query used by the plugin to clean all trash comments is:
DELETE FROM comments WHERE comment_approved = ‘trash’

= What does mean “trackback”? What sql code is used to clean it? =
Trackbacks allows you to notify other websites owners that you have linked to their article on your website. These trackbacks can be used to send huge amounts of spam. Spammers use them to get their links posted on as many sites as possible. That is why they should be deactivated/cleaned if you do not use them. The sql query used by the plugin to clean trackbacks is:
DELETE FROM comments WHERE comment_type = ‘trackback’

= What does mean “pingback”? What sql code is used to clean it? =
Pingbacks allow you to notify other websites owners that you have linked to their article on your website. Pingbacks were designed to solve some of the problems that people saw with trackbacks. Although there are some minor technical differences, a trackback is basically the same things as a pingback. These pingbacks can be used to send huge amounts of spam. Spammers use them to get their links posted on as many sites as possible. That is why they should be deactivated/cleaned if you do not use them. The sql query used by the plugin to clean pingbacks is:
DELETE FROM comments WHERE comment_type = ‘pingback’

= What does mean “Orphan post meta”? What sql code is used to clean it? =
The post meta data is the information you provide to viewers about each post. This information usually includes the author of the post, when it was written (or posted), and how the author categorized that particular post. In some cases, some post meta data information becomes orphan and does not belong to any post. They are then called “orphan postmeta” and should be cleaned since they are not useful. The sql query used by the plugin to clean all orphan postmeta is:
DELETE pm FROM postmeta pm LEFT JOIN posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL

= What does mean “Orphan comment meta”? What sql code is used to clean it? =
The same as “Orphan post meta” with the exception that “orphan comment meta” concern comments and not posts. The sql query used by the plugin to clean all orphan comment meta is:
DELETE FROM commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM comments)

= What does mean “Orphan user meta”? What sql code is used to clean it? =
The user meta data is the information you provide to viewers about each user. This information usually includes additional data that is not stored in the users table of WordPress. In some cases, some user meta data information becomes orphaned and does not belong to any user. They are then called “orphaned usermeta” and should be cleaned since they are not useful. The sql query used by the plugin to clean all orphan comment meta is:
DELETE FROM usermeta WHERE user_id NOT IN (SELECT ID FROM users)

= What does mean “Orphan term meta”? What sql code is used to clean it? =
The term meta data is the information that is provided for each taxonomy term. This information usually includes additional data that is not stored in the terms table of WordPress. In some cases, some term meta data information becomes orphaned and does not belong to any taxonomy term. They are then called “orphaned termmeta” and should be cleaned since they are not useful. The sql query used by the plugin to clean all orphan comment meta is:
DELETE FROM termmeta WHERE term_id NOT IN (SELECT term_id FROM terms)

= What does mean “Orphan relationships”? What sql code is used to clean it? =
Sometimes the wp_term_relationships table becomes bloated with many orphaned relationships. This happens particularly often if you’re using your site not as a blog but as some other type of content site where posts are deleted periodically. Over time, you could get thousands of term relationships for posts that no longer exist which consumes a lot of database space. The sql query used by the plugin to clean all orphan relationships is:
DELETE FROM term_relationships WHERE term_taxonomy_id=1 AND object_id NOT IN (SELECT id FROM posts)

= What does mean “expired transient”? =
Transients are a way of storing cached data in the WordPress DB temporarily by giving it a name and a time frame after which it will expire and be deleted. This helps improve WordPress performance and speed up your website while reducing the overall server load. Expired transients are transients that are expired and still exist in the database. These ones can be safely cleaned. Transients housekeeping is now part of WordPress core, as of version 4.9, so no need to clean up them manually unless you have specific needs.


== Upgrade notice ==

== Changelog ==
= Version 1.0.7 2020-11-24 =
* Update - Change premium version link

= Version 1.0.6 2020-11-19 =
* Update - Add Pro verion features list

= Version 1.0.5 2020-11-10 =
* Update - Select optimize database tables

= Version 1.0.4 2020-11-05 =
* Update - Plugin premium version link

= Version 1.0.3 2020-11-02 =
* Update - Add dedicated tech support

= Version 1.0.2 2020-10-30 =
* Update - Add premium link on admin notice

= Version 1.0.1 2020-10-29 =
* Update - Redirect to backup files list page after backup complete

= Version 1.0.0 2020-10-21 =
Initial release
