<?php
    /**
     * @author Alexey Kulikov aka Clops <me@clops.at>
     */
namespace Clops\Controller;

    use Silex\Application;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;

    /**
     * Class PageController
     *
     * @package Clops\Controller
     */
    class CommitController
    {

        /**
         * Splash Page
         *
         * @param Request     $request
         * @param Application $app
         *
         * @return mixed
         */
        public function addAction(Request $request, Application $app)
        {
	        //check if the request has all the desired data

	        //save image to local-storage

	        //create database entry

	        //send OK reply
			return new JsonResponse(
				array(
					'status' => 'ok',
					'id'     => uniqid('lolcommit_', true)
				)
			);
        }

    }
