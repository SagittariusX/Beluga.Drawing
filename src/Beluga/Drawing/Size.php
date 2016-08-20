<?php
/**
 * In this file the class {@see \Beluga\Drawing\Size} is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga
 * @since          2016-08-14
 * @subpackage     Drawing
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga\Drawing;


use \Beluga\BelugaError;
use \Beluga\DynamicProperties\ExplicitGetterSetter;
use \Beluga\IO\{IOError, FileNotFoundError};


/**
 * Defines a size.
 *
 * @since v0.1.0
 * @property-read bool $fixed  Defines if the instance values are fixed (not changeable/read-only).
 * @property      int  $width  The width value.
 * @property      int  $height The height value.
 */
class Size extends ExplicitGetterSetter
{


   // <editor-fold desc="// = = = =   P R I V A T E   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * If TRUE nothing can be changed for current Size instance.
    *
    * @var bool
    */
   private $_fixed = false;

   /**
    * The current width.
    *
    * @var integer
    */
   private $_width;

   /**
    * The current height.
    *
    * @var integer
    */
   private $_height;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R I V A T E   S T A T I C   F I E L D S   = = = = = = = = = = = = = = = = = =">

   private static $emptyFixed = null;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new instance.
    *
    * @param  int  $width  The current width.
    * @param  int  $height The current height.
    * @param  bool $fixed  Is this instance fixed? (fixed is the read-only mode).
    */
   public function __construct( int $width = 0, int $height = 0, bool $fixed = false )
   {

      $this->_width  = $width;
      $this->_height = $height;
      $this->_fixed  = $fixed;

      if ( $this->_width < 0 )
      {
         $this->_width = 0;
      }

      if ( $this->_height < 0 )
      {
         $this->_height = 0;
      }

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">


   // <editor-fold desc="// - - -   G E T T E R   - - - - - - - - - - - - - - - - - - - - - -">

   /**
    * Gets the width.
    *
    * @return int
    */
   public final function getWidth() : int
   {

      return $this->_width;

   }

   /**
    * Gets the height.
    *
    * @return int
    */
   public final function getHeight() : int
   {

      return $this->_height;

   }

   /**
    * Gets if the instance values are fixed (not changeable).
    *
    * @return bool
    */
   public final function getFixed() : bool
   {

      return $this->_fixed;

   }

   // </editor-fold>


   // <editor-fold desc="// - - -   S E T T E R   - - - - - - - - - - - - - - - - - - - - - -">

   /**
    * Sets the width.
    *
    * @param  int $width
    * @return Size
    * @throws \Beluga\BelugaError If the instance is set to fixed
    */
   public final function setWidth( int $width ) : Size
   {

      $this->triggerFixedErrorByNeed( 'width-change' );

      $this->_width = \max( $width, 0 );

      return $this;

   }

   /**
    * Sets the height.
    *
    * @param  int $height
    * @return Size
    * @throws \Beluga\BelugaError If the instance is set to fixed
    */
   public final function setHeight( int $height ) : Size
   {

      $this->triggerFixedErrorByNeed( 'height-change' );

      $this->_height = \max( $height, 0 );

      return $this;

   }

   // </editor-fold>


   // <editor-fold desc="// = >   C h e c k i n g   M e t h o d s">

   /**
    * Returns if one of the 2 class properties is 0 or lower.
    *
    * @return boolean
    */
   public function isEmpty() : bool
   {

      return $this->_width <= 0 || $this->_height <= 0;

   }

   /**
    * Returns if the current size can contain the size dimensions of the defined size.
    *
    * @param  \Beluga\Drawing\Size $size
    * @return bool
    */
   public final function contains( Size $size ) : bool
   {

      return (
         ( $size->_width <= $this->_width )
         &&
         ( $size->_height <= $this->_height )
      );

   }

   /**
    * Returns if the current size defines a quadratic size (width === height)
    *
    * @return boolean
    */
   public final function isQuadratic() : bool
   {

      return ( $this->_width === $this->_height );

   }

   /**
    * Returns if the current size uses a portrait format (width &lt; height)
    *
    * @return bool
    */
   public final function isPortrait() : bool
   {

      return ( $this->_width < $this->_height );

   }

   /**
    * Returns if the current size uses a portrait format (width &gt; height)
    *
    * @return boolean
    */
   public final function isLandscape() : bool
   {

      return ( $this->_width > $this->_height );

   }

   /**
    * Returns if the current size uses a near quadratic format. It means, if width and height
    * difference is lower or equal to ?? percent.
    *
    * @param double $maxDifference If you want to change the allowed difference percent value you can do it here.
    *                              Valid values here are 0.01 (means 1%) to 0.3 (means 30%) default is 0.15
    * @return boolean
    */
   public final function isNearQuadratic( float $maxDifference = 0.15 ) : bool
   {

      if ( $this->isQuadratic() )
      {
         return true;
      }

      if ( $maxDifference >= 0.01 && $maxDifference <= 0.3 )
      {
         $diff = 1.0 + $maxDifference;
      }
      else
      {
         $diff = 1.15;
      }

      if ( $this->isPortrait() )
      {
         return ( ( 0.0 + $this->_height ) / $this->_width ) <= $diff;
      }

      return ( ( 0.0 + $this->_width ) / $this->_height ) <= $diff;

   }

   // </editor-fold>


   // <editor-fold desc="// = >   D e c r e a s i n g   M e t h o d s">

   /**
    * Decrease both sides (width + height) by the defined value. (w=100, h=200) decreased by 50 is (w=50 h=150)
    *
    * @param  int $value
    * @return \Beluga\Drawing\Size
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function reduce( int $value = 1 ) : Size
   {

      $this->triggerFixedErrorByNeed( 'reduce' );
      
      $this->_width  = $this->_width - $value;
      $this->_height = $this->_height - $value;

      return $this;

   }

   /**
    * Decrease both sides (width + height) by the defined percent value. (w=100, h=200) decreased by 10%
    * is (w=90 h=180).
    *
    * @param  int $percent The percent value for decreasing. Must be lower than 100!
    * @return \Beluga\Drawing\Size
    * @throws \Beluga\BelugaError  If $percent is not lower than 100 or if its lower than 1
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function reduceByPercent( int $percent ) : Size
   {

      $this->triggerFixedErrorByNeed( 'reduceByPercent' );

      if ( $percent >= 100 || $percent < 1 )
      {
         throw new BelugaError(
            'Drawing',
            'Size contraction only works with percent values lower 100 and bigger than 0'
         );
      }

      $this->_width  = \intval( \floor( $this->_width  * ( ( 100 - $percent ) / 100 ) ) );
      $this->_height = \intval( \floor( $this->_height * ( ( 100 - $percent ) / 100 ) ) );

      return $this;

   }

   /**
    * Decrease the current size to fit the defined Size, by holding its proportions.
    *
    * @param  \Beluga\Drawing\Size $maxSize
    * @return bool TRUE on success, or FALSE, if current size is already smaller than defined size.
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function reduceToMaxSize( Size $maxSize ) : bool
   {

      $this->triggerFixedErrorByNeed( 'reduceToMaxSize' );

      if ( ( $this->_width  < $maxSize->_width )
        && ( $this->_height < $maxSize->_height ) )
      {
         return false;
      }

      if ( ( $this->_width  === $maxSize->_width )
        && ( $this->_height === $maxSize->_height ) )
      {
         return true;
      }

      $dw = $this->_width / $maxSize->_width;
      $dh = $this->_height / $maxSize->_height;

      if ( $dw < $dh )
      {
         $this->_width = \intval(
            \floor(
               ( $this->_width * $maxSize->_height ) / $this->_height
            )
         );
         $this->_height = $maxSize->_height;
      }
      else if ( $dw > $dh )
      {
         $this->_height = \intval(
            \floor(
               ( $maxSize->_width * $this->_height ) / $this->_width
            )
         );
         $this->_width = $maxSize->_width;
      }
      else # ($dw == $dh)
      {
         $this->_width  = $maxSize->_width;
         $this->_height = $maxSize->_height;
      }

      return true;

   }

   /**
    * Decrease the longest side to the defined length and also contracts the shorter side to hold the proportion
    * of this size. If the current size defines a quadratic size, it is also decreased but with searching the
    * longer side.
    *
    * @param  int $newMaxSideLength
    * @return bool TRUE on success, or FALSE if longest side is already shorter or equal to $newMaxSideLength.
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function reduceMaxSideTo( int $newMaxSideLength ) : bool
   {

      $this->triggerFixedErrorByNeed( 'reduceMaxSideTo' );

      if ( $this->isPortrait() )
      {

         if ( $newMaxSideLength >= $this->_height )
         {
            return false;
         }

         $resultPercent = ( 100 * $newMaxSideLength ) / $this->_height;

         $this->_width = \intval(
            \floor(
               ( $resultPercent * $this->_width ) / 100
            )
         );
         $this->_height = $newMaxSideLength;

      }
      else
      {

         if ( $newMaxSideLength >= $this->_width )
         {
            return false;
         }

         $resultPercent = ( 100 * $newMaxSideLength ) / $this->_width;

         $this->_height = \intval(
            \floor(
               ( $resultPercent * $this->_height ) / 100
            )
         );
         $this->_width = $newMaxSideLength;

      }

      return true;

   }

   /**
    * Decrease the longest side to the defined length and also contracts the shorter side to hold the proportion
    * of this size. The value, used as max side length is depending to the current size format.
    *
    * $newLandscapeMaxWidth is used if the current size uses a landscape ord quadratic format. Otherwise the
    * $newPortraitMaxHeight is used.
    *
    * @param  int $newLandscapeMaxWidth The max width, used if size has landscape or quadratic format.
    * @param  int $newPortraitMaxHeight The max height, used if size has portrait format.
    * @return bool
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function reduceMaxSideTo2( int $newLandscapeMaxWidth, int $newPortraitMaxHeight ) : bool
   {

      $this->triggerFixedErrorByNeed( 'reduceMaxSideTo2' );

      if ( $this->isPortrait() )
      {

         if ( $newPortraitMaxHeight >= $this->_height )
         {
            return false;
         }
         $resultPercent = ( 100 * $newPortraitMaxHeight ) / $this->_height;
         $this->_width = \intval(
            \floor(
               ( $resultPercent * $this->_width ) / 100
            )
         );
         $this->_height = $newPortraitMaxHeight;

      }
      else
      {

         if ( $newLandscapeMaxWidth >= $this->_width )
         {
            return false;
         }
         $resultPercent = ( 100 * $newLandscapeMaxWidth ) / $this->_width;
         $this->_height = \intval(
            \floor(
               ( $resultPercent * $this->_height ) / 100
            )
         );
         $this->_width = $newLandscapeMaxWidth;

      }

      return true;

   }

   /**
    * Decrease the shortest side to the defined length and also contracts the longer side to hold the proportion
    * of this size. If the current size defines a quadratic size, it is also decreased but with searching the
    * longer side.
    *
    * @param  int $newShortSideLength
    * @return bool TRUE on success, or FALSE if shortest side is already shorter or equal to $newShortSideLength.
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function reduceMinSideTo( int $newShortSideLength ) : bool
   {

      $this->triggerFixedErrorByNeed( 'reduceMinSideTo' );

      if ( $this->isPortrait() )
      {

         if ( $newShortSideLength >= $this->_width )
         {
            return false;
         }

         $resultPercent = ( 100 * $newShortSideLength ) / $this->_width;

         $this->_height = \intval(
            \floor(
               ( $resultPercent * $this->_height ) / 100
            )
         );
         $this->_width = $newShortSideLength;

      }
      else
      {

         if ( $newShortSideLength >= $this->_height )
         {
            return false;
         }

         $resultPercent = ( 100 * $newShortSideLength ) / $this->_height;

         $this->_width = \intval(
            \floor(
               ( $resultPercent * $this->_width ) / 100
            )
         );
         $this->_height = $newShortSideLength;

      }

      return true;

   }

   /**
    * Decrease the shortest side to the defined length and also contracts the longer side to hold the proportion
    * of this size. The value, used as max side length is depending to the current size format.
    *
    * $newLandscapeMaxHeight is used if the current size uses a landscape ord quadratic format. Otherwise the
    * $newPortraitMaxWidth is used.
    *
    * @param  int $newLandscapeMaxHeight The max height, used if size has landscape or quadratic format.
    * @param  int $newPortraitMaxWidth   The max width, used if size has portrait format.
    * @return bool
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function reduceMinSideTo2( int $newLandscapeMaxHeight, int $newPortraitMaxWidth ) : bool
   {

      $this->triggerFixedErrorByNeed( 'reduceMinSideTo2' );

      if ( $this->isPortrait() )
      {

         if ( $newPortraitMaxWidth >= $this->_width )
         {
            return false;
         }
         $resultPercent = ( 100 * $newPortraitMaxWidth ) / $this->_width;
         $this->_height = \intval(
            \floor(
               ( $resultPercent * $this->_height ) / 100
            )
         );
         $this->_width = $newPortraitMaxWidth;

      }
      else
      {

         if ( $newLandscapeMaxHeight >= $this->_height )
         {
            return false;
         }
         $resultPercent = ( 100 * $newLandscapeMaxHeight ) / $this->_height;
         $this->_width = \intval(
            \floor(
               ( $resultPercent * $this->_width ) / 100
            )
         );
         $this->_height = $newLandscapeMaxHeight;

      }

      return true;

   }

   // </editor-fold>


   // <editor-fold desc="// = >   E x p a n d i n g   M e t h o d s">

   /**
    * Expands both sides (width + height) by the defined value. (w=100, h=200) expanded by 50 is (w=150 h=250)
    *
    * @param  int $value
    * @return \Beluga\Drawing\Size
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function expand( int $value = 1 ) : Size
   {

      $this->triggerFixedErrorByNeed( 'expand' );

      $this->_width  = $this->_width + $value;
      $this->_height = $this->_height + $value;

      return $this;

   }

   /**
    * Expands the longest side to the defined length and also expands the shorter side to hold the proportion
    * of this size. If the current size defines a quadratic size, its also expanded but without searching the
    * longer side.
    *
    * @param  integer $newMaxSideLength
    * @return boolean TRUE on success, or FALSE if longest side is already longer than $newMaxSideLength.
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function expandMaxSideTo( int $newMaxSideLength ) : bool
   {

      $this->triggerFixedErrorByNeed( 'expandMaxSideTo' );

      if ( $this->isPortrait() )
      {

         if ( $newMaxSideLength < $this->_height )
         {
            return false;
         }
         $resultPercent = ( 100 * $newMaxSideLength ) / $this->_height;
         $this->_width = \intval(
            \floor(
               ( $resultPercent * $this->_width ) / 100
            )
         );
         $this->_height = $newMaxSideLength;

      }
      else
      {

         if ( $newMaxSideLength < $this->_width )
         {
            return false;
         }
         $resultPercent = ( 100 * $newMaxSideLength ) / $this->_width;
         $this->_height = \intval(
            \floor(
               ( $resultPercent * $this->_height ) / 100
            )
         );
         $this->_width = $newMaxSideLength;

      }

      return true;

   }

   /**
    * Expands the longest side to the defined length and also expands the shorter side to hold the proportion
    * of this size. The value, used as max side length is depending to the current size format.
    *
    * $newLandscapeMaxWidth is used if the current size uses a landscape ord quadratic format. Otherwise the
    * $newPortraitMaxHeight is used.
    *
    * @param  int $newLandscapeMaxWidth The max width, used if size has landscape or quadratic format.
    * @param  int $newPortraitMaxHeight The max height, used if size has portrait format.
    * @return bool
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function expandMaxSideTo2( int $newLandscapeMaxWidth, int $newPortraitMaxHeight ) : bool
   {

      $this->triggerFixedErrorByNeed( 'expandMaxSideTo2' );

      if ( $this->isPortrait() )
      {

         if ( $newPortraitMaxHeight <= $this->_height )
         {
            return false;
         }
         $resultPercent = ( 100 * $newPortraitMaxHeight ) / $this->_height;
         $this->_width = \intval(
            \floor(
               ( $resultPercent * $this->_width ) / 100
            )
         );
         $this->_height = $newPortraitMaxHeight;

      }
      else
      {

         if ( $newLandscapeMaxWidth <= $this->_width )
         {
            return false;
         }
         $resultPercent = ( 100 * $newLandscapeMaxWidth ) / $this->_width;
         $this->_height = \intval(
            \floor(
               ( $resultPercent * $this->_height ) / 100
            )
         );
         $this->_width = $newLandscapeMaxWidth;

      }

      return true;

   }

   // </editor-fold>


   // <editor-fold desc="// = >   R e s i z i n g   M e t h o d s">

   /**
    * Changes both sides (width + height) by the defined value. (w=100, h=200) resized by -10 is (w=90 h=190)
    *
    * @param  int $value
    * @return \Beluga\Drawing\Size
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function resize( int $value = 1 ) : Size
   {

      $this->triggerFixedErrorByNeed( 'resize' );

      $this->_width  = $this->_width + $value;
      $this->_height = $this->_height + $value;

      return $this;

   }

   /**
    * Resize the longest side to the defined length and also resize the shorter side to hold the proportion
    * of this size. If the current size defines a quadratic size, its also resized but without searching the
    * longer side. :-)
    *
    * @param  integer $newSideLength
    * @return boolean
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function resizeMaxSideTo( int $newSideLength ) : bool
   {

      if ( $this->isPortrait() )
      {

         if ( $newSideLength == $this->_height )
         {
            return false;
         }
         if ( $newSideLength < $this->_height )
         {
            return $this->reduceMaxSideTo( $newSideLength );
         }
         return $this->expandMaxSideTo( $newSideLength );

      }

      if ( $newSideLength == $this->_width )
      {
         return false;
      }

      if ( $newSideLength < $this->_width )
      {
         return $this->reduceMaxSideTo( $newSideLength );
      }

      return $this->expandMaxSideTo( $newSideLength );

   }

   /**
    * Resize the longest side to the defined length and also resize the shorter side to hold the proportion
    * of this size. The value, used as max side length is depending to the current size format.
    *
    * $newLandscapeWidth is used if the current size uses a landscape ord quadratic format. Otherwise the
    * $newPortraitHeight is used.
    *
    * @param  int $newLandscapeWidth Max. width for landscape format or quadratic format.
    * @param  int $newPortraitHeight Max. height for portrait format.
    * @return bool
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function resizeMaxSideTo2( int $newLandscapeWidth, int $newPortraitHeight ) : bool
   {

      if ( $this->isPortrait() )
      {

         // Portrait format

         if ( $newPortraitHeight == $this->_height )
         {
            // No resizing required
            return false;
         }

         if ( $newPortraitHeight < $this->_height )
         {
            // Decrease the current size
            return $this->reduceMaxSideTo2( $newLandscapeWidth, $newPortraitHeight );
         }

         // Expand the current size
         return $this->expandMaxSideTo2( $newLandscapeWidth, $newPortraitHeight );

      }

      // Landscape format

      if ( $newLandscapeWidth == $this->_width )
      {
         // No resizing required
         return false;
      }

      if ( $newPortraitHeight < $this->_width )
      {
         // Decrease the current size
         return $this->reduceMaxSideTo2( $newLandscapeWidth, $newPortraitHeight );
      }

      // Expand the current size
      return $this->expandMaxSideTo2( $newLandscapeWidth, $newPortraitHeight );

   }

   // </editor-fold>


   // <editor-fold desc="// = >   R o t a t i o n   M e t h o d s">

   /**
    * Rotate the current size by 90°. (In other words: width and height are exchanged.)
    *
    * @return \Beluga\Drawing\Size
    * @throws \Beluga\BelugaError  If the instance is set to fixed.
    */
   public final function rotateSquare() : Size
   {

      $this->triggerFixedErrorByNeed( 'rotateSquare' );

      $tmp          = $this->_width;
      $this->_width  = $this->_height;
      $this->_height = $tmp;

      return $this;

   }

   // </editor-fold>


   /**
    * Returns the size string. Format is: "width=?; height=?"
    *
    * @return string
    */
   public function __toString()
   {

      return \sprintf( 'width=%d; height=%d', $this->_width, $this->_height );

   }

   /**
    * Returns the width and height as array with the keys 0=width, 1=height, 'width' and 'height'
    *
    * So you can access, for example the height by $returnedArray[ 1 ] or by $returnedArray[ 'height' ]
    *
    * @return array
    */
   public function toArray() : array
   {

      return [
         0        => $this->_width,
         1        => $this->_height,
         'width'  => $this->_width,
         'height' => $this->_height
      ];

   }

   /**
    * @return \Beluga\Drawing\Size
    */
   public function __clone()
   {

      return new Size( $this->_width, $this->_height );

   }


   // </editor-fold>


   // <editor-fold desc="// = = = =   P R I V A T E   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * @param string $operationName
    * @throws \Beluga\BelugaError
    */
   private function triggerFixedErrorByNeed( string $operationName )
   {
      if ( $this->_fixed )
      {
         throw new BelugaError(
            'Drawing.Image',
            'Invalid operation "' . $operationName . '"! This Size instance is a fixed size.'
         );
      }
   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Parses a array to a {@see \Beluga\Drawing\Size} instance.
    *
    * @param  array $objectData
    * @param  bool  $fixed      Is this instance fixed? (fixed is the read-only mode).
    * @return \Beluga\Drawing\Size
    */
   public static function FromArray( array $objectData, bool $fixed = false ) : Size
   {

      $w = 0;
      if ( isset( $objectData[ 'width' ] ) )
      {
         $w = \intval( $objectData[ 'width' ] );
      }
      else if ( isset( $objectData[ 'Width' ] ) )
      {
         $w = \intval( $objectData[ 'Width' ] );
      }
      else if ( isset( $objectData[ 0 ] ) )
      {
         $w = \intval( $objectData[ 0 ] );
      }

      $h = 0;
      if ( isset( $objectData[ 'height' ] ) )
      {
         $h = \intval( $objectData[ 'height' ] );
      }
      else if ( isset( $objectData[ 'Height' ] ) )
      {
         $h = \intval( $objectData[ 'Height' ] );
      }
      else if ( isset( $objectData[ 1 ] ) )
      {
         $h = \intval( $objectData[ 1 ] );
      }

      if ( isset( $objectData[ 'fixed' ] ) )
      {
         $fixed = (bool) \intval( $objectData[ 'fixed' ] );
      }

      return new Size( $w, $h, $fixed );

   }

   /**
    * Parses a string to a {@see \Beluga\Drawing\Size} instance.
    *
    * Accepted formats are :
    *
    * - width=<WIDTH>; height=<HEIGHT>
    * - <WIDTH>, <HEIGHT>
    * - an absolute path of an existing image file with an format, known by PHP
    *
    * @param  string $objectString
    * @param  bool  $fixed      Is this instance fixed? (fixed is the read-only mode).
    * @return \Beluga\Drawing\Size|FALSE
    */
   public static function FromString( string $objectString, bool $fixed = false )
   {

      if ( \preg_match( '~^width=(\d{1,10});\s*height=(\d{1,10})(;\s*fixed=true)?$~i', $objectString, $hits ) )
      {
         return new Size( \intval( $hits[ 1 ] ), \intval( $hits[ 2 ] ), ! empty( $hits[ 3 ] ) || $fixed );
      }

      if ( \preg_match( '~^(\d{1,10}),\s*(\d{1,10})$~i', $objectString, $hits ) )
      {
         return new Size( \intval( $hits[ 1 ] ), \intval( $hits[ 2 ] ), $fixed );
      }

      if ( ! \preg_match( '~^[A-Za-z0-9_.:,;/!$%~*+-]+$~', $objectString ) )
      {
         return false;
      }

      if ( ! \file_exists( $objectString ) )
      {
         return false;
      }

      try
      {
         $tmp = \getimagesize( $objectString );
      }
      catch ( \Throwable $ex )
      {
         $ex = null;
         return false;
      }

      return new Size( \intval( $tmp[ 0 ] ), \intval( $tmp[ 1 ] ), $fixed );

   }

   /**
    * Tries to parse the the defined values as a size and returns the success state.
    *
    * If parsing was successful (TRUE is returned) the resulting Beluga\Drawing\Size is
    * returned by the $size parameter.
    *
    * @param  mixed            $value int|double|array|string|image-resource
    * @param  \Beluga\Drawing\Size $size  The resulting {@see \Beluga\Drawing\Size} instance.
    * @param  bool             $fixed     Is this instance fixed? (fixed is the read-only mode).
    * @return boolean
    */
   public static function TryParse( $value, &$size, bool $fixed = false ) : bool
   {

      $size = null;

      if ( \is_int( $value ) )
      {
         // An single integer value will result in an quadratic size
         $size = new Size( $value, $value, $fixed );
         return true;
      }

      if ( \is_double( $value ) )
      {
         // Double|Float in converted to integer
         $size = new Size( (int) $value, (int) $value, $fixed );
         return true;
      }

      if ( \is_array( $value ) )
      {
         // Array get parsed by FromArray
         return ( false !== ( $size = self::FromArray( $value, $fixed ) ) );
      }

      if ( \is_string( $value ) )
      {
         // String get parsed by FromString
         return ( false !== ( $size = self::FromString( $value, $fixed ) ) );
      }

      if ( \is_resource( $value ) && \get_resource_type( $value ) === 'gd' )
      {
         try
         {
            $size = new Size( \imagesx( $value ), \imagesy( $value ), $fixed );
            return true;
         }
         catch ( \Throwable $ex )
         {
            $ex = null;
            return false;
         }
      }

      return false;

   }

   /**
    * Parses a value to a {@see \Beluga\Drawing\Size} instance. If parsing fails, it returns boolean FALSE.
    *
    * @param  mixed $value
    * @param  bool  $fixed     Is this instance fixed? (fixed is the read-only mode).
    * @return \Beluga\Drawing\Size|bool Or boolean FALSE
    */
   public static function Parse( $value, bool $fixed = false )
   {

      $res = null;

      if ( ! self::TryParse( $value, $res, $fixed ) )
      {
         return false;
      }

      return $res;

   }

   /**
    * @param  string $imageFile
    * @param  bool   $fixed     Is this instance fixed? (fixed is the read-only mode).
    * @return \Beluga\Drawing\Size
    * @throws \Beluga\IO\FileNotFoundError
    * @throws \Beluga\IO\IOError
    */
   public static function FromImageFile( $imageFile, bool $fixed = false ) : Size
   {

      if ( ! \file_exists( $imageFile ) )
      {
         throw new FileNotFoundError( 'Drawing', $imageFile, 'Can not get size from image file.' );
      }

      try
      {
         $tmp = \getimagesize( $imageFile );
         return new Size( \intval( $tmp[ 0 ] ), \intval( $tmp[ 1 ] ), $fixed );
      }
      catch ( \Throwable $ex )
      {
         throw new IOError( 'Drawing', $imageFile, 'Can not get size from image file.', 256, $ex );
      }

   }

   /**
    * Gets a empty Size instance.
    *
    * @param  bool   $fixed     Is this instance fixed? (fixed is the read-only mode).
    * @return \Beluga\Drawing\Size
    */
   public static function Empty( bool $fixed = false ) : Size
   {

      if ( $fixed )
      {
         return \is_null( static::$emptyFixed )
            ? ( static::$emptyFixed = new Size( 0, 0, true ) )
            : static::$emptyFixed;
      }

      return new Size( 0, 0, $fixed );

   }

   # </editor-fold>


}

