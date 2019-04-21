Releasing and deploying
=======================

Create a release of the latest code
-----------------------------------

Ensure we are on master branch

	git checkout master

Set version:

	VERSION="3.5.6"
	bin/set_version.sh $VERSION
	
Commit and tag:	
	
	git commit -am "Updated version to $VERSION"
	git tag $VERSION
	git push && git push --tags
	
Merge tag to latest stable code branch:
	
	git checkout 3.5-stable
	git merge --no-ff $VERSION
	git push

Set development version on master:
	
	git checkout master
	
	VERSION="3.5-dev"
	bin/set_version.sh 
	git commit -am "Updated version to $VERSION"
	git push

Update game code on server
--------------------------

Login to the server
run the command "update-game-round <roundname>". Example:
    
  update-game-round round15

Restart the event handler in the web-based admin tool.

//Use this only if script above fails	
Login to the server, become the user running the game round and change into the game round directory, for example:
	
	sudo -u etoa -s
	cd /var/www/round14.game.etoa.net/

Ensure we are on the correct branch:

	git status
	
Update code, migrate database and rebuild eventhandler code:
	
	git pull && bin/db.php migrate && eventhandler/bin/build.sh && php composer.phar install -o --no-dev
	
Restart the event handler in the web-based admin tool.
