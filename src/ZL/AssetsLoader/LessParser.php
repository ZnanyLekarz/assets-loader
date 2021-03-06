<?php
/**
 * User: Maciej Łebkowski
 * Date: 2013-09-01 12:53
 */

namespace ZL\AssetsLoader;

class LessParser
{
	protected $cachePath;
	protected $lessc;

	public function __construct($cachePath)
	{
		$this->lessc = new \lessc;
		$this->setCachePath($cachePath);
	}

	public function setCachePath($cachePath)
	{
		if (false === is_dir($cachePath))
		{
			mkdir($cachePath, 0770, true);
		}
		$this->cachePath = realpath($cachePath);
	}

	public function compile($filePath)
	{
		$ext = pathinfo($filePath, PATHINFO_EXTENSION);
		if ('less' !== $ext)
		{
			return $filePath;
		}

		$output = sprintf('%s/%s-%s', $this->cachePath, basename(dirname($filePath)), basename($filePath, 'less') . 'css');
		$cache = $output . '.cache';
		if (file_exists($cache))
		{
			$root = json_decode(file_get_contents($cache), true);
		}
		else
		{
			$root = $filePath;
		}

//		$this->lessc->checkedCompile($filePath, $output);

		$root = $this->lessc->cachedCompile($root);
		if (isset($root['compiled']))
		{
			file_put_contents($output, $root['compiled']);
			unset($root['compiled']);
			file_put_contents($cache, json_encode($root));
		}
		return $output;
	}

}
