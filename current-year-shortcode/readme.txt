=== Current Year, Symbols and IP Shortcode ===
Contributors: kgmservizi
Donate link: https://kgmservizi.com
Tags: current year shortcode, copyright shortcode, copyright symbol, trademark shortcode, symbol shortcode
Requires at least: 2.5
Tested up to: 6.8
Requires PHP: 5.6
Stable tag: 2.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Useful shortcode for WordPress.
Current year, copyright, symbols and user IP with shortcode.

== Description ==

**Don't work? Open ticket, we answer in max 48h.**

**How shortcode work:** [WordPress Codex](https://codex.wordpress.org/Shortcode "WordPress Codex")

**Full documentation and shortcode list:** [Documentation](http://uskgm.it/cysdoc "Documentation")

Current year, copyright, symbols and user IP with shortcode.

[y] display current year **2026**
[m] display current month **April**
[d] display current day **12**
[dmy] display current date **12/04/2026**
[c] display copyright symbol **©**
[t] display registered trademark symbol **®**
[tm] display unregistered trademark symbol **™**
[sm] display service mark symbol **℠**
[cc] display **Copyright**
[cy] display symbol of copyright and current year **© 2026**
[cyy year="2003"] display symbol of copyright, first year, current year **© 2003-2026**
[cyyl year="2003"] display copyright, first year, current year **Copyright 2003-2026**
[cyyls year="2003"] display copyright (+©), first year, current year **© Copyright 2003-2026**
[show_user_ip] display ip of current user

**Useful attribute for extend shortcode functionality**

**Format Attribute for Year**
For shortcode with year you can choose format with attribute format="". It support "Y" (es.2026) or "y" (es.26). Default format without attribute is "Y".
*You must enter year attribute in the same format.*

**Example**
[cyy year="03" format="y"] **© 03-26**
[cyy year="2003" format="Y"] **© 2003-2026**

**Format Attribute for Month**
For shortcode with month you can choose format with attribute format="". It support "F" - "m" - "M" - "n".

**Format Attribute for Day**
For shortcode with day you can choose format with attribute format="". It support "D" - "d" - "j" - "N"

**Format Attribute for current date**
All php date function attributes.

**Offset Attribute**
For day shortcode [d] you can add offset with attribute offset="". It support + or -.
For month shortcode [m] you can add offset with attribute offset="". It support + or -.
For year shortcode [y] you can add offset with attribute offset="". It support + or -.
For current date shortcode [dmy] you can add offset with attribute offset="". It support + or - in standard php date format, for ex. +1 year or -1 day.

**Example**
[d offset="+1"] **13** (if current day is 12)
[m offset="+1"] **May** (if current month is April)
[y offset="+1"] **2027** (if current year is 2026)
[dmy offset="+1 year"] **12/04/2027**



== Installation ==

1. Upload the plugin files to the '/wp-content/plugins/' directory, or install the plugin through the WordPress add plugins page directly.
2. Activate the plugin through the 'Plugins' page in WordPress.
3. Add shortcode in all position where WordPress accept it.


== Frequently Asked Questions ==

= What are WordPress shortcode? =

**Read how WordPress shortcode work:** [WordPress Codex](https://codex.wordpress.org/Shortcode "WordPress Codex")

= What are the available shortcodes? =

[y] display current year **2026**
[m] display current month **April**
[d] display current day **12**
[dmy] display current date **12/04/2026**
[c] display copyright symbol **©**
[t] display registered trademark symbol **®**
[tm] display unregistered trademark symbol **™**
[sm] display service mark symbol **℠**
[cc] display **Copyright**
[cy] display symbol of copyright and current year **© 2026**
[cyy year="2003"] display symbol of copyright, first year, current year **© 2003-2026**
[cyyl year="2003"] display copyright, first year, current year **Copyright 2003-2026**
[cyyls year="2003"] display copyright (+©), first year, current year **© Copyright 2003-2026**
[show_user_ip] display ip of current user

**Full list here:** [Documentation](http://uskgm.it/cysdoc "Documentation")

= What are the symbols shortcode? =

[c] display copyright symbol **©**
[t] display registered trademark symbol **®**
[tm] display unregistered trademark symbol **™**
[sm] display service mark symbol **℠**

Symbols shortcode retrieve the most common "registration" symbols. 
For more symbols shortcode write a support ticket!

= What is the IP shortcode? =

[show_user_ip] display ip of current user

With IP shortcode you can retrieve your user IP.

= How work year format attribute? =

For shortcode with year you can choose format with attribute format="". It support "Y" (es.2026) or "y" (es.26). Default format without attribute is "Y".
*You must enter year attribute in the same format.*

**Example**
[cyy year="03" format="y"] **© 03-26**
[cyy year="2003" format="Y"] **© 2003-2026**

= How work month format attribute? =

For shortcode with month you can choose format with attribute format="". It support "F" - "m" - "M" - "n".

= How work day format attribute? =

For shortcode with day you can choose format with attribute format="". It support "D" - "d" - "j" - "N"

= How work year, month and day offset attribute? =

For day shortcode [d] you can add offset with attribute offset="". It support + or -.
For month shortcode [m] you can add offset with attribute offset="". It support + or -.
For year shortcode [y] you can add offset with attribute offset="". It support + or -.
For current date shortcode [dmy] you can add offset with attribute offset="". It support + or - in standard php date format, for ex. +1 year or -1 day.

**Example**
[d offset="+1"] **13** (if current day is 12)
[m offset="+1"] **May** (if current month is April)
[y offset="+1"] **2027** (if current year is 2026)
[dmy offset="+1 year"] **12/04/2027**


== Screenshots ==

1. Year shortcode.
2. Month shortcode.
3. Day shortcode.
4. Copyright shortcode.
5. Symbols and IP shortcode.


== Changelog ==

= 2.5 =
* Improved accessibility: symbols and copyright shortcodes now include aria-label for screen readers.
* Security: improved input sanitization and output escaping across all shortcodes.
* Fixed offset bug with negative values (e.g. [y offset="-3"]).
* Fixed loose type comparison in copyright year range shortcodes.
* Removed duplicate validation code, consolidated into shared functions.
* Optimized date function calls for better performance.
* Renamed internal filter hook from wpb_get_ip to cys_get_ip (old hook still works with deprecation notice).
* Fixed function prefix inconsistency (csy_ renamed to cys_).
* Code formatting aligned to WordPress Coding Standards.
* Error messages now include role="alert" for accessibility.

= 2.4.1 =
* Bugfix.

= 2.4 =
* Complete code revision.

= 2.3.5 =
* 6.6 compatibility.

= 2.3.4 =
* 6.5 compatibility.

= 2.3.3 =
* 6.4 compatibility.

= 2.3.2 =
* Code revisions.

= 2.3.1 =
* Bugfix and compatibility.

= 2.3 =
* Translation with date_i18n. Added current date shortcode (dmy).

= 2.2.3 =
* Months translatable with .pot.

= 2.1 =
* Bugfix.

= 2.0 =
* Bugfix.

= 1.9 =
* Compatibility WordPress 5.8.

= 1.8.3 =
* Compatibility WordPress 5.7.

= 1.8.2 =
* Bugfix.

= 1.8.1 =
* FAQ updated.

= 1.8 =
* New shortcode for month and day, new documentation.

= 1.7.2 =
* Version compatibility update.

= 1.7.1 =
* Version compatibility update.

= 1.7 =
* Bugfix and new shortcode for retrieve IP.

= 1.6 =
* New name and compatibility update.

= 1.5 =
* Bugfix and offset for [y] shortcode.

= 1.4 =
* Added ™ and ℠ symbol.

= 1.3 =
* Compatibility revision.

= 1.2 =
* Compatibility revision.

= 1.1 =
* Bugfix.

= 1 =
* Now you can format year with format="" attribute. You can choose "y" or "Y". More info in description.

= 0.4 =
* Now with [cyy] if current year is = attribute year, first year isn't displayed. New shortcode [cyyl] for show "Copyright year-year". New shortcode [cyyls] for show "©Copyright year-year".

= 0.3.1 =
* Bugfix

= 0.3 =
* Added [cc] shortcode "Copyright".

= 0.2 =
* Added more shortcode (copyright, trademark, copyright + year of creation + current year)

= 0.1 =
* Initial release


== Upgrade Notice ==

= 2.5 =
Security and accessibility improvements. Fixed negative offset bug. If you use the wpb_get_ip filter hook, please update to cys_get_ip (old hook still works for now).

= 2.4.1 =
Bugfix.

= 2.4 =
Complete code revision.

= 2.3.5 =
6.6 compatibility.

= 2.3.4 =
6.5 compatibility.

= 2.3.3 =
6.4 compatibility.

= 2.3.2 =
Code revisions.

= 2.3.1 =
Bugfix and compatibility.

= 2.3 =
Translation with date_i18n. Added current date shortcode (dmy).

= 2.2.3 =
Months translatable with .pot.

= 2.1 =
Bugfix.

= 2.0 =
Bugfix.

= 1.9 =
Compatibility WordPress 5.8.

= 1.8.3 =
Compatibility WordPress 5.7.

= 1.8.2 =
Bugfix.

= 1.8.1 =
FAQ updated.

= 1.8 =
New shortcode for month and day, new documentation.

= 1.7.2 =
Version compatibility update.

= 1.7.1 =
Version compatibility update.

= 1.7 =
Bugfix and new shortcode for retrieve IP.

= 1.6 =
New name and compatibility update.

= 1.5 =
Bugfix and offset for [y] shortcode.

= 1.4 =
Added ™ and ℠ symbol.

= 1.3 =
Compatibility revision.

= 1.2 =
Compatibility revision.

= 1.1 =
Bugfix.

= 1 =
Now you can format year with format="" attribute. You can choose "y" or "Y". More info in description.

= 0.4 =
Now with [cyy] if current year is = attribute year, first year isn't displayed. New shortcode [cyyl] for show "Copyright year-year"

= 0.3.1 =
Bugfix

= 0.3 =
Added [cc] shortcode "Copyright".

= 0.2 =
Added more shortcode (copyright, trademark, copyright + year of creation + current year)

= 0.1 =
Initial release.

`<?php code(); // goes in backticks ?>`