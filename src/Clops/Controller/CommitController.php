<?php
	/**
	 * @author Alexey Kulikov aka Clops <me@clops.at>
	 */
	namespace Clops\Controller;

	use Silex\Application;
	use Symfony\Component\Config\Definition\Exception\Exception;
	use Symfony\Component\HttpFoundation\File\UploadedFile;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;

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
			if (!$request->files->has('file')) {
				return $this->error('No file set');
			}

			if (!$request->request->get('repo')) {
				return $this->error('No repo set');
			}

			if (isset($app['config']['main']['key']) && !empty($app['config']['main']['key'])) {
				if (!$request->request->get('key')) {
					return $this->error('No key set');
				}

				//the first check for a string "false" is actuall a bug in the lolcommits plugin, take care!
				if ($request->request->get('key') != 'false' && $app['config']['main']['key'] != $request->request->get('key')) {
					return $this->error('Incorrect key provided, ciao!');
				}
			}

			//init vars
			$directory  = null;
			$fileName   = null;
			$repository = ($request->request->get('repo') == '/' ? '_unknown_' : $request->request->get('repo'));
			$sha        = ($request->request->get('sha')? $request->request->get('sha') : uniqid());
			$message    = ($request->request->get('message')? $request->request->get('message') : uniqid());

			//save image to local-storage
			try {
				$path      = ROOT_PATH . "/web/commits/";
				$directory = $repository . '/' . date('Y') . '/' . date('m') . '/';
				if (!file_exists($path . $directory)) {
					mkdir($path . $directory, 0755, true);
				}

				/** @var UploadedFile $file */
				$file     = $request->files->get('file');
				$fileName = $file->getClientOriginalName();
				$file->move($path . $directory, $file->getClientOriginalName());

				//last but not least --> create database entry
		        $app['db']->insert('commits', array(
			        'sha'     => $sha,
			        'message' => $message,
			        'image'   => $directory . $fileName,
			        'repo'    => $repository
		        ));
	        } catch (Exception $e) {
				$this->error($e->getMessage());
			}

			//send OK reply
			return new JsonResponse(
				array(
					'status' => 'ok',
					'file'   => 'commits/' . $directory . $fileName
				)
			);
		}


		/**
		 * @param $message
		 *
		 * @return JsonResponse
		 */
		private function error($message)
		{
			return new JsonResponse(
				array(
					'status'  => 'error',
					'message' => $message
				)
			);
		}

	}
