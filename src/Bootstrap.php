<?php
    /**
     * @author Alexey Kulikov aka Clops <me@clops.at>
     *
     * This is the main configuration dispatcher for the whole site
     */

	use Silex\Application;
    use Silex\Provider\HttpCacheServiceProvider;
    use Silex\Provider\MonologServiceProvider;
    use Silex\Provider\TwigServiceProvider;
	use Silex\Provider\DoctrineServiceProvider;
    use SilexAssetic\AsseticServiceProvider;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	define("ROOT_PATH", __DIR__ . "/..");

    ####### SETUP ########################################################################################
    #
	# CONFIG FILES -->
	if(file_exists(__DIR__ . '/../resources/config/settings.yml')){ //important!!! composer.phar will create the file on install
		$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/../resources/config/settings.yml'));
	}

    # TWIG -->
    /** @var Silex\Application $app * */
    $app->register(new TwigServiceProvider(), array(
        'twig.options' => array(
            'cache'            => isset($app['twig.options.cache']) ? $app['twig.options.cache'] : false,
            'strict_variables' => true
        ),
        'twig.path'    => array(__DIR__ . '/../resources/views')
    ));

    # CACHE -->
    $app->register(new HttpCacheServiceProvider());

    # MONOLOG -->
    $app->register(new MonologServiceProvider(), array(
        'monolog.logfile' => __DIR__ . '/../resources/log/app.log',
        'monolog.name'    => 'app',
        'monolog.level'   => 300 // = Logger::WARNING
    ));

	# DOCTRINE -->
	$app->register(new DoctrineServiceProvider(), array(
		'db.options' => array(
			'driver'   => 'pdo_sqlite',
			'path'     => __DIR__.'/../resources/db/app.db',
		),
	));

    # ASSETIC (from https://github.com/lyrixx/Silex-Kitchen-Edition/blob/master/src/app.php ) -->
    if (isset($app['assetic.enabled']) && $app['assetic.enabled']) {
        $app->register(new AsseticServiceProvider(), array(
            'assetic.options' => array(
                'debug' => $app['debug'],
                'auto_dump_assets' => $app['debug'],
            )
        ));

        $app['assetic.filter_manager'] = $app->share(
            $app->extend('assetic.filter_manager', function ($fm, $app) {
                $fm->set('lessphp', new Assetic\Filter\LessphpFilter());

                return $fm;
            })
        );

        $app['assetic.asset_manager'] = $app->share(
            $app->extend('assetic.asset_manager', function ($am, $app) {
                $am->set('styles', new Assetic\Asset\AssetCache(
                    new Assetic\Asset\GlobAsset(
                        $app['assetic.input.path_to_css'],
                        array($app['assetic.filter_manager']->get('lessphp'))
                    ),
                    new Assetic\Cache\FilesystemCache($app['assetic.path_to_cache'])
                ));
                $am->get('styles')->setTargetPath($app['assetic.output.path_to_css']);

                $am->set('scripts', new Assetic\Asset\AssetCache(
                    new Assetic\Asset\GlobAsset($app['assetic.input.path_to_js']),
                    new Assetic\Cache\FilesystemCache($app['assetic.path_to_cache'])
                ));
                $am->get('scripts')->setTargetPath($app['assetic.output.path_to_js']);

                return $am;
            })
        );

    }

	## Some Default Headers ###
	//handling CORS preflight request
	$app->before(function (Request $request) {
		if ($request->getMethod() === "OPTIONS") {
			$response = new Response();
			$response->headers->set("Access-Control-Allow-Origin","*");
			$response->headers->set("Access-Control-Allow-Methods","GET,POST,OPTIONS");
			$response->headers->set("Access-Control-Allow-Headers","Content-Type");
			$response->setStatusCode(200);
			return $response->send();
		}
	}, Application::EARLY_EVENT);

	//handling CORS respons with right headers
	$app->after(function (Request $request, Response $response) {
		$response->headers->set("Access-Control-Allow-Origin","*");
		$response->headers->set("Access-Control-Allow-Methods","GET,POST,OPTIONS");
	});

    return $app;
