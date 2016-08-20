<?php
/**
 * In this file the class {@see \Beluga\Drawing\Rectangle} is defined.
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


use \Beluga\TypeTool;


/**
 * Represents a location depending 2 dimensional rectangle.
 *
 * @since      v0.1.0
 * @property-read int $right
 * @property-read int $bottom
 * @property-read int $left Alias of x
 * @property-read int $x
 * @property-read int $top Alias of y
 * @property-read int $y
 * @property-read int $width
 * @property-read int $height
 */
class Rectangle
{


   // <editor-fold desc="// = = = =   P U B L I C   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The rectangle position.
    *
    * @var \Beluga\Drawing\Point
    */
   public $point;

   /**
    * The rectangle size
    *
    * @var \Beluga\Drawing\Size
    */
   public $size;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new instance.
    *
    * @param \Beluga\Drawing\Point $point
    * @param \Beluga\Drawing\Size  $size
    */
   public function __construct( Point $point = null, Size $size = null )
   {

      if ( ! \is_null( $point ) )
      {
         $this->point = $point;
      }
      else
      {
         $this->point = new Point();
      }

      if ( ! \is_null( $size ) )
      {
         $this->size = $size;
      }
      else
      {
         $this->size = new Size();
      }

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Returns the X position of the right rectangle corner.
    *
    * @return integer
    */
   public final function getRight() : int
   {

      return $this->point->x + $this->size->width;

   }

   /**
    * Returns the Y position of the bottom rectangle corner.
    *
    * @return integer
    */
   public final function getBottom() : int
   {

      return $this->point->y + $this->size->height;

   }

   /**
    * Returns, if the current rectangle contains the defined rectangle.
    *
    * @param  \Beluga\Drawing\Rectangle $rect
    * @return boolean
    */
   public final function contains( Rectangle $rect ) : bool
   {

      return (
         (
            (
               (
                  $this->point->x <= $rect->point->x
               )
               && (
                  ( $rect->point->x + $rect->size->width ) <= ( $this->point->x + $this->size->width )
               )
            )
            &&
            (
               $this->point->y <= $rect->point->y
            )
         )
         &&
         (
            ( $rect->point->y + $rect->size->height ) <= ( $this->point->y + $this->size->height )
         )
      );

   }

   /**
    * Returns, if the current rectangle contains the defined location.
    *
    * @param  int|\Beluga\Drawing\Point $xOrPoint
    * @param  int|null                  $y
    * @return boolean
    */
   public final function containsLocation( $xOrPoint, int $y = null ) : bool
   {

      $x = $xOrPoint;

      if ( $x instanceof Point )
      {
         $y = $x->y;
         $x = $x->x;
      }
      else if ( \is_integer( $xOrPoint ) )
      {
         $x = $xOrPoint;
      }
      else if ( TypeTool::IsInteger( $xOrPoint ) )
      {
         $x = \intval( $xOrPoint );
      }
      else
      {
         $x = 0;
      }

      if ( ! \is_int( $y ) )
      {
         $y = \intval( $y );
      }

      return (
         (
            (
               ( $this->point->x <= $x )
               &&
               ( $x < ( $this->point->x + $this->size->width ) )
            )
            &&
            ( $this->point->y <= $y )
         )
         &&
         ( $y < ( $this->point->y + $this->size->height ) )
      );

   }

   /**
    * Returns, if the current rectangle contains the defined Size.
    *
    * @param  \Beluga\Drawing\Size|integer $widthOrSize
    * @param  integer                  $height
    * @return boolean
    */
   public final function containsSize( $widthOrSize, int $height = null ) : bool
   {

      $width = $widthOrSize;

      if ( $width instanceof Size )
      {
         $height = $width->height;
         $width  = $width->width;
      }
      else if ( \is_integer( $widthOrSize ) )
      {
         $width = $widthOrSize;
      }
      else if ( TypeTool::IsInteger( $widthOrSize ) )
      {
         $width = \intval( $widthOrSize );
      }
      else
      {
         $width = 0;
      }

      if ( ! \is_int( $height ) )
      {
         $height = \intval( $height );
      }

      return
         $this->size->width  >= $width
         &&
         $this->size->height >= $height;

   }

   /**
    * Gets a clone of the current instance.
    *
    * @return \Beluga\Drawing\Rectangle
    */
   public function getClone() : Rectangle
   {

      return new Rectangle(
         new Point( $this->point->x   , $this->point->y     ),
         new Size ( $this->size->width, $this->size->height )
      );

   }

   public function __clone()
   {

      return $this->getClone();

   }

   /**
    * Builds the intersection (Schnittmenge) between the current and the defined rectangle.
    *
    * @param  \Beluga\Drawing\Rectangle $rect
    * @return \Beluga\Drawing\Rectangle Returns the resulting rectangle or boolean FALSE if no intersection exists.
    */
   public final function intersect( Rectangle $rect )
   {

      $x = \max( $this->point->x, $rect->point->x );

      $num2 = \min(
         ( $this->point->x + $this->size->width ),
         ( $rect->point->x + $rect->size->width )
      );

      $y = \max( $this->point->y, $rect->point->y );

      $num4 = \min(
         ( $this->point->y + $this->size->height ),
         ( $rect->point->y + $rect->size->height )
      );

      if ( ( $num2 >= $x ) && ( $num4 >= $y ) )
      {
         return Rectangle::Init(
            $x,
            $y,
            $num2 - $x,
            $num4 - $y
         );
      }
      else
      {
         return false;
      }

   }

   /**
    * Returns if the current rectangle is a empty rectangle (width, height, x and y must be lower than 1)
    *
    * @return boolean
    */
   public final function isEmpty() : bool
   {

      return $this->point->isEmpty()
         &&  $this->size->width  < 1
         &&  $this->size->height < 1;

   }

   /**
    * Builds a new rectangle that must contain the current rectangle and the defined rectangle. (a union)
    *
    * @param  \Beluga\Drawing\Rectangle $rect
    * @return \Beluga\Drawing\Rectangle Returns the resulting rectangle.
    */
   public final function union( Rectangle $rect )
   {

      $x = \max( $this->point->x, $rect->point->x );
      $num2 = \max(
         $this->point->x + $this->size->width,
         $rect->point->x + $rect->size->width
      );

      $y = \min( $this->point->y, $rect->point->y );
      $num4 = \max(
         $this->point->y + $this->size->height,
         $rect->point->y + $rect->size->height
      );

      return Rectangle::Init(
         $x,
         $y,
         $num2 - $x,
         $num4 - $y
      );

   }

   public function  __get( $name )
   {

      switch ( \strtolower( $name ) )
      {

         case 'left':
         case 'x':
            return $this->point->x;

         case 'top':
         case 'y':
            return $this->point->y;

         case 'width':
         case 'w':
            return $this->size->width;

         case 'height':
         case 'h':
            return $this->size->height;

         case 'bottom':
            return $this->getBottom();

         case 'right':
            return $this->getRight();

         default:
            return false;

      }

   }

   /**
    * Returns the rectangle string. Format is: "width=?; height=?; x=?; y=?"
    *
    * @return string
    */
   public function __toString()
   {

      return (string) $this->point . '; ' . (string) $this->size;

   }

   /**
    * Returns a array with all instance data. Used array keys are 'x', 'y', 'width' and 'height'.
    *
    * @return array
    */
   public function toArray() : array
   {

      return [
         'x'      => $this->point->x,
         'y'      => $this->point->y,
         'width'  => $this->size->width,
         'height' => $this->size->height
      ];

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * …
    *
    * @param  integer $x
    * @param  integer $y
    * @param  integer $width
    * @param  integer $height
    * @return \Beluga\Drawing\Rectangle
    */
   public static function Init( int $x, int $y, int $width, int $height ) : Rectangle
   {

      return new Rectangle(
         new Point( $x, $y ),
         new Size( $width, $height )
      );

   }

   /**
    * …
    *
    * @param resource $imageResource
    * @return \Beluga\Drawing\Rectangle
    */
   public static function FromImageResource( $imageResource ) : Rectangle
   {

      return new Rectangle(
         new Point(),
         new Size( \imagesx( $imageResource ), \imagesy( $imageResource ) )
      );

   }

   /**
    * Parses a string to a {@see \Beluga\Drawing\Rectangle} instance.
    *
    * Accepted string must use the following format:
    *
    * <code>x=0; y=0; width=800; height=600</code>
    *
    * @param  string $objectString
    * @return \Beluga\Drawing\Rectangle Or boolean FALSE
    */
   public static function FromString( string $objectString )
   {

      $hits = null;

      if ( ! \preg_match( '~^x=(\d{1,4});\s*y=(\d{1,4});\s*width=(\d{1,4});\s*height=(\d{1,4})$~',
         $objectString, $hits ) )
      {
         return false;
      }

      return self::Init(
         \intval( $hits[ 1 ] ),
         \intval( $hits[ 2 ] ),
         \intval( $hits[ 3 ] ),
         \intval( $hits[ 4 ] )
      );

   }

   /**
    * Parses a array to a {@see \Beluga\Drawing\Rectangle} instance.
    *
    * Required array keys are: x, y, width, height
    *
    * @param  array $objectData
    * @return \Beluga\Drawing\Rectangle Or boolean FALSE
    */
   public static function FromArray( array $objectData )
   {

      $objectData = \array_change_key_case( $objectData );

      if ( isset( $objectData[ 'x' ] )
        && isset( $objectData[ 'y' ] )
        && isset( $objectData[ 'width' ] )
        && isset( $objectData[ 'height' ] ) )
      {
         return self::Init(
            \intval( $objectData['x'] ),
            \intval( $objectData['y'] ),
            \intval( $objectData['width'] ),
            \intval( $objectData['height'] )
         );
      }

      return false;

   }

   /**
    * Parses a array to a {@see \Beluga\Drawing\Rectangle} instance. Required format "x=?; y=?; width=?; height=?"
    *
    * @param  string                 $value
    * @param  \Beluga\Drawing\Rectangle &$output
    * @return boolean
    */
   public static function TryParse( $value, &$output ) : bool
   {

      if ( $value instanceof Rectangle )
      {
         $output = $value;
         return true;
      }

      if ( \is_string( $value ) )
      {
         return ( false !== ( $output = self::FromString( $value ) ) );
      }

      if ( \is_array( $value ) )
      {
         return ( false !== ( $output = self::FromArray( $value ) ) );
      }

      return false;

   }

   # </editor-fold>


}

