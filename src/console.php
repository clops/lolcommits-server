<?php

    use Symfony\Component\Console\Application;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Finder\Finder;

    $console = new Application('Silex - Clops Edition', '0.1');

    $app->boot();

    $console
        ->register('assetic:dump')
        ->setDescription('Dumps all assets to the filesystem')
        ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
            if (!$app['assetic.enabled']) {
                return false;
            }

            $dumper = $app['assetic.dumper'];
            if (isset($app['twig'])) {
                $dumper->addTwigAssets();
            }
            $dumper->dumpAssets();
            
            $output->writeln('<info>Dump finished</info>');
        })
    ;

    if (isset($app['cache.path'])) {
        $console
            ->register('cache:clear')
            ->setDescription('Clears the cache')
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {

                $cacheDir = $app['cache.path'];
                $finder = Finder::create()->in($cacheDir)->notName('.gitkeep');

                $filesystem = new Filesystem();
                $filesystem->remove($finder);

                $output->writeln(sprintf("%s <info>success</info>", 'cache:clear'));
            });
    }

	$console
		->register('settings:create')
		->setDescription('Creates a default settings.yml file under resources/config/')
		->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
			//check if a settings file perhaps already exists, if so --> nothing to do
			$filesystem = new Filesystem();
			if($filesystem->exists(ROOT_PATH.'/resources/config/settings.yml')){
				$output->writeln('<error>resources/config/settings.yml already exists, no need to create</error>');
			}else{
				$filesystem->copy(ROOT_PATH.'/resources/config/settings.default.yml', ROOT_PATH.'/resources/config/settings.yml');
				$output->writeln(sprintf("%s <info>success</info>", 'settings:create'));
			}

		});

	$console
		->register('db:create')
		->setDescription('Creates Database Tables')
		->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
			$app['db']->exec("CREATE TABLE IF NOT EXISTS commits(
				sha     CHAR(50) PRIMARY KEY NOT NULL,
				message TEXT,
				repo    CHAR(50),
				image   CHAR(255),
				created DATETIME DEFAULT CURRENT_TIMESTAMP
			)");

			$app['db']->exec("CREATE INDEX IF NOT EXISTS commits_created ON commits(created DESC)");

			$output->writeln("Table commits <info>created</info>");
			$output->writeln("Index commits_created <info>created</info>");
			$output->writeln(sprintf("%s <info>success</info>", 'db:create'));
		});

    return $console;
