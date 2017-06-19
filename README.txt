


==================SETUP CREDITENTIALS===================
Go to install.bash
Enter your information for LOCATION, UTORID, DBNAME, DBPASSWORD, you only get one chance to make the setup run, because it will change the information in the api/api.php

The credentials for the htpasswd is as follows:
user: self
password: good

Please ensure you edit .htaccess for the absolute path to htpasswd

================Features===================
1. Moving the header image, JQuery Shake
2. Using mD5 password hashing protection
3. added number of games played in the table
4. Customozied the charts and buttons with customized styling
5. Email: you have to add a valid email which should contain an @ sign, with the appropiate regular expression
6. Autofocus: we have added this 'autofocus' in the 'login' view page, where the user can start typing in the input field right away once it displays the page.
7. Go Back: we added another 'Go back' button in the 'register' view page, which can allow the User to go back to the login button, if the User changes his/her mind.
8. READ-ONLY & Disabled: we have added this feature in 'profile' view page, where it will display the 'Username', but the User can only view its Username once it Autofills it into the field, but unable to modify it. 
9. Added a Favicon to the site


===============Game Mechanics===========
The scoring of the game is dependent on the duration of the game. For example, if you last for 10 seconds, the score will be 10 seconds. If you kill the devil monster, you get 100 points, and if you kill elite monster you get 500 points. The movement of Elite monster is very thrilling. 

===============User Management==========
Since I did mod_rewrite, where in this link below:
https://cs.utm.utoronto.ca/~abbass13/ww/api/users/fajo/
-> "users" is the name of table in my database
-> "fajo" is the name of user in the table
-> if there is another parameter such as highscores...i.e /api/users/fajo/highscores/, it will display the top 3 highscores of that users, CHECK THE EXAMPLES BELOW

Example:
You may have to type in this url request
https://cs.utm.utoronto.ca/~abbass13/ww/api/users/fajo/

and it may show
{"name":"fajo","email":"fajo@fajo.com","numGamesPlayed":"46","Last Login":"2017-03-06 23:38:18.501899"}

Example2:
https://cs.utm.utoronto.ca/~abbass13/ww/api/users/fajo/highscores/

may show:
[{"userscore":"570"},{"userscore":"361"},{"userscore":"326"}]


