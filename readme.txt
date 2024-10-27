=== AdSense Integrator ===
Contributors: mywpplugin
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8337773
Tags: adsense, ad, google, ads, posts, Post, advertising, advertisements, monetization, ypn, cj, adpinion, adbrite, manage, referral, link, shoppingads, ad manager, adsense insertion, ad insertion, theme, layout, admin, page
Requires at least: 2.0.0
Tested up to: 3.2
Stable tag: trunk

Adsense in few clicks with this easy, fast, powerful Ads System for Adsense and not only Adsense: user-friendly, many options, languages, wp 3.x


== Description ==

This plugin was developed to **insert** and **manage** your **AdSense** and non Adsense ads, based on the last Google rules and AdSense updates.

Go to the official page of the plugin **[AdSense Integrator](http://www.mywordpressplugin.com/adsense-integrator/)**
to get the last updates and news about it. Sponsored by **Advertalis [seo services company](http://www.advertalis.com/)**, **[Shop Theory](http://www.shoptheory.it/)**  and **[makeityourring diamond engagement rings](http://www.makeityourringdiamondengagementrings.bz/)** 

It has been originally created to automatically insert and display Google Adsense code, but it can be used as well for other types of campaigns, like AdBrite, AffiliateBOT, SHAREASALE, LinkShare, ClickBank, Oxado, Adpinion, AdGridWork, Adroll, Commission Junction, CrispAds, ShoppingAds, Yahoo!PN and others, included custom text or banner advertising campaigns.  

**Features:** 

* Easy "Copy & Paste" your ads code for embedding AdSense in your WordPress posts 
* Set the number and types of ads for page  
* Select the section of your blog where to insert each ad (homepage, posts, pages, categories, archives and tags)  
* All settings configured through WordPress Options interface (no knowledge of plugins or PHP required)  
* Easily test different ad formats and positions modifying your existing ads
* Exclude ads in selected posts or pages simply editing the post and using the new option you'll find there
* **Ip filtering** / banning system with alternative text or ad
* **CSS Margins** of the ads configurable directly in the settings
* **Manual Ad Insertion** into your post with tag system to call your ad by his name
**Use:** insert <!--ADS_INT adsname MARGIN --> or simply  <!--ADS_INT adsname --> in the HTML view of the post
**note:** adsname must NOT have spaces, you can use underscore (_) instead
* **Theme Ads Insertion** insert your ads into theme files with a php function to call your ad by his name
**Use:**  <?php get_ads('ADSNAME'); ?> in the .php theme file you need, like sidebar.php)
**note:** ADSNAME must NOT have spaces, you can use underscore (_) instead


**Last important changes:** 

**New (v 1.8.0):**
* **New feature** exclude ads displaying into single categories 

**New (v 1.7.3):**
* **Important Bugfix** now the option "disable ads in this post/page" works at 100%! 

**New (v 1.7.0):**  
* **Major Update** now fully wp 3.x compatible, all bugs fixed (see the changelog)
the "repetitions" option affects all the ads, except if inserted in posts/pages with the tag (ex. <!--ADS_INT Test1 -->) on in theme with php (ex. <?php get_ads('Test1'); ?>)

**New (v 1.6.1):**  
* **Dutch language added** thanks to Rene 

**New (v 1.6.0):**  
* **Repetitions number increased** in order to support not only Adsense, but as well other networks or custom ads/messages

**New (v 1.5.0):**  
* **Internationalization** multilanguage system to translate the plugin in many languages, added German and Italian

**New (v 1.4.3):**  
* **Disable All Ads** checkbox added to globally enable/disable ads

**New (v 1.4.1):**  
* **Disable Ads in single static pages** checkbox added in create or modify page



**Usage:**  

1. Enter you Google Adsense account (or create one if you haven't) 
2. Create an announcement and copy the code 
3. Enter the plugin settings (this page) 
4. Create a new ad with the desired options


You can create as many ads (Adsense or not) as you want, but remember that Google allows you to insert max 3 ads for each types (announcements and link groups). The plugin controls and stops ads if you exceed this number, but please pay attention if you inserted other ads in your themplate's code or in the widgets.
In the next release we will add the widget management to insert Google AdSense in every part of your blog, stay tuned!

Once created you can easily and quickly manage your existing ads modifying the options.
To pause your ad without deleting it, please set "Times" to "0" and the ad will disappear.

**Reward Author feature:**
This feature supports the development of the project replacing the 4% of your your ads impressions with ours. 
If you are happy with our script and do you want to aid us in the development of this and new wordpress plugins, 
please leave this option checked!



== Screenshots ==

1. Settings page of AdSense Integrator
2. New "disable ads for single categories" feature

== Installation ==

Like most all WordPress plugins, just copy the "adsense-integrator" folder into wp-content/plugins, then go to the plugins page of your WordPress blog and activate it:

1. Upload `adsense-integrator/adsense-integrator.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


**Adsense Integrator** is developed and offered to you by: 

**Advertalis [Seo Services Company](http://www.advertalis.com/)**




== Other Notes == 


**To Do**

* Insert ads in sidebars, footer and other places
* Create zones where to place more rotating ads
* more options to manage your ads
* ...please tell us your suggestions at: My WordPress [Adsense Plugin](http://www.mywordpressplugin.com/)


**Change Log**

Below you can find the changes for versions listed. 
We decided to create this log to help you understanding each update we will do.

* 1.8.1  new translation: spanish
* 1.8.0  new feature: possibility to disable ads displaying in single categories
* 1.7.3  important bugfix: disable ads in this post/page
* 1.7.2  little bugfix for get_locale() function 
* 1.7.1  little bugfix for the first Ad creation 
* 1.7.0  bugs with WP 3.x: fixed; 
"type of ad" option: removed; 
disable ads in single post/page: fixed; 
disable ads on the post feature is automatically "on" for each new post: fixed; 
display ads in excerpt: fixed, ads update bug: fixed, 
NEW: the "repetitions" option affects all the ads, except if inserted in posts/pages with the tag (ex. <!--ADS_INT Test1 -->) on in theme with php (ex. <?php get_ads('Test1'); ?>)

* 1.6.1  Dutch translation added, thanks to Rene
* 1.6.0  Repetitions number increased to 10 and some bugfixes

* 1.5.7  Bugfix for ads saving
* 1.5.6  new option: enable all ads for administrators only (for test mode)
* 1.5.5  Bugfix for ads in posts without or with few text
* 1.5.4  new global option: disable all ads for administrators, tested with WP 2.8.6
* 1.5.3  Bugfix for saving new ads and updates, tested with WP 2.8.5 
* 1.5.2  Belarusian (Belorussian) translation by fatcow.com
* 1.5.1  internationalization bugfix, default.po and .mo added for other translations
* 1.5.0  internationalization: new multilanguage system, German and Italian added

* 1.4.3  option added: disable all ads
* 1.4.2  new graphic inteface, embed ads code ready to insert
* 1.4.1  disable ads in single static pages
* 1.4.0  theme ads insertion added, call-time pass-by-reference bug fixed

* 1.3.3  installation bug fix
* 1.3.2  bug fix adsense pub code
* 1.3.1  manual ad insertion bug fixed, custom ads margins bug fixed 
* 1.3.0  added the manual ad insertion with tag system to call the ad by the name

* 1.2.1  small visualization fixes and labes changes
* 1.2.0  implementation of CSS Margins and bugfix for new ads creation in wp 2.7

* 1.1.0  implementation of IP filtering/banning system with alternative text or ad

* 1.0.5  visualization fixed for WP 2.7, added option to exclude ads in posts/pages
* 1.0.4  tested with WP 2.7, some visualization's bugs still to fix but working 
* 1.0.3  readme.txt updated, "reward author mode" reduced to 4% impressions
* 1.0.2  small visualization's bugfix
* 1.0.1  readme.txt and settings text updated
* 1.0.0  First public release

As a general rule the **version X.Y.Z** means: 
X = major versions  |  Y = additional features  |  Z = bugfixes or docs changes 


**Adsense Integrator** is developed and offered to you by: 

**Advertalis [Seo Services Company](http://www.advertalis.com/)**


