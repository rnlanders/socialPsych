socialPsych
===========

socialPsych is an educational social network platform with gamified practice tests.

Please note that although I am technically a professional web programmer, I am 100% self-taught.  Also, this was pretty early in my learning to code.  As a result, the code is not elegant or well-commented.  Sorry!

Note that the practice test content is licensed and not included here.

The functionality of this system is described in great detail here:
Landers, R. N. & Callan, R. C. (2011). Casual social games as serious games: The psychology of gamification in undergraduate education and employee training.  In M. Ma, A. Oikonomou, & L. C. Jain (Eds.), Serious Games and Edutainment Applications (pp. 399-424). Surrey, UK: Springer. 

Two summary videos on the project are available:
https://vimeo.com/11500513
https://vimeo.com/11506353

To install this on your own server, you'll need to:
1) Copy all of these files over to your server
2) Update config.php to contain your MySQL database info
3) Install YOURLS (http://yourls.org/) in a subdirectory called url/ referencing the three URL tables created in Step 2
4) Update the web template to remove the copyrighted ODU header, footer, and template (i.e. change leftmenu.php and style.css)
5) Add an /images/ subdirectory and add images into it (for the certificaiton center and main page)
6) Install phpmailer to phpmailer/ subdirectory
7) Add a CRON job to run cleantests_cron.php at least daily
6) Probably something else I'm forgetting!

Additional questions can be sent to Richard Landers, faculty at Old Dominion University (rnlanders@odu.edu).
