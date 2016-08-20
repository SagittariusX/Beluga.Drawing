<?php
/**
 * In this file the class {@see \Beluga\Drawing\Image\AbstractImage} is defined.
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


use \Beluga\{BelugaError,ArgumentError};
use \Beluga\Drawing\{Color,ColorGD,Size};
use \Beluga\DynamicProperties\ExplicitGetter;


/**
 * This abstract class defined a object usable a a base image class.
 * It defines the core code of a {@see \Beluga\Drawing\Image\IIMage} interface.
 *
 * @since         v0.1.0
 * @property-read integer               $width
 * @property-read integer               $height
 * @property-read \Beluga\Drawing\ColorGD[] $usercolors
 * @property-read string                $file
 */
abstract class AbstractImage extends ExplicitGetter
{


   // <editor-fold desc="// = = = =   P U B L I C   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The image size
    *
    * @var \Beluga\Drawing\Size
    */
   public $size;

   /**
    * @var string
    */
   public $mimeType;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Is the current instance disposed?
    *
    * @var boolean
    */
   protected $disposed = false;

   /**
    * All user defined colors
    *
    * @var array
    */
   protected $colors = [];

   /**
    * @var string
    */
   protected $_file = null;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">


   // <editor-fold desc="// - - -   G E T T E R   - - - - - - - - - - - - - - - - - - - - - -">

   /**
    * Returns the current image width.
    *
    * @return integer
    */
   public final function getWidth() : int
   {

      return $this->disposed ? 0 : $this->size->getWidth();

   }

   /**
    * Returns the current image height.
    *
    * @return integer
    */
   public final function getHeight() : int
   {

      return $this->disposed ? 0 : $this->size->getHeight();

   }

   /**
    * Returns the current image size.
    *
    * @return \Beluga\Drawing\Size
    */
   public final function getSize() : Size
   {

      return $this->disposed ? Size::Empty( true ) : $this->size;

   }

   /**
    * Returns the current image mime type.
    *
    * @return string
    */
   public final function getMimeType() : string
   {

      return $this->disposed ? '' : $this->mimeType;

   }

   /**
    * Returns all currently user defined colors for this image as array with colornames (keys) + colors as
    * {@see \Beluga\Drawing\ColorGD} objects.
    *
    * @return \Beluga\Drawing\ColorGD[] Array of {@see \Beluga\Drawing\ColorGD}
    */
   public final function getUserColors() : array
   {

      return $this->disposed ? [ ] : $this->colors;

   }

   /**
    * Returns the user defined color with defined name.
    *
    * @param  string $name The user defined color name.
    * @return \Beluga\Drawing\ColorGD
    * @throws \Beluga\ArgumentError
    */
   public final function getUserColor( string $name )
   {

      if ( $this->disposed )
      {
         return null;
      }

      if ( ! isset( $this->colors[ $name ] ) )
      {
         throw new ArgumentError(
            '$name',
            $name,
            'Drawing.Image',
            'There is no user color with this name defined for this image!'
         );
      }

      return $this->colors[ $name ];

   }

   /**
    * Returns the current image file path, if defined.
    *
    * @return string
    */
   public final function getFile() : string
   {

      return $this->disposed ? '' : $this->_file;

   }

   // </editor-fold>


   /**
    * Returns, if the current image is a true color image.
    *
    * @return boolean
    */
   public final function isTrueColor() : bool
   {

      return $this->disposed ? false : ( $this->mimeType == 'image/jpeg' ||  $this->mimeType == 'image/png' );

   }

   /**
    * Returns, if the current image can use some transparency.
    *
    * @return boolean
    */
   public final function canUseTransparency() : bool
   {

      return $this->disposed ? false : ( $this->mimeType == 'image/gif' ||  $this->mimeType == 'image/png' );

   }

   /**
    * Returns if the current image resource is destroyed yet.
    *
    * @return boolean
    */
   public function isDisposed() : bool
   {

      return $this->disposed;

   }

   /**
    * Adds a new color to image, for usage in combination with this image and returns the resulting
    * {@see \Beluga\Drawing\ColorGD} instance.
    *
    * @param  string $name The user defined color name
    * @param  string|RGB-Array|integer|\Beluga\Drawing\ColorGD|\Beluga\Drawing\Color $colorDefinition
    * @param  int $opacity The opacity of the color (0-100) in percent
    * @return \Beluga\Drawing\ColorGD|bool
    * @throws \Beluga\BelugaError If the instance is already disposed.
    * @throws \Beluga\ArgumentError
    */
   public final function addUserColor( string $name, $colorDefinition, int $opacity = 100 )
   {

      if ( $this->isDisposed() )
      {
         throw new BelugaError(
            'Drawing.Image',
            'This image instance is not usable cause its already disposed (destroyed)!'
         );
      }

      if ( \is_object( $colorDefinition ) )
      {

         if ( $colorDefinition instanceof ColorGD )
         {
            $this->colors[ $name ] = $colorDefinition;
         }
         else if ( $colorDefinition instanceof Color )
         {
            $this->colors[ $name ] = new ColorGD(
               $colorDefinition->createGdValue(),
               $colorDefinition->getOpacity()
            );
         }
         else
         {
            throw new ArgumentError(
               '$colorDefinition',
               $colorDefinition,
               'Drawing.Image',
               'Illegal color definition for usage inside a \Beluga\Drawing\Image\GdImage!'
            );
         }

         return $this->colors[ $name ];

      }

      $tmp = new ColorGD( $colorDefinition, $opacity );
      $this->colors[ $name ] = $tmp;

      return $this->colors[ $name ];

   }

   /**
    * Gets if the image is of type PNG.
    *
    * @return bool
    */
   public final function isPng() : bool
   {

      return $this->mimeType == 'image/png';

   }

   /**
    * Gets if the image is of type GIF.
    *
    * @return bool
    */
   public final function isGif() : bool
   {

      return $this->mimeType == 'image/gif';

   }

   /**
    * Gets if the image is of type JPEG.
    *
    * @return bool
    */
   public final function isJpeg() : bool
   {

      return $this->mimeType == 'image/jpeg';

   }

   /**
    * Returns if an file is associated to the image
    *
    * @return bool
    */
   public final function hasAssociatedFile() : bool
   {
      return ! empty( $this->_file );
   }


   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * @param  string|\Beluga\Drawing\ColorGD|\Beluga\Drawing\Color $color
    * @param  string $name
    * @return \Beluga\Drawing\ColorGD
    */
   protected function getGdColorObject( $color, string $name )
   {

      if ( $color instanceof ColorGD )
      {
         return $color;
      }

      if ( $color instanceof Color )
      {
         return $this->addUserColor( $name, $color );
      }

      if ( \is_string( $color ) )
      {

         if ( isset( $this->colors[ $color ] ) )
         {
            return $this->colors[ $color ];
         }

         try
         {
            $color = $this->addUserColor( $name, $color );
            if ( ! ( $color instanceof ColorGD ) )
            {
               throw new \Exception();
            }
            return $color;
         }
         catch ( \Throwable $ex )
         {
            $ex = null;
            if ( isset( $this->colors[ $name ] ) )
            {
               return $this->colors[ $name ];
            }
            if ( \count( $this->colors ) > 0 )
            {
               $keys = \array_keys( $this->colors );
               return $this->colors[ $keys[ 0 ] ];
            }
            return $this->addUserColor( $name, 'black' );
         }

      }

      if ( isset( $this->colors[ $name ] ) )
      {
         return $this->colors[ $name ];
      }

      if ( \count( $this->colors ) > 0 )
      {
         $keys = \array_keys( $this->colors );
         return $this->colors[ $keys[ 0 ] ];
      }

      return $this->addUserColor( $name, 'black' );

   }

   // </editor-fold>


}

