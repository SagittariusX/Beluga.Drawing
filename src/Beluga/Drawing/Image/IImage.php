<?php
/**
 * In this file the interface {@see \Beluga\Drawing\Image\IImage} is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga
 * @since          2016-08-14
 * @subpackage     Drawing\Image
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga\Drawing\Image;


use \Beluga\Drawing\{ContentAlign,Gravity,Point,Rectangle,Size};


/**
 * @since v0.1
 */
interface IImage
{


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Disposes the current image resource.
    */
   public function dispose();

   /**
    * Returns the image width.
    *
    * @return integer
    */
   public function getWidth() : int;

   /**
    * Returns the image height.
    *
    * @return integer
    */
   public function getHeight() : int;

   /**
    * Returns the image size.
    *
    * @return \Beluga\Drawing\Size
    */
   public function getSize() : Size;

   /**
    * Returns the associated image mime type.
    *
    * @return string
    */
   public function getMimeType() : string;

   /**
    * Returns all user defined image colors as array of {@see \Beluga\Drawing\ColorGD} objects.
    *
    * return array Array of {@see \Beluga\Drawing\ColorGD}
    */
   public function getUserColors() : array;

   /**
    * Returns the user color, registered under user defined color name.
    *
    * @param  string $name
    * @return \Beluga\Drawing\ColorGD
    */
   public function getUserColor( string $name );

   /**
    * Returns the path of current open image file, if it is defined.
    *
    * @return string
    */
   public function getFile();

   /**
    * Returns if the underlying image resource is destroyed/disposed.
    *
    * @return boolean
    */
   public function isDisposed() : bool;

   /**
    * Return if the current image is an true color image.
    *
    * @return boolean
    */
   public function isTrueColor() : bool;

   /**
    * Returns if the current image can use some transparency.
    *
    * @return boolean
    */
   public function canUseTransparency() : bool;

   /**
    * Adds a new user defined, named color and returns the associated {@see \Beluga\Drawing\Color} instance.
    *
    * @param  string $name The user defined color name
    * @param  string|RGB-Array|integer|\Beluga\Drawing\ColorGD|\Beluga\Drawing\Color $colorDefinition
    * @param  integer $opacity The opacity (0-100) in percent.
    * @return \Beluga\Drawing\ColorGD or FALSE
    * @throws \Beluga\ArgumentError
    */
   public function addUserColor( string $name, $colorDefinition, int $opacity = 100 );

   public function __get( string $name );

   public function __clone();

   /**
    * Saves the current image to defined image file. If no image file is defined the internally defined image file path
    * is used. If its also not defined a {@see \Beluga\IO\Exception} is thrown.
    *
    * @param  string  $file    Path of image file to save the current image instance. (Must be only defined if the
    *                          instance does not define a path.
    * @param  integer $quality Image quality if it is saved as an JPEG image. (1-100)
    * @throws \Beluga\IO\IOError
    */
   public function save( string $file = null, int $quality = 75 );

   /**
    * Outputs the current image, including all required HTTP headers and exit the script.
    *
    * @param  integer $quality  Image quality if it is an JPEG image. (1-100)
    * @param  string  $filename Output image file name for HTTP headers.
    */
   public function output( int $quality = 60, string $filename = null );

   /**
    * Returns if the current image is an PNG image.
    *
    * @return boolean
    */
   public function isPng() : bool;

   /**
    * Returns if the current image is an GIF image.
    *
    * @return boolean
    */
   public function isGif() : bool;

   /**
    * Returns if the current image is an JPEG image.
    *
    * @return boolean
    */
   public function isJpeg() : bool;

   /**
    * Returns if currently a image file path is defined that can be used to store the image.
    *
    * @return boolean
    */
   public function hasAssociatedFile() : bool;

   // </editor-fold>

   /**
    * Rotates the current image in 90° steps.
    *
    * @param  int  $angle 90, 180, 270, -90, -180, -270
    * @param  string|\Beluga\Drawing\Color|\Beluga\Drawing\ColorGD $fillColor
    * @param  bool $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError If $angle is not 0, 90 or a multiple of 90.
    */
   public function rotateSquarely( int $angle = 90, $fillColor = null, bool $internal = true );


   # <editor-fold desc="// = >   C R O P P I N G">

   /**
    * Crops the defined image part.
    *
    * @param  integer $width The width
    * @param  integer $height The height
    * @param  integer $gravity The gravity. Use one of the constants from {@see \Beluga\Drawing\Gravity}.
    * @param  boolean $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public function crop( int $width, int $height, int $gravity = Gravity::TOP_LEFT, bool $internal = true ) : IImage;

   /**
    * Crops the defined image part.
    *
    * @param  integer $width
    * @param  integer $height
    * @param  \Beluga\Drawing\ContentAlign $align
    * @param  boolean $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError If width and/or height is lower than 1
    */
   public function crop2( int $width, int $height, ContentAlign $align, bool $internal = true ) : IImage;

   /**
    * Crop the defined image part and returns the current or new image instance.
    *
    * @param  \Beluga\Drawing\Rectangle $rect
    * @param  boolean               $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public function cropRect( Rectangle $rect, bool $internal = true ) : IImage;

   /**
    * Crop the max usable quadratic image part from declared position and returns the current or new image instance.
    *
    * @param  integer|string $gravity  The gravity. Use one of the constants from {@see \Beluga\Drawing\Gravity}.
    * @param  boolean        $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public function cropQuadratic( int $gravity = Gravity::MIDDLE_CENTER, bool $internal = true ) : IImage;

   /**
    * Crop the max usable quadratic image part from declared position and returns the current or new image instance.
    *
    * @param  \Beluga\Drawing\ContentAlign $align
    * @param  boolean                  $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public function cropQuadratic2( ContentAlign $align, bool $internal = true ) : IImage;

   // </editor-fold>


   // <editor-fold desc="// = >   C O N T R A C T I O N">

   /**
    * Reduce the original image size by the specified percentage value.
    *
    * @param  int  $percent The percentage value.
    * @param  bool $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError If $percent is lower than 1 oder greater than 99.
    */
   public function contract( int $percent, bool $internal = true ) : IImage;

   /**
    * Reduce the original image size by holding the image size proportion, to fit best the declared
    * maximum size.
    *
    * @param  \Beluga\Drawing\Size $maxsize
    * @param  bool $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError If $maxsize is empty. or bigger than current image
    */
   public function contractToMaxSize( Size $maxsize, bool $internal = true ) : IImage;

   /**
    * Reduce the original image size by holding the image size proportion.
    * The longer image side will be reduced to a length, defined by $maxSideLandscape or $maxSidePortrait,
    * depending to the format of the current image
    *
    * @param  int   $maxSideLandscape The max. longer side length of landscape format images.
    * @param  int   $maxSidePortrait  The max. longer side length of portrait/quadratic format images.
    * @param  bool  $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError If $maxSideLandscape or $maxSidePortrait is empty.
    */
   public function contractToMaxSide( int $maxSideLandscape, int $maxSidePortrait, bool $internal = true ) : IImage;

   /**
    * Reduce the original image size by holding the image size proportion.
    * The shorter image side will be reduced to a length, defined by $maxSideLandscape or $maxSidePortrait,
    * depending to the format of the current image
    *
    * @param  int   $maxSideLandscape The max. shorter side length of landscape format images.
    * @param  int   $maxSidePortrait  The max. shorter side length of portrait/quadratic format images.
    * @param  bool  $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError If $maxSideLandscape or $maxSidePortrait is empty.
    */
   public function contractToMinSide( int $maxSideLandscape, int $maxSidePortrait, bool $internal = true ) : IImage;

   // </editor-fold>


   // <editor-fold desc="// = >   P L A C I N G">

   /**
    * Places the defined $placement image inside the current image.
    *
    * @param  \Beluga\Drawing\Image\IImage $placement The image that should be placed
    * @param  \Beluga\Drawing\Point        $point     The top left point of the placement
    * @param  int                          $opacity   The opacity of the placement in % (1-100)
    * @param  bool                         $internal  Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError
    */
   public function place( IImage $placement, Point $point, int $opacity = 100, bool $internal = true ) : IImage;

   /**
    * Places the defined $placement image inside the current image.
    *
    * @param  \Beluga\Drawing\Image\IImage $placement The image that should be placed
    * @param  int                          $padding   The min. padding around the placement
    * @param  int                          $gravity   The placement gravity inside the current image
    *                                                 (see {@see \Beluga\Drawing\Image\Gravity}::* constants)
    * @param  int                          $opacity   The opacity of the placement in % (1-100)
    * @param  bool                         $internal  Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError
    */
   public function placeWithGravity(
      IImage $placement, int $padding, $gravity = Gravity::MIDDLE_CENTER, int $opacity = 100, bool $internal = true )
      : IImage;

   /**
    * Places the defined $placement image inside the current image.
    *
    * @param  \Beluga\Drawing\Image\IImage $placement The image that should be placed
    * @param  int                          $padding   The min. padding around the placement
    * @param  \Beluga\Drawing\ContentAlign $align     The placement align inside the current image
    * @param  int                          $opacity   The opacity of the placement in % (1-100)
    * @param  bool                         $internal  Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError
    */
   public function placeWithContentAlign(
      IImage $placement, int $padding, ContentAlign $align, int $opacity = 100, bool $internal = true ) : IImage;

   // </editor-fold>


   // <editor-fold desc="// = >   B O R D E R I N G">

   /**
    * Draws a single border with defined color around the image.
    *
    * @param  string|array|\Beluga\Drawing\ColorGD|\Beluga\Drawing\Color $borderColor
    * @param  bool                                                       $internal    Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public function drawSingleBorder( $borderColor, bool $internal = true ) : IImage;

   /**
    * Draws a double border with defined colors around the image.
    *
    * @param  string|array|\Beluga\Drawing\ColorGD|\Beluga\Drawing\Color $innerBorderColor
    * @param  string|array|\Beluga\Drawing\ColorGD|\Beluga\Drawing\Color $outerBorderColor
    * @param  bool                                                       $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public function drawDoubleBorder( $innerBorderColor, $outerBorderColor, bool $internal = true ) : IImage;

   // </editor-fold>


   // <editor-fold desc="// = >   T E X T   D R A W I N G">

   /**
    * Draws a text at declared position into current image.
    *
    * @param  string $text       The text that should be drawn.
    * @param  string $font       The font that should be used. For example: For GdImage you can define here
    *                            the path of the *.ttf font file or NULL. If you define null the parameter
    *                            $fontSize declares the size + font (1-4). If a path is defined here $fontSize
    *                            should declare the size in points.
    * @param  int    $fontSize   The size of the font.
    * @param  string|array|\Beluga\Drawing\Color|\Beluga\Drawing\ColorGD $color The text color
    * @param  \Beluga\Drawing\Point                                      $point The top left point where to start the text
    * @param  bool   $internal   Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public function drawText( string $text, $font, $fontSize, $color, Point $point, bool $internal = true ) : IImage;

   /**
    * Draws a text by using {@see \Beluga\Drawing\ContentAlign} into current image.
    *
    * @param  string $text       The text that should be drawn.
    * @param  string $font       The font that should be used. For example: For GdImage you can define here
    *                            the path of the *.ttf font file or NULL. If you define null the parameter
    *                            $fontSize declares the size + font (1-4). If a path is defined here $fontSize
    *                            should declare the size in points.
    * @param  int    $fontSize   The size of the font.
    * @param  string|array|\Beluga\Drawing\Color|\Beluga\Drawing\ColorGD $color The text color
    * @param  int    $padding    The padding around the text.
    * @param  int    $gravity    The gravity of the text inside the Image. (see {@see \Beluga\Drawing\Gravity}::*)
    * @param  bool   $internal   Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public function drawTextWithGravity(
      string $text, $font, $fontSize, $color, int $padding, int $gravity, bool $internal = true ) : IImage;

   /**
    * Draws a text by using a gravity into current image.
    *
    * @param  string $text       The text that should be drawn.
    * @param  string $font       The font that should be used. For example: For GdImage you can define here
    *                            the path of the *.ttf font file or NULL. If you define null the parameter
    *                            $fontSize declares the size + font (1-4). If a path is defined here $fontSize
    *                            should declare the size in points.
    * @param  int    $fontSize   The size of the font.
    * @param  string|array|\Beluga\Drawing\Color|\Beluga\Drawing\ColorGD $color The text color
    * @param  int    $padding    The padding around the text.
    * @param  \Beluga\Drawing\ContentAlign $align
    * @param  bool   $internal   Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public function drawTextWithAlign(
      string $text, $font, $fontSize, $color, int $padding, ContentAlign $align, bool $internal = true ) : IImage;

   // </editor-fold>

}

