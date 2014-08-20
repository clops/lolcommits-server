<?php

    namespace Clops\Composer;

    class Script
    {
        public static function install()
        {
	        self::setDirectoryPermissions();
	        self::executeConsoleCommands();

	        //create a settings file from the default
	        exec('php console settings:create');
	        exec('php console db:create');

	        touch('resources/db/app.db'); //attempt to create it in case it does not exist
	        chmod('resources/db/app.db', 0777);
        }

	    public static function update()
	    {
		    self::executeConsoleCommands();
	    }

	    public static function setDirectoryPermissions()
	    {
		    chmod('resources/cache', 0777);
		    chmod('resources/log', 0777);
		    chmod('resources/db', 0777);
		    chmod('web/assets', 0777);
		    chmod('web/commits', 0777);
		    chmod('console', 0500);
	    }

	    public static function executeConsoleCommands()
	    {
		    exec('php console assetic:dump');
		    exec('php console cache:clear');
	    }


    }
