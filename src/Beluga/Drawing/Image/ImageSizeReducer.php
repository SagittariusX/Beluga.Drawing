<?php
/**
 * In this file the class {@see \Beluga\Drawing\Image\ImageSizeReducer} is defined.
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
use \Beluga\Drawing\{Gravity,Size};
use \Beluga\DynamicProperties\ExplicitGetterSetter;


/**
 * This class defines the Information required to reduce the size of an {@see \Beluga\Drawing\Image\IImage}
 * implementation.
 *
 * @since v0.1.0
 * @property      integer $width     The width (only used for {@see \Beluga\Drawing\Image\ImageSizeReducerType::CROP}
 *                                   and {@see \Beluga\Drawing\Image\ImageSizeReducerType::RESIZE}!)
 * @property      integer $height    The height (only used for {@see \Beluga\Drawing\Image\ImageSizeReducerType::CROP}
 *                                   and {@see \Beluga\Drawing\Image\ImageSizeReducerType::RESIZE}!)
 * @property      integer $landscape Length definition for landscape format images (only used for
 *                                   {@see \Beluga\Drawing\Image\ImageSizeReducerType::LONG_SIDE}
 *                                   and {@see \Beluga\Drawing\Image\ImageSizeReducerType::SHORT_SIDE}!)
 * @property      integer $portrait  Length definition for landscape and quadratic format images (only used for
 *                                   {@see \Beluga\Drawing\Image\ImageSizeReducerType::LONG_SIDE}
 *                                   and {@see \Beluga\Drawing\Image\ImageSizeReducerType::SHORT_SIDE}!)
 * @property-read integer $type      The Size reducer type (See {@see \Beluga\Drawing\Image\ImageSizeReducerType}::* constants)
 * @property      integer $gravity   The gravity if something should be cropped. (See {@see \Beluga\Drawing\Gravity}}::* constants)
 */
class ImageSizeReducer extends ExplicitGetterSetter
{


   // <editor-fold desc="// = = = =   P R I V A T E   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   private $data;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new instance.
    *
    * @param integer $type       The Size reducer type (See {@see \Beluga\Drawing\Image\ImageSizeReducerType}::* constants)
    */
   protected function __construct( int $type )
   {
      $this->data = array(
         'type'      => $type,
         'width'     => 0,
         'height'    => 0,
         'portrait'  => 0,
         'landscape' => 0,
         'gravity'   => Gravity::MIDDLE_CENTER
      );
   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">


   /**
    * @param  \Beluga\Drawing\Image\IImage $img
    * @param  boolean $internal
    * @return \Beluga\Drawing\Image\IImage
    */
   public function run( IImage $img, bool $internal = true ) : IImage
   {

      $imgSize = $img->getSize();

      switch ( $this->data['type'] )
      {

         case ImageSizeReducerType::CROP:
            if ( $this->data[ 'width' ]  > $imgSize->width || $this->data[ 'height' ] > $imgSize->height )
            {
               return $img->contractToMaxSize(
                  new Size( $this->data[ 'width' ], $this->data[ 'height' ] ),
                  $internal
               );
            }
            $widthVal  = $this->data[ 'width'  ] / $imgSize->width;
            $heightVal = $this->data[ 'height' ] / $imgSize->height;
            if ( $widthVal > $heightVal )
            {
               $result = $img->contractToMaxSize(
                  new Size( $this->data[ 'width' ], \intval( $imgSize->height * $widthVal ) ),
                  $internal
               );
               return $result->crop( $this->data[ 'width' ], $this->data[ 'height' ], $this->data[ 'gravity' ] );
            }
            if ( $widthVal < $heightVal )
            {
               $result = $img->contractToMaxSize(
                  new Size( \intval( $imgSize->width * $heightVal ), $this->data[ 'height' ] ),
                  $internal
               );
               return $result->crop(
                  $this->data[ 'width' ],
                  $this->data[ 'height' ],
                  $this->data[ 'gravity' ]
               );
            }
            break;

         case ImageSizeReducerType::RESIZE:
            return $img->contractToMaxSize(
               new Size( $this->data[ 'width' ], $this->data[ 'height' ] ),
               $internal
            );

         case ImageSizeReducerType::LONG_SIDE:
            return $img->contractToMaxSide(
               $this->data[ 'landscape' ],
               $this->data[ 'portrait' ],
               $internal
            );

         case ImageSizeReducerType::SHORT_SIDE:
            return $img->contractToMinSide(
               $this->data[ 'landscape' ],
               $this->data[ 'portrait' ],
               $internal
            );

         default: return $img;

      }

      return $img;

   }

   /**
    * Return all instance options as associative array with the keys: 'type', 'width', 'height',
    * 'portrait', 'landscape', 'gravity'
    *
    * @return array
    */
   public function toArray() : array
   {

      return $this->data;

   }

   /**
    * Writes all instance options to an XML element.
    *
    * @param \XMLWriter $w
    * @param string $elementName
    */
   public function writeXML( \XMLWriter $w, string $elementName = 'ImageSizeReducer' )
   {

      $w->startElement( $elementName );
      $this->writeXMLAttributes( $w );
      $w->endElement();

   }

   /**
    * Writes all instance options to XML attributes inside the opened XML element.
    *
    * @param \XMLWriter $w
    */
   public function writeXMLAttributes( \XMLWriter $w )
   {

      switch ( $this->data[ 'type' ] )
      {

         case ImageSizeReducerType::CROP:
            $w->writeAttribute( 'type',   'crop' );
            $w->writeAttribute( 'width',  $this->data[ 'width' ] );
            $w->writeAttribute( 'height', $this->data[ 'height' ] );
            break;

         case ImageSizeReducerType::RESIZE:
            $w->writeAttribute( 'type',   'resize' );
            $w->writeAttribute( 'width',  $this->data[ 'width' ] );
            $w->writeAttribute( 'height', $this->data[ 'height' ] );
            break;

         case ImageSizeReducerType::LONG_SIDE:
            $w->writeAttribute( 'type',      'long' );
            $w->writeAttribute( 'landscape', $this->data[ 'landscape' ] );
            $w->writeAttribute( 'portrait',  $this->data[ 'portrait' ] );
            break;

         case ImageSizeReducerType::SHORT_SIDE:
            $w->writeAttribute( 'type',      'short' );
            $w->writeAttribute( 'landscape', $this->data[ 'landscape' ] );
            $w->writeAttribute( 'portrait',  $this->data[ 'portrait' ] );
            break;

         default: break;

      }

   }

   /**
    * Gets all instance options as json encoded string.
    *
    * @return string
    */
   public function toJson() : string
   {

      return \json_encode( $this->data );

   }

   /**
    * Implements the type casts to string. It returns the same as toJson().
    *
    * @return string
    */
   public function __toString()
   {

      return $this->toJson();

   }


   // <editor-fold desc="// - - -   G E T T E R   - - - - - - - - - - - - - - - - - - - - - -">

   /**
    * Gets the width (only used for {@see \Beluga\Drawing\Image\ImageSizeReducerType::CROP}
    * and {@see \Beluga\Drawing\Image\ImageSizeReducerType::RESIZE}!)
    *
    * @return int
    */
   public function getWidth() : int
   {

      return $this->data[ 'width' ];

   }

   /**
    * Gets the height (only used for {@see \Beluga\Drawing\Image\ImageSizeReducerType::CROP}
    * and {@see \Beluga\Drawing\Image\ImageSizeReducerType::RESIZE}!)
    *
    * @return int
    */
   public function getHeight() : int
   {

      return $this->data[ 'height' ];

   }

   /**
    * Gets the Length definition for landscape format images (only used for
    * {@see \Beluga\Drawing\Image\ImageSizeReducerType::LONG_SIDE}
    * and {@see \Beluga\Drawing\Image\ImageSizeReducerType::SHORT_SIDE}!)
    *
    * @return int
    */
   public function getLandscape() : int
   {

      return $this->data[ 'landscape' ];

   }

   /**
    * Alias of getLandscape()
    *
    * @return int
    */
   public function getLandscapeLength() : int
   {

      return $this->getLandscape();

   }

   /**
    * Gets the Length definition for portrait and quadratic format images (only used for
    * {@see \Beluga\Drawing\Image\ImageSizeReducerType::LONG_SIDE}
    * and {@see \Beluga\Drawing\Image\ImageSizeReducerType::SHORT_SIDE}!)
    *
    * @return int
    */
   public function getPortrait() : int
   {

      return $this->data[ 'portrait' ];

   }

   /**
    * Alias of getPortrait()
    *
    * @return int
    */
   public function getPortraitLength() : int
   {

      return $this->getPortrait();

   }

   /**
    * Gets the Size reducer type (See {@see \Beluga\Drawing\Image\ImageSizeReducerType}::* constants)
    *
    * @return int
    */
   public function getType() : int
   {

      return $this->data[ 'type' ];

   }

   /**
    * Gets the gravity if something should be cropped. (See {@see \Beluga\Drawing\Gravity}}::* constants)
    *
    * @return int
    */
   public function getGravity() : int
   {

      return $this->data[ 'gravity' ];

   }

   // </editor-fold>


   // <editor-fold desc="// - - -   S E T T E R   - - - - - - - - - - - - - - - - - - - - - -">

   /**
    * Sets the gravity if something should be cropped. (See {@see \Beluga\Drawing\Gravity}}::* constants)
    *
    * @param  int $gravity
    * @return ImageSizeReducer
    * @throws \Beluga\ArgumentError
    */
   public function setGravity( int $gravity ) : ImageSizeReducer
   {

      if ( ! \in_array( $gravity, Gravity::KNOWN_VALUES ) )
      {
         throw new ArgumentError( 'gravity', $gravity, 'Drawing.Image', 'Can not crop an image with an unknown gravity!' );
      }

      $this->data[ 'gravity' ] = $gravity;

      return $this;

   }

   /**
    * Sets the width (only used for {@see \Beluga\Drawing\Image\ImageSizeReducerType::CROP}
    * and {@see \Beluga\Drawing\Image\ImageSizeReducerType::RESIZE}!)
    *
    * @param  int $width
    * @return ImageSizeReducer
    * @throws \Beluga\ArgumentError
    */
   public function setWidth( int $width ) : ImageSizeReducer
   {

      if ( $width <= 0 )
      {
         throw new ArgumentError( 'width', $width, 'Drawing.Image', 'Can not crop an image with an empty width!' );
      }

      $this->data[ 'width' ] = $width;

      return $this;

   }

   /**
    * sets the height (only used for {@see \Beluga\Drawing\Image\ImageSizeReducerType::CROP}
    * and {@see \Beluga\Drawing\Image\ImageSizeReducerType::RESIZE}!)
    *
    * @param  int $height
    * @return ImageSizeReducer
    * @throws \Beluga\ArgumentError
    */
   public function setHeight( int $height ) : ImageSizeReducer
   {

      if ( $height <= 0 )
      {
         throw new ArgumentError( 'height', $height, 'Drawing.Image', 'Can not crop an image with an empty height!' );
      }

      $this->data[ 'height' ] = \intval( $height );

      return $this;

   }

   /**
    * Sets the Length definition for portrait and quadratic format images (only used for
    * {@see \Beluga\Drawing\Image\ImageSizeReducerType::LONG_SIDE}
    * and {@see \Beluga\Drawing\Image\ImageSizeReducerType::SHORT_SIDE}!)
    *
    * @param  int $portraitSideLength
    * @return ImageSizeReducer
    * @throws \Beluga\ArgumentError
    */
   public function setPortrait( int $portraitSideLength ) : ImageSizeReducer
   {

      if ( $portraitSideLength <= 0 )
      {
         throw new ArgumentError(
            'portraitSideLength', $portraitSideLength, 'Drawing.Image',
            'Can not crop an image with an empty side length!' );
      }

      $this->data[ 'portrait' ] = $portraitSideLength;

      return $this;

   }

   /**
    * Alias of setPortrait( $portraitSideLength )
    *
    * @param  int $portraitSideLength
    * @return \Beluga\Drawing\Image\ImageSizeReducer
    * @throws \Beluga\ArgumentError
    */
   public function setPortraitLength( int $portraitSideLength ) : ImageSizeReducer
   {

      return $this->setPortrait( $portraitSideLength );

   }

   /**
    * Sets the Length definition for landscape format images (only used for
    * {@see \Beluga\Drawing\Image\ImageSizeReducerType::LONG_SIDE}
    * and {@see \Beluga\Drawing\Image\ImageSizeReducerType::SHORT_SIDE}!)
    *
    * @param  int $landscapeSideLength
    * @return ImageSizeReducer
    * @throws \Beluga\ArgumentError
    */
   public function setLandscape( int $landscapeSideLength ) : ImageSizeReducer
   {

      if ( $landscapeSideLength <= 0 )
      {
         throw new ArgumentError(
            'landscapeSideLength', $landscapeSideLength, 'Drawing.Image',
            'Can not crop an image with an empty side length!' );
      }

      $this->data[ 'landscape' ] = $landscapeSideLength;

      return $this;

   }

   /**
    * Alias of setLandscape( $landscapeSideLength )
    *
    * @param int $landscapeSideLength
    * @return \Beluga\Drawing\Image\ImageSizeReducer
    * @throws \Beluga\ArgumentError
    */
   public function setLandscapeLength( int $landscapeSideLength ) : ImageSizeReducer
   {

      return $this->setLandscape( $landscapeSideLength );

   }

   // </editor-fold>


   // </editor-fold>


   # <editor-fold desc="= = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = = =">

   /**
    * Init an Image size reducer of type CROP (see {@see \Beluga\Drawing\Image\ImageSizeReducerType::CROP})
    *
    * @param  int $width  The width of the required resulting image
    * @param  int $height The height of the required resulting image
    * @param  int $gravity The gravity of the cropped image part
    * @return \Beluga\Drawing\Image\ImageSizeReducer
    * @throws \Beluga\ArgumentError
    */
   public static function CreateCropper( int $width, int $height, int $gravity = Gravity::MIDDLE_CENTER ) : ImageSizeReducer
   {

      if ( $width <= 0 )
      {
         throw new ArgumentError( 'width', $width, 'Drawing.Image', 'Can not crop an image to a empty width!' );
      }

      if ( $height <= 0 )
      {
         throw new ArgumentError( 'height', $height, 'Drawing.Image', 'Can not crop an image to a empty height!' );
      }

      if ( ! \in_array( $gravity, Gravity::KNOWN_VALUES ) )
      {
         throw new ArgumentError( 'gravity', $gravity, 'Drawing.Image', 'Can not crop an image with an unknown gravity!' );
      }

      return ( new ImageSizeReducer( ImageSizeReducerType::CROP ) )
         ->setWidth( $width )
         ->setHeight( $height );

   }

   /**
    * Init an Image size reducer of type RESIZE (see {@see \Beluga\Drawing\Image\ImageSizeReducerType::RESIZE})
    *
    * @param  int $width  The max. width of the required resulting image.
    * @param  int $height The max. height of the required resulting image.
    * @return \Beluga\Drawing\Image\ImageSizeReducer
    * @throws \Beluga\ArgumentError
    */
   public static function CreateResizer( int $width, int $height ) : ImageSizeReducer
   {

      if ( $width <= 0 )
      {
         throw new ArgumentError( 'width', $width, 'Drawing.Image', 'Can not reduce an image to a empty width!' );
      }

      if ( $height <= 0 )
      {
         throw new ArgumentError( 'height', $height, 'Drawing.Image', 'Can not reduce an image to a empty height!' );
      }

      return ( new ImageSizeReducer( ImageSizeReducerType::RESIZE ) )
         ->setWidth( $width )
         ->setHeight( $height );

   }

   /**
    * Init an Image size reducer of type LONG_SIDE (see {@see \Beluga\Drawing\Image\ImageSizeReducerType::LONG_SIDE})
    *
    * @param  int $portraitLength
    * @param  int $landscapeLength
    * @return \Beluga\Drawing\Image\ImageSizeReducer
    * @throws \Beluga\ArgumentError
    */
   public static function CreateLongSideReducer( int $portraitLength, int $landscapeLength ) : ImageSizeReducer
   {

      if ( $portraitLength <= 0 )
      {
         throw new ArgumentError(
            'portraitLength', $portraitLength, 'Drawing.Image', 'Can not reduce an image to a empty side length!' );
      }

      if ( $landscapeLength <= 0 )
      {
         throw new ArgumentError(
            'landscapeLength', $landscapeLength, 'Drawing.Image', 'Can not reduce an image to a empty side length!' );
      }

      return ( new ImageSizeReducer( ImageSizeReducerType::LONG_SIDE ) )
         ->setLandscape( $landscapeLength )
         ->setPortrait( $portraitLength );

   }

   /**
    * Init an Image size reducer of type SHORT_SIDE (see {@see \Beluga\Drawing\Image\ImageSizeReducerType::SHORT_SIDE})
    *
    * @param  int $portraitLength
    * @param  int $landscapeLength
    * @return \Beluga\Drawing\Image\ImageSizeReducer
    * @throws \Beluga\ArgumentError
    */
   public static function CreateShortSideReducer( int $portraitLength, int $landscapeLength ) : ImageSizeReducer
   {

      if ( $portraitLength <= 0 )
      {
         throw new ArgumentError(
            'portraitLength', $portraitLength, 'Drawing.Image', 'Can not reduce an image to a empty side length!' );
      }

      if ( $landscapeLength <= 0 )
      {
         throw new ArgumentError(
            'landscapeLength', $landscapeLength, 'Drawing.Image', 'Can not reduce an image to a empty side length!' );
      }

      return ( new ImageSizeReducer( ImageSizeReducerType::SHORT_SIDE ) )
         ->setLandscape( $landscapeLength )
         ->setPortrait( $portraitLength );

   }

   /**
    * Init an Image size reducer.
    *
    * @param  int $type   The the image size reducer type
    * @param  int $width  The width of the required resulting image
    * @param  int $height The height of the required resulting image
    * @param  int $gravity The gravity of the cropped image part
    * @param  int $portraitLength
    * @param  int $landscapeLength
    * @return \Beluga\Drawing\Image\ImageSizeReducer
    * @throws \Beluga\ArgumentError
    */
   protected static function Create(
      int $type, int $width, int $height, int $gravity = Gravity::MIDDLE_CENTER,
      int $portraitLength = 0, int $landscapeLength = 0 )
      : ImageSizeReducer
   {

      switch ( $type )
      {

         case ImageSizeReducerType::CROP:
            if ( $width < 1 )
            {
               throw new ArgumentError( 'width', $width, 'Drawing.Image', 'Can not crop an image to a empty width!' );
            }
            if ( $height < 1 )
            {
               throw new ArgumentError( 'height', $height, 'Drawing.Image', 'Can not crop an image to a empty height!' );
            }
            if ( ! \in_array( $gravity, Gravity::KNOWN_VALUES ) )
            {
               $gravity = Gravity::MIDDLE_CENTER;
            }
            return static::CreateCropper( $width, $height, $gravity );

         case ImageSizeReducerType::RESIZE:
            if ( $width < 1 )
            {
               throw new ArgumentError( 'width', $width, 'Drawing.Image', 'Can not reduce an image to a empty width!' );
            }
            if ( $height < 1 )
            {
               throw new ArgumentError( 'height', $height, 'Drawing.Image', 'Can not reduce an image to a empty height!' );
            }
            return static::CreateResizer( $width, $height );

         case ImageSizeReducerType::LONG_SIDE:
            if ( $portraitLength < 1 )
            {
               throw new ArgumentError(
                  'portraitLength', $portraitLength, 'Drawing.Image',
                  'Can not reduce an image size to a empty side length!' );
            }
            if ( $landscapeLength < 1 )
            {
               throw new ArgumentError(
                  'landscapeLength', $landscapeLength, 'Drawing.Image',
                  'Can not reduce an image size to a empty side length!' );
            }
            return static::CreateLongSideReducer( $portraitLength, $landscapeLength );

         case ImageSizeReducerType::SHORT_SIDE:
            if ( $portraitLength < 1 )
            {
               throw new ArgumentError(
                  'portraitLength', $portraitLength, 'Drawing.Image',
                  'Can not reduce an image size to a empty side length!' );
            }
            if ( $landscapeLength < 1 )
            {
               throw new ArgumentError(
                  'landscapeLength', $landscapeLength, 'Drawing.Image',
                  'Can not reduce an image size to a empty side length!' );
            }
            return static::CreateShortSideReducer( $portraitLength, $landscapeLength );

         default:
            throw new ArgumentError(
               'type', $type, 'Drawing.Image',
               'Can not reduce an image size with a unknown reducer type!' );

      }

   }

   /**
    * Tries to parse the defined \SimpleXMLElement to an ImageSizeReducer instance
    *
    * @param  \SimpleXMLElement                      $element
    * @param  \Beluga\Drawing\Image\ImageSizeReducer $refReducer Returns the resulting reducer instance
    *                                                            if the method return TRUE
    * @return bool
    */
   public static function TryParseXmlElement( \SimpleXMLElement $element, &$refReducer = null ) : bool
   {

      if ( ! isset( $value[ 'type' ] ) )
      {
         return false;
      }

      $type = \strtolower( (string) $element[ 'type' ] );

      switch ( $type )
      {

         case 'crop':
         case \strval( ImageSizeReducerType::CROP ):
            if ( ! isset( $element[ 'width' ] ) || ! isset( $element[ 'height' ] ) )
            {
               return false;
            }
            $w = \intval( (string) $element[ 'width' ] );
            $h = \intval( (string) $element[ 'height' ] );
            $g = isset( $element[ 'gravity' ] ) ? \intval( (string) $element[ 'gravity' ] ) : Gravity::MIDDLE_CENTER;
            if ( ! \in_array( $g, Gravity::KNOWN_VALUES ) )
            {
               $g = Gravity::MIDDLE_CENTER;
            }
            if ( $w < 1 || $h < 1 )
            {
               return false;
            }
            $refReducer = static::CreateCropper( $w, $h, $g );
            return true;

         case 'resize':
         case \strval( ImageSizeReducerType::RESIZE ):
            if ( ! isset( $element[ 'width' ] ) || ! isset( $element[ 'height' ] ) )
            {
               return false;
            }
            $w = \intval( (string) $element[ 'width' ] );
            $h = \intval( (string) $element[ 'height' ] );
            if ( $w < 1 || $h < 1 )
            {
               return false;
            }
            $refReducer = static::CreateResizer( $w, $h );
            return true;

         case 'long':
         case \strval( ImageSizeReducerType::LONG_SIDE ):
            if ( ! isset( $value[ 'landscape' ] ) || ! isset( $value[ 'portrait' ] ) )
            {
               return false;
            }
            $l = \intval( (string) $value[ 'landscape' ] );
            $p = \intval( (string) $value[ 'portrait' ] );
            if ( $l < 1 || $p < 1 )
            {
               return false;
            }
            $refReducer = static::CreateLongSideReducer( $p, $l );
            return true;

         case 'short':
         case \strval( ImageSizeReducerType::SHORT_SIDE ):
            if ( ! isset( $value[ 'landscape' ] ) || ! isset( $value[ 'portrait' ] ) )
            {
               return false;
            }
            $l = \intval( (string) $value[ 'landscape' ] );
            $p = \intval( (string) $value[ 'portrait' ] );
            if ( $l < 1 || $p < 1 )
            {
               return false;
            }
            $refReducer = static::CreateShortSideReducer( $p, $l );
            return true;

         default:
            return false;

      }

   }

   /**
    * Tries to parse the defined value to an ImageSizeReducer instance
    *
    * @param \SimpleXMLElement|array|string         $value
    * @param \Beluga\Drawing\Image\ImageSizeReducer $refReducer Returns the resulting reducer instance
    *                                                            if the method return TRUE
    * @return bool
    */
   public static function TryParse( $value, &$refReducer = null ) : bool
   {

      if ( \is_null( $value ) )
      {
         return false;
      }

      if ( $value instanceof \SimpleXMLElement )
      {

         return static::TryParseXmlElement( $value, $refReducer );

      }

      if ( \is_string( $value ) && ! empty( $value ) && $value[ 0 ] == '{' )
      {
         try
         {
            $array = \json_decode( $value, true );
            if ( ! \is_array( $array ) )
            {
               return false;
            }
            $value = $array;
         }
         catch ( \Throwable $ex ) { $ex = null; return false; }
      }

      if ( \is_array( $value ) )
      {

         if ( ! isset( $value[ 'type' ] ) )
         {
            return false;
         }

         if ( ! isset( $value[ 'width' ] ) )
         {
            $value[ 'width' ] = 0;
         }

         if ( ! isset( $value[ 'height' ] ) )
         {
            $value[ 'height' ] = 0;
         }

         if ( ! isset( $value[ 'landscape' ] ) )
         {
            $value[ 'landscape' ] = 0;
         }

         if ( ! isset( $value[ 'portrait' ] ) )
         {
            $value[ 'portrait' ] = 0;
         }

         if ( ! isset( $value[ 'gravity' ] ) )
         {
            $value[ 'gravity' ] = Gravity::MIDDLE_CENTER;
         }

         $refReducer = static::Create(
            \intval( $value[ 'type' ] ),
            \intval( $value[ 'width' ] ),
            \intval( $value[ 'height' ] ),
            \intval( $value[ 'gravity' ] ),
            \intval( $value[ 'portrait' ] ),
            \intval( $value[ 'landscape' ] )
         );

         return true;

      }

      return false;

   }

   // </editor-fold>


}

