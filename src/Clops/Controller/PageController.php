<?php
    /**
     * @author Alexey Kulikov aka Clops <me@clops.at>
     */
namespace Clops\Controller;

    use Silex\Application;
    use Symfony\Component\HttpFoundation\Request;

    /**
     * Class PageController
     *
     * @package Clops\Controller
     */
    class PageController
    {

        /**
         * Splash Page
         *
         * @param Request     $request
         * @param Application $app
         *
         * @return mixed
         */
        public function indexAction(Request $request, Application $app)
        {

	        //indeed, this is currently rather dumb
	        //plan is to load only the last X lol-commits and add waypoint
	        //navigation for a correctly working pager...
	        $files  = glob(ROOT_PATH.'/web/commits/*/*/*.jpg');
	        foreach($files as &$file){
		        $file = str_replace(ROOT_PATH.'/web/', '', $file);
	        }

            return $app['twig']->render('index.html.twig', array('images' => $files));
        }

    }
