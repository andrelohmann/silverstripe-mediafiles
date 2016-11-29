<?php
/**
 * Restart processing of all failed video files
 *
 * @package framework
 * @subpackage filesystem
 */
class FinishProcessingVideoFiles extends BuildTask {

	protected $title = 'Restart processing of all stuck video files';

	protected $description = 'Restart processing of all stuck VideoFile objects';

	/**
	 * Check that the user has appropriate permissions to execute this task
	 */
	public function init() {
		if(!Director::is_cli() && !Director::isDev() && !Permission::check('ADMIN')) {
			return Security::permissionFailure();
		}

		parent::init();
	}

	/**
	 * Clear out the image manipulation cache
	 * @param SS_HTTPRequest $request
	 */
	public function run($request) {
		$failedFiles = 0;
		$Videos = VideoFile::get()->filter(array('ProcessingStatus' => 'processing'))->sort('ID');

		foreach($Videos as $vid){
			
			$failedFiles++;
			
			$vid->ProcessingStatus = 'new';
			$vid->write();
			
			$vid->onAfterLoad();
			
			sleep(5);
		}

		echo "$failedFiles stuck VideoFile objects have reinitiated the processing.";
	}

}
