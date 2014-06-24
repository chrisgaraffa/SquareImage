<?php

/**
* 
*/
class SquareImage {
	var $image;
	var $thumbnailW = 70;
	var $thumbnailH = 70;

	function __construct($path = '') {
		if (strlen($path)) {
			$this->setImage($path);
		}
	}

	function setImage($path) {
		$this->image = new Imagick();
		$this->image->readImageFile($path);
	}

	function getImage() {
		return $this->$image;
	}

	function setThumbnailSize($w, $h) {
		$this->setThumbnailWidth($w);
		$this->setThumbnailHeight($h);
	}

	function setThumbnailWidth($w) {
		$this->thumbnailW = $w;
	}

	function setThumbnailHeight($h) {
		$this->thumbnailH = $h;
	}

	function getThumbnailWidth() {
		return $this->thumbnailW;
	}

	function getThumbnailHeight() {
		return $this->thumbnailH;
	}

	function imageEntropy($img) {
		$hist = $img->getImageHistogram();
		$hist_size = array_sum($hist);
		$newHist = array();
		foreach ($hist as $h) {
			$newHist[] = float($h) / $hist_size;
		}

		$returnVal = 0;
		foreach ($hist as $p) {
			if ($p > 0) {
				$returnVal += $p * log($p, 2);
			}
		}
		return -1 * $returnVal;
	}

	function getSquareImage() {
		$imageGeometry = $this->image->getImageGeometry();
		$x = $imageGeometry['width'];
		$y = $imageGeometry['height'];

		while ($y > $x) {
			$slice_height = min(y-x, 10);

			$bottom = clone $this->image;
			$top = clone $this->image;

			$bottom = $bottom->cropImage(0, $y - $slice_height, $x, $y);
			$top = $top->cropImage(0, 0, $x, $slice_height);

			if ($this->imageEntropy($bottom) < $this->imageEntropy($top)) {
				$this->image = $this->image->cropImage(0, 0, $x, $y - $slice_height);
			} else {
				$this->image = $this->image->crop(0, $slice_height, $x, $y);
			}

			$imageGeometry = $this->image->getImageGeometry();
			$x = $imageGeometry['width'];
			$y = $imageGeometry['height'];
		}

		return $this->image;
	}
}
