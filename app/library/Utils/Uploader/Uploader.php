<?php

namespace Phalcon\Utils\Uploader;

use \Phalcon\Mvc\User\Component;
use \Phalcon\Image\Adapter\GD;
use \Phalcon\Security\Random;
use \Phalcon\Utils\Uploader\Validator;

class Uploader extends Component {

	private $files;

	private $rules = [];

	private $info = [];

	private $validator;

	private $random;

	public function __construct($rules = [])
	{
		if (empty($rules) === false) {
			$this->setRules($rules);
		}

		// get validator
		$this->validator = new Validator();

		// Get korean random
		$this->random = new Random();
	}

	public function setRules(array $rules)
	{
		foreach ($rules as $key => $values) {

			if ((is_array($values) === true && empty($values) === false) || is_callable($values)) {
				$this->rules[$key] = $values;
			} else {
				$this->rules[$key] = trim($values);
			}
		}

		return $this;
	}

	public function isValid()
	{
		// get files for upload
		$this->files = $this->request->getUploadedFiles();

		if (
			sizeof($this->files) > 0 &&
			sizeof($this->files) <= $this->config->site->maxFiles
		) {

			// do any actions if files exists

			foreach ($this->files as $file) {

				// apply all the validation rules for each file

				foreach ($this->rules as $key => $rule) {

					if (method_exists($this->validator, 'check' . ucfirst($key)) === true) {
						$this->validator->{'check' . ucfirst($key)}($file, $rule);
					}
				}
			}
		}

		$errors = $this->getErrors();

		return (empty($errors) === true) ? true : false;
	}

	public function move()
	{
		// do any actions if files exists
		foreach ($this->files as $file) {

			$slug = $this->random->hex(10);

			// Set files name
			$fileName = $slug . '.' . $file->getExtension();
			$thumbName = $slug . '_t.' . 'jpg';

			// Files directory
			$filedir = rtrim($this->rules['dynamic'], '/') . DIRECTORY_SEPARATOR;

			// Set files path
			$filePath = $filedir . $fileName;
			$thumbPath = $filedir . $thumbName;

			// move file to target directory
			$isUploaded = $file->moveTo($filePath);

			// Create thumb from file
			$thumb = new GD($filePath);
			$width = $thumb->getWidth();
			$height = $thumb->getHeight();
			$thumb->background('#EDEDED')->resize(200, 200)->save($thumbPath, 95);

			if ($isUploaded === true) {
				$this->info[] = [
					'filepath'  => $filePath,
					'thumbtmp'  => $thumbPath,
					'directory' => dirname($filePath),
					'filename'  => $fileName,
					'slug'      => $slug,
					'size'      => $file->getSize(),
					'extension' => $file->getExtension(),
					'width' 	=> $width,
					'height' 	=> $height
				];
			}
		}

		return $this->getInfo();
	}


	public function getErrors()
	{
		// error container
		return $this->validator->errors;
	}

	public function getInfo()
	{
		// error container
		return $this->info;
	}

	public function truncate()
	{
		if (empty($this->info) === false) {
			foreach ($this->info as $file) {
				if (file_exists($file['filepath'])) {
					unlink($file['filepath']);
					unlink($file['thumbtmp']);
				}
			}
		}
	}
}
