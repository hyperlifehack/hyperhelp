<?php
/*
  
=== toplist / highscore ===

make that list actually working... 

fix some security problems with capture.php (can be accessed without login)

=== generator.php ===

o create a new project url /yourproject
which holds only the records for your project

=== needhelp.php ===

o basically a dashboard for "job" descriptions

a map http://leafletjs.com/reference.html were you can see who around you would need some help

=== offerhelp.php ===

o basically a dashboard people and the skills they can offer
e.g. "i can work with wood and turn it into spoons" <- cool. next time someone searches for "spoon" he should find this entry.

a map http://leafletjs.com/reference.html were people offer what services around you

=== unit ===
 
o you should be able to define your own "units" besides minutes like 1x "TortCoins" = 1x peace of vegan cake = 1x Minute of your precious life = 1... :-D

=== 5-star-rating system ===

oo whenever there is an action performed, the receiving user get's an email "action this and that was performed by ... please rate and comment on that action" 

=== sync with other installations ===
oo there should be no central server, instead, every server offers a download-link for the open-source-server-software.
oo when an interested user downloads the server-software,
the syncservers.php file is updated to contain the full list of up-to-date known servers that are running the software,
so the new server can sync it's data with them.

== scan for servers ==

on the admin-panel of every server, there should be an "scan for sync-servers" functionality, that:

1. either uses php to scan for servers running that software
2. fires up some c-writtern linux-bash-tool to do the job even quicker

=== pre-installed servers and hardware === 

*/
?>