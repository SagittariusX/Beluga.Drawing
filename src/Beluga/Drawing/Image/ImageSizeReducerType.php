<?php
/**
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga
 * @since          2016-08-18
 * @subpackage     Drawing\Image
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga\Drawing\Image;


/**
 * The Beluga\Drawing\Image\ImageSizeReducerType class.
 *
 * @since v0.1.0
 */
interface ImageSizeReducerType
{

   /**
    * Reduce the image size to declared width and height by holding the proportion and crop it by need.
    *
    * For example: A image with a size of width=250, height=150 should be reduced to an size width=75, height=100
    *
    * So the base image first is reduced by holding the proportion to an size that fits the width=75, height=100
    * this is width=167, height=100
    *
    * If there is no overflow to required size the resized image is returned.
    *
    * Otherwise the required image part is cropped. If no gravity and no contentAlign is defined it is cropped
    * centered from the image.
    */
   const CROP = 0;

   /**
    * Reduce the image size to fit a declared maximum size and hold the image size proportion.
    *
    * For example: A image with a size of width=250, height=150 should be reduced to an size width=75, height=100
    *
    * So the base image reduced by holding the proportion to an size that can be fitted by the required size
    * width=75, height=100: this is width=75, height=45
    */
   const RESIZE = 1;

   /**
    * Reduce the image size. The reduced image size should have reduced the longer side to declared length. The shorter
    * side length is reduced by holding the image size proportion.
    *
    * For it you have to declare 2 lengths. One for Landscape size format images and one for portrait and quadratic
    * format images.
    */
   const LONG_SIDE = 2;

   /**
    * Reduce the image size. The reduced image size should have reduced the shorter side to declared length. The longer
    * side length is reduced by holding the image size proportion.
    *
    * For it you have to declare 2 lengths. One for Landscape size format images and one for portrait and quadratic
    * format images.
    */
   const SHORT_SIDE = 3;

}

