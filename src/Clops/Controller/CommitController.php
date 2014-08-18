<?php
    /**
     * @author Alexey Kulikov aka Clops <me@clops.at>
     */
namespace Clops\Controller;

    use Silex\Application;
    use Symfony\Component\Config\Definition\Exception\Exception;
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
			if(!$request->request->get('file')){
				return $this->error('No file set');
			}

	        if(!$request->request->get('repo')){
		        return $this->error('No repo set');
	        }

	        //save image to local-storage
	        try{
		        $directory = $request->request->get('repo').'/'.date('Y').'/'.date('m').'/';
		        if(!file_exists(ROOT_PATH."/web/commits/".$directory)){
					mkdir(ROOT_PATH."/web/commits/".$directory, 0777, true);
		        }

		        $fileName  = uniqid().'.jpg';
				file_put_contents(ROOT_PATH."/web/commits/".$directory.$fileName, $request->request->get('file'));
	        }catch(Exception $e){
		        $this->error( $e->getMessage() );
	        }

	        //send OK reply
			return new JsonResponse(
				array(
					'status' => 'ok',
					'file'   => 'commits/'.$directory.$fileName
				)
			);
        }


	    /**
	     * @param $message
	     *
	     * @return JsonResponse
	     */
	    private function error( $message ){
		    $response = new JsonResponse(
			    array(
				    'status' => 'error',
				    'message'=> $message
			    )
		    );

		    $response->setStatusCode( 500 ); //internal server error (although it might be something else?)
		    return $response;
	    }

    }
