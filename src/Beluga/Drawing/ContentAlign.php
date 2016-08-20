<?php
/**
 * In this file the class {@see \Beluga\Drawing\ContentAlign} is defined.
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


/**
 * Defines the content align of a element.
 *
 * @since      v0.1
 */
class ContentAlign
{

   
   // <editor-fold desc="// = = = =   C L A S S   C O N S T A N T S   = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Bottom right aligned
    */
   const BOTTOM_RIGHT = 0;

   /**
    * Bottom center aligned
    */
   const BOTTOM = 1;

   /**
    * Bottom left aligned
    */
   const BOTTOM_LEFT = 2;

   /**
    * Middle right aligned
    */
   const MIDDLE_RIGHT = 3;

   /**
    * Middle center aligned
    */
   const MIDDLE = 4;

   /**
    * Middle left aligned
    */
   const MIDDLE_LEFT = 5;

   /**
    * Top right aligned
    */
   const TOP_RIGHT = 6;

   /**
    * Top center aligned
    */
   const TOP = 7;

   /**
    * Top left aligned
    */
   const TOP_LEFT = 8;

   // </editor-fold>

   
   // <editor-fold desc="// = = = =   P U B L I C   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The alignment value (one of the {@see \Beluga\Drawing\ContentAlign} class constants).
    *
    * @var integer
    */
   public $value;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new instance.
    *
    * @param integer $value One of the {@see \Beluga\Drawing\ContentAlign}::* class constants
    */
   public function __construct( int $value = self::BOTTOM_RIGHT )
   {
      
      if ( ! static::isValidValue( $value ) )
      {
         
         if ( ! \is_string( $value ) )
         {
            $this->value = static::BOTTOM_RIGHT;
         }
         else if ( \preg_match( '~^[0-8]$~', $value ) )
         {
            $this->value = \intval( $value );
         }
         else
         {
            $this->value = static::BOTTOM_RIGHT;
         }
      }
      else
      {
         $this->value = $value;
      }
      
   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   public function __toString()
   {

      return \strval( $this->value );

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   private static function isValidValue( $value ) : bool
   {

      if ( ! \is_int( $value ) )
      {
         return false;
      }

      return $value > -1 && $value < 9;

   }

   // </editor-fold>


}

