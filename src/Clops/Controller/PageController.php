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
	        //optional limit for infinite waypoint scrolling
	        $limit   = $request->get('limit');
	        if(!$limit){ //current time is the default
		        $limit = date('Y-m-d H:i:s');
	        }
sleep(2);
	        //get the last 10 commits
	        $commits = $app['db']->fetchAll("SELECT * FROM commits WHERE created < ? ORDER BY created DESC LIMIT 10", array(
		        (string)$limit
	        ));

	        $total   = $app['db']->fetchAssoc("SELECT count(*) AS total FROM commits WHERE created < ?", array(
		        (string)$limit
	        ));

            return $app['twig']->render('index.html.twig', array(
	            'commits' => $commits,
                'total'   => $total['total']
            ));
        }

    }
