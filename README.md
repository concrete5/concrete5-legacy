     oNMMMMMMMMMMMMMMMMMMMMMMMMMMMNo 
    0MMMMMMMMMMMMMMMXodWMMMMMMMMMMMM0
    MMMMMMMMMOdKMMMW.  kMMMMMMMMMMMMM
    MMMMMMMMN   KMMX   kMMW;.lMMMMMMM
    MMMMMMMMM.  cMMX   kMMo  ;MMMMMMM
    MMMc .XMMl   WMX   OMN.  OMMMMMMM
    MMMl  :MMN   0MM.  XMd  'MMMMMMMM
    MMMM:  0MMx  kMMc ,MM,  0MMMMMMMM
    MMMMMc 'MMMklNWNXkNMM:.xMMMMMMMMM
    MMMMMMk.X0o;.       .:OMMMMMMMMMM
    MMMMMMMk.             ,MMMNOOXMMM
    MMMMMMl     .xKNXK0kkKMWx'   .NMM                                                  _______ 
    MMMMMN      KMMMMMMMMWx.    lWMMM                                        _        (_______)
    MMMMMM;     :XMMMMMXo.    cNMMMMM    ____ ___  ____   ____  ____ _____ _| |_ _____ ______  
    MMMMMMWl      ..'..     lNMMMMMMM   / ___) _ \|  _ \ / ___)/ ___) ___ (_   _) ___ (_____ \ 
    MMMMMMMMNo,.         ,xWMMMMMMMMM  ( (__| |_| | | | ( (___| |   | ____| | |_| ____|_____) )
    OMMMMMMMMMMMN0OkkkOXMMMMMMMMMMMMO   \____)___/|_| |_|\____)_|   |_____)  \__)_____|______/ 
     lNMMMMMMMMMMMMMMMMMMMMMMMMMMMNl 

# Note

This is the development distribution of concrete5. It is bleeding edge. For fully supported releases, check out

http://www.concrete5.org

# Installation Instructions for concrete5

1. Make sure your config/, packages/ and files/ directories are writable by a web server. These directories are in the root of the archive. This can either be done by making the owner of the directories the web server user, or by making them world writable using chmod 777 (in Linux/OS X.)
2. Create a new MySQL database and a MySQL user account with the following privileges on that database: INSERT, SELECT, UPDATE, DELETE, CREATE, DROP, ALTER
3. Visit your Concrete5 site in your web browser. You should see an installation screen where you can specify your site's name, your base URL, and your database settings, and the rest of the information necessary to install concrete5.
4. Click through and wait for the installation to complete.
5. concrete5 should be installed.
	
# Documentation

http://concrete5.org/documentation/

### Short Tags
The concrete5 git repository currently uses php "short tags". Pull reqests should maintain this convention. Final release versions have short tags converted to long tags. _Note:_ This issue has thoroughly discussed. Currently the shed is red but may be painted green in the future.

If short tags are not enabled in your development environment you can enable them either
* In `php.ini` add `short_open_tag = On`
* In Apache `.htaccess` add `php_value short_open_tag 1`
