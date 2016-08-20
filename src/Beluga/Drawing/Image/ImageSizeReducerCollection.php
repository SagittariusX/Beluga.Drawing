<?php
/**
 * In this file the class {@see \Beluga\Drawing\Image\ImageSizeReducerCollection} is defined.
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


use \Beluga\ArgumentError;


/**
 * @since v0.1.0
 */
class ImageSizeReducerCollection extends \ArrayObject
{

   
   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new ImageSizeReducerCollection instance.
    *
    * @param array $array Optional initial array
    */
   public function __construct( array $array = [ ] )
   {
      
      parent::__construct( $array );
      
   }

   // </editor-fold>

   
   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   public function __toString()
   {
      
      return $this->toJson();
      
   }

   /**
    * Return the array representation of current collection instance.
    *
    * @return array
    */
   public function toArray() : array
   {
      
      return (array) $this;
      
   }

   /**
    * Gets the json string, representing all collection items.
    *
    * @return string
    */
   public function toJson() : string
   {
      
      return \json_encode( $this->toArray() );
      
   }

   /**
    * Writes the current instance data to defined XMLWriter.
    *
    * @param \XMLWriter $w
    * @param string $elementName
    */
   public function writeXML( \XMLWriter $w, string $elementName = 'ImageSizeReducers' )
   {
      
      if ( ! empty( $elementName ) )
      {
         $w->startElement( $elementName );
      }
      
      foreach ( $this as $reducer )
      {
         if ( \is_null( $reducer ) || ! ( $reducer instanceof ImageSizeReducer ) )
         {
            continue;
         }
         $reducer->writeXML( $w );
      }
      
      if ( ! empty( $elementName ) )
      {
         $w->endElement();
      }
      
   }

   /**
    * @param mixed $index
    * @param \Beluga\Drawing\Image\ImageSizeReducer $newValue
    * @throws \Beluga\ArgumentError
    */
   public function offsetSet( $index, $newValue )
   {

      try
      {
         $this->set( $index, $newValue );
      }
      catch ( \Throwable $ex )
      {
         throw new ArgumentError(
            'newValue',
            $newValue,
            \sprintf('Only values of type %s are allowed!', '\\Beluga\\Drawing\\Image\\ImageSizeReducer' )
         );
      }

   }

   /**
    * @param mixed $index
    * @param \Beluga\Drawing\Image\ImageSizeReducer $newValue
    * @return \Beluga\Drawing\Image\ImageSizeReducerCollection
    */
   public function set( $index, ImageSizeReducer $newValue ) : ImageSizeReducerCollection
   {

      parent::offsetSet( $index, $newValue );

      return $this;

   }

   /**
    * @param int $index
    * @return \Beluga\Drawing\Image\ImageSizeReducer
    */
   public function offsetGet( $index )
   {

      parent::offsetGet( $index );

   }

   /**
    * @param int $index
    * @return \Beluga\Drawing\Image\ImageSizeReducer
    */
   public function get( $index ) : ImageSizeReducer
   {

      return $this->offsetGet( $index );

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * @param \SimpleXMLElement|array|string $value
    * @return \Beluga\Drawing\Image\ImageSizeReducerCollection
    */
   public static function Parse( $value ) : ImageSizeReducerCollection
   {

      $result = new self();

      if ( \is_null( $value ) )
      {
         return $result;
      }

      if ( $value instanceof \SimpleXMLElement )
      {
         if ( ! isset( $value->ImageSizeReducer ) )
         {
            return $result;
         }
         foreach ( $value->ImageSizeReducer as $reducer )
         {
            if ( ! ImageSizeReducer::TryParseXmlElement( $reducer, $r ) )
            {
               continue;
            }
            $result[] = $r;
         }
         return $result;
      }

      if ( \is_string( $value ) && ! empty( $value ) && $value[ 0 ] == '{' )
      {
         try
         {
            $array = \json_decode( $value, true );
            if ( ! \is_array( $array ) )
            {
               return $result;
            }
            $value = $array;
         }
         catch ( \Exception $ex ) { $ex = null; return $result; }
      }

      if ( \is_array( $value ) )
      {
         foreach ( $value as $item )
         {
            if ( ! ImageSizeReducer::TryParse( $item, $r ) )
            {
               continue;
            }
            $result[] = $r;
         }
      }

      return $result;

   }

   // </editor-fold>


}

