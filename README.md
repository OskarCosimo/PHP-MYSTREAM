# PHPMYSTREAM
Class to instruct any HTML5 players to play a file in a local environment

Protected Usage:
1. upload the php-mystream.php class in a protected space (possibly outside the root)
1. include php-mystream.php in a new public page "playcontent.php"
2. simply call "MYSTREAM($localpathofmp4file)" in the "playcontent.php" page
3. in the video path of any html video player request directly the page where you make the call to "MYSTREAM($localpathofmp4file)" for example "playcontent.php?videopath=test.mp4"
4. remember to use a protected mechanism to pass the file path
5. adjust the class wherever you want for your environment
