<?php
/**
 * In this file the class {@see \Beluga\Drawing\Point} is defined.
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


use \Beluga\Type;


/**
 * This class defines a 2 dimensional point.
 *
 * @since      v0.1.0
 */
class Point
{


   // <editor-fold desc="// = = = =   P U B L I C   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The X coordinate (horizontal)
    *
    * @var integer
    */
   public $x;

   /**
    * The Y coordinate (vertical)
    *
    * @var integer
    */
   public $y;

   // </editor-fold>

   
   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new instance.
    *
    * @param integer $x  The X coordinate (horizontal)
    * @param integer $y  The Y coordinate (vertical)
    */
   public function __construct( int $x = 0, int $y = 0 )
   {

      $this->x = $x;
      $this->y = $y;

   }

   // </editor-fold>

   
   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Returns if X and Y is 0.
    *
    * @return boolean
    */
   public final function isEmpty() : bool
   {

      return $this->x === 0 && $this->y === 0;

   }

   /**
    * Returns a string, representing the current point. (Format: x=%d; y=%d)
    * 
    * @return string
    */
   public function __toString()
   {

      return \sprintf( 'x=%d; y=%d', $this->x, $this->y );

   }

   /**
    * Return a associative array with the 2 elements 'x' and 'y'.
    * 
    * @return array
    */
   public function toArray() : array
   {

      return [
         'x' => $this->x,
         'y' => $this->y
      ];

   }

   # </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Parse a value to an \Beluga\Drawing\Point instance. If the value uses an invalid format (boolean)FALSE is returned.
    *
    * @param  \Beluga\Drawing\Point|\Beluga\Drawing\Rectangle|string|array $value
    * @param  \Beluga\Drawing\Point &$output
    * @return boolean
    */
   public static function TryParse( $value, &$output ) : bool
   {

      if ( \is_null( $value ) )
      {
         return false;
      }

      if ( $value instanceof Point )
      {
         $output = $value;
         return true;
      }

      if ( $value instanceof Rectangle )
      {
         $output = $value->Point;
         return true;
      }

      if ( \is_string( $value ) )
      {
         $hits = null;
         if ( \preg_match( '~^x=(\d{1,4});\s*y=(\d{1,4})$~', $value, $hits ) )
         {
            $output = new Point( \intval( $hits[ 1 ] ), \intval( $hits[ 2 ] ) );
            return true;
         }
         if ( \preg_match( '~^(\d{1,4}),\s*(\d{1,4})$~', $value, $hits ) )
         {
            $output = new Point( \intval( $hits[ 1 ] ), \intval( $hits[ 2 ] ) );
            return true;
         }
         return false;
      }

      if ( \is_array( $value ) )
      {
         if ( isset( $value[ 'x' ] ) && isset( $value[ 'y' ] ) )
         {
            $output = new Point( \intval( $value[ 'x' ] ), \intval( $value[ 'y' ] ) );
            return true;
         }
         if ( isset( $value[ 'X' ] ) && isset( $value[ 'Y' ] ) )
         {
            $output = new Point( \intval( $value[ 'X' ] ), \intval( $value[ 'Y' ] ) );
            return true;
         }
         if ( \count( $value ) != 2 )
         {
            return false;
         }
         if ( isset( $value[ 0 ] ) && isset( $value[ 1 ] ) )
         {
            $output = new Point( \intval( $value[ 0 ] ), \intval( $value[ 1 ] ) );
            return true;
         }
         return false;
      }

      $type = new Type( $value );
      if ( $type->hasAssociatedString() )
      {
         $hits = null;
         if ( \preg_match( '~^x=(\d{1,4});\s*y=(\d{1,4})$~', $type->getStringValue(), $hits ) )
         {
            $output = new Point( \intval( $hits[ 1 ] ), \intval( $hits[ 2 ] ) );
            return true;
         }
         if ( \preg_match( '~^(\d{1,4}),\s*(\d{1,4})$~', $type->getStringValue(), $hits ) )
         {
            $output = new Point( \intval( $hits[ 1 ] ), \intval( $hits[ 2 ] ) );
            return true;
         }
      }

      return false;

   }

   // </editor-fold>


}

