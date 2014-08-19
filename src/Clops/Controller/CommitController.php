<?php
    /**
     * @author Alexey Kulikov aka Clops <me@clops.at>
     */
namespace Clops\Controller;

    use Silex\Application;
    use Symfony\Component\Config\Definition\Exception\Exception;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\File\UploadedFile;

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
			if(!$request->files->has('file') ){
				return $this->error('No file set');
			}

	        if(!$request->request->get('repo')){
		        return $this->error('No repo set');
	        }

	        //@todo check if there is a key set and it matches the one configured here

	        //init vars
	        $directory = null;
	        $fileName  = null;

	        //save image to local-storage
	        try{
		        $path      = ROOT_PATH."/web/commits/";
		        $directory = $request->request->get('repo').'/'.date('Y').'/'.date('m').'/';
		        if(!file_exists($path.$directory)){
					mkdir($path.$directory, 0755, true);
		        }

		        /** @var UploadedFile $file */
		        $file      = $request->files->get('file');
		        $fileName  = $file->getClientOriginalName();
				$file->move($path.$directory, $file->getClientOriginalName());
		        //move_uploaded_file($file->getRealPath(), ROOT_PATH."/web/commits/".$directory.$fileName);
	        }catch(Exception $e){
		        $this->error( $e->getMessage() );
	        }

	        $ret = array(
		        'status' => 'ok',
		        'file'   => 'commits/'.$directory.$fileName
	        );

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
