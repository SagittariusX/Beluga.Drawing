<?php
/**
 * In this file the class {@see \Beluga\Drawing\Image\GdImage} is defined.
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
use \Beluga\Drawing\{ColorTool,ContentAlign,Gravity,Point,Rectangle,Size};
use \Beluga\IO\{File,FileAccessError,FileNotFoundError,IOError,MimeTypeTool};


/**
 * This class defines an image, handled with PHPs core GD image library.
 *
 * @since v0.1.0
 */
class GdImage extends AbstractImage implements IImage
{


   // <editor-fold desc="// = = = =   P R O T E C T E D   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * @var resource GD-Image Resource
    */
   protected $r;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R I V A T E   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = =">

   private function __construct( $resource, Size $size, string $mimeType, string $file = null )
   {
      $this->size     = $size;
      $this->r        = $resource;
      $this->mimeType = $mimeType;
      $this->_file    = $file;
      $this->colors   = [ ];
      $this->disposed = ! \is_resource( $resource );
   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   D E S T U C T O R   = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =">

   public function __destruct()
   {

      $this->dispose();

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">


   /**
    * Disposes the current image resource.
    */
   public final function dispose()
   {

      if ( $this->disposed )
      {
         return;
      }

      if ( \is_resource( $this->r ) )
      {
         \imagedestroy( $this->r );
      }

      $this->r        = null;
      $this->size     = null;
      $this->colors   = [];
      $this->mimeType = null;
      $this->_file    = null;
      $this->disposed = true;

   }

   /**
    * Gets the current image gd resource.
    *
    * @return resource
    */
   public final function getResource()
   {

      return $this->r;

   }

   /**
    * Returns if the underlying image resource is destroyed/disposed.
    *
    * @return boolean
    */
   public final function isDisposed() : bool
   {

      return $this->disposed || ! \is_resource( $this->r );

   }

   public function __get( string $name )
   {

      switch ( \strtolower( $name ) )
      {
         case 'resource':
         case 'img':
         case 'image':
         case 'imgage':   return $this->r;
         default:         return parent::__get( $name );
      }

   }

   public function __clone()
   {

      if ( $this->isDisposed() )
      {
         return null;
      }

      if ( $this->isTrueColor() )
      {
         $dst = \imagecreatetruecolor( $this->getWidth(), $this->getHeight() );
      }
      else
      {
         $dst = \imagecreate( $this->getWidth(), $this->getHeight() );
      }

      if ( $this->canUseTransparency() )
      {
         \imagealphablending( $dst, false );
         \imagesavealpha( $dst, true );
      }

      \imagecopyresampled(
         $dst,
         $this->r,
         0,
         0,
         0,
         0,
         $this->getWidth(),
         $this->getHeight(),
         $this->getWidth(),
         $this->getHeight()
      );

      $cln = new GdImage(
         $dst,
         new Size( $this->size->getWidth(), $this->size->getHeight() ),
         $this->mimeType,
         $this->getFile()
      );

      return $cln;

   }

   /**
    * Saves the current image to defined image file. If no image file is defined the internally defined image file path
    * is used. If its also not defined a {@see \Beluga\IO\Exception} is thrown.
    *
    * @param  string  $file    Path of image file to save the current image instance. (Must be only defined if the
    *                          instance does not define a path.
    * @param  integer $quality Image quality if it is saved as an JPEG image. (1-100)
    * @throws \Beluga\IO\IOError
    */
   public final function save( string $file = null, int $quality = 75 )
   {

      if ( ! empty( $file ) )
      {
         $this->_file = $file;
      }

      if ( empty( $this->_file ) )
      {
         throw new IOError(
            'Drawing.Image',
            $file,
            'Saving a GdImage instance fails! No file is defined.'
         );
      }

      $fMime = MimeTypeTool::GetByFileName( $this->_file );
      if ( $fMime != $this->mimeType )
      {
         $tmp = \explode( '/', $fMime );
         $ext = $tmp[ 1 ];
         if ( $ext == 'jpeg' )
         {
            $ext = 'jpg';
         }
         $this->_file    = File::ChangeExtension( $this->_file, $ext );
         $this->mimeType = $fMime;
      }

      switch ( $this->mimeType )
      {
         case 'image/png':
            \imagepng( $this->r, $this->_file );
            break;
         case 'image/gif':
            \imagegif( $this->r, $this->_file );
            break;
         default:
            \imagejpeg( $this->r, $this->_file, $quality );
            break;
      }

   }

   /**
    * Outputs the current image, including all required HTTP headers and exit the script.
    *
    * @param  integer $quality  Image quality if it is an JPEG image. (1-100)
    * @param  string  $filename Output image file name for HTTP headers.
    */
   public final function output( int $quality = 60, string $filename = null )
   {

      \header( 'Expires: 0' );
      \header( 'Cache-Control: private' );
      \header( 'Pragma: cache' );

      if ( empty( $filename ) )
      {
         $filename = \basename( $this->_file );
      }
      else
      {
         $filename = \basename( $filename );
      }

      if ( ! empty( $filename ) )
      {
         \header( "Content-Disposition: inline; filename=\"{$filename}\"" );
         $mime = MimeTypeTool::GetByFileName( $filename );
         \header( "Content-Type: {$mime}" );
         switch ( $mime )
         {
            case 'image/png':
               \imagepng( $this->r );
               break;
            case 'image/gif':
               \imagegif( $this->r );
               break;
            default:
               \imagejpeg( $this->r, null, $quality );
               break;
         }
         exit;
      }

      \header( "Content-Type: {$this->mimeType}" );

      switch ( $this->mimeType )
      {
         case 'image/png':
            \imagepng( $this->r );
            break;
         case 'image/gif':
            \imagegif( $this->r );
            break;
         default:
            \imagejpeg( $this->r, null, $quality );
            break;
      }

      exit;

   }


   public final function drawRectangle( Rectangle $rect, $color, $colorName = 'rectangle' )
   {

      $color = $this->getGdColorObject( $color, $colorName );

      imagefilledrectangle(
         $this->r,
         $rect->point->x,
         $rect->point->y,
         $rect->point->x + $rect->size->getWidth(),
         $rect->point->y + $rect->size->getHeight(),
         $color->getGdValue()
      );

   }


   // <editor-fold desc=" - - -   P U B L I C   A C T I O N   M E T H O D S   - - - - - - - - - - - - - - -">


   // <editor-fold desc="// = >   O T H E R   S T U F F">

   /**
    * Rotate the image in 90° steps (90° or a multiple of it)
    *
    * @param  integer                                      $angle     90, 180, 270, -90, -180, -270
    * @param  string|\Beluga\Drawing\Color|\Beluga\Drawing\ColorGD $fillColor Fill collor by need
    * @param  boolean                                      $internal  Dont create a new instance for it?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError                                   If $angle is mot a multiple of 90.
    */
   public final function rotateSquarely( int $angle = 90, $fillColor = null, bool $internal = true ) : IImage
   {

      if ( ( $angle % 90 ) != 0 )
      {
         throw new ArgumentError(
            'angle',
            $angle,
            'Drawing.Image',
            'Only 90° and multiplications of it are allowed values for square image rotations.'
         );
      }

      $fillColor = $this->getGdColorObject( $fillColor, 'rotate_fillcolor' );
      $tmpR      = \imagerotate( $this->r, $angle, $fillColor->getGdValue() );

      if ( $internal )
      {
         \imagedestroy( $this->r );
         $this->r    = $tmpR;
         $this->size = new Size(
            \imagesx( $this->r ),
            \imagesy( $this->r )
         );
         return $this;
      }

      $res = new GdImage(
         $tmpR,
         new Size( \imagesx( $tmpR ), \imagesy( $tmpR ) ),
         $this->mimeType,
         $this->_file
      );

      $res->colors = $this->colors;

      return $res;

   }

   /**
    * Creates a negative of current image.
    *
    * @param  boolean $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\GdImage
    */
   public final function negate( bool $internal = false ) : GdImage
   {

      if ( ! $internal )
      {
         $clon = clone $this;
         return $clon->negate( true );
      }

      $w = $this->getWidth();
      $h = $this->getHeight();

      $im = \imagecreatetruecolor( $w, $h );
      for ( $y = 0; $y < $h; ++$y )
      {
         for ( $x = 0; $x < $w; $x++ )
         {
            $colors = \imagecolorsforindex(
               $this->r,
               \imagecolorat( $this->r, $x, $y )
            );
            $r = 255 - $colors[ 'red' ];
            $g = 255 - $colors[ 'green' ];
            $b = 255 - $colors[ 'blue' ];
            $newColor = \imagecolorallocate( $im, $r, $g, $b );
            \imagesetpixel( $im, $x, $y, $newColor );
         }
      }
      
      \imagedestroy( $this->r );
      $this->r = null;
      
      $this->r = $im;
      
      return $this;
      
   }

   // </editor-fold>


   // <editor-fold desc="// = >   C R O P P I N G">

   /**
    * Crops the defined image part.
    *
    * @param  integer $width The width
    * @param  integer $height The height
    * @param  integer $gravity The gravity. Use one of the constants from {@see \Beluga\Drawing\Gravity}.
    * @param  boolean $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public final function crop( int $width, int $height, int $gravity = Gravity::TOP_LEFT, bool $internal = true )
      : IImage
   {

      // Check if width and height is valid, and regulate the finally used values
      $this->cropCheck( $width, $height );

      if ( $this->hasSameSize( $width, $height ) )
      {
         // No size change needed
         return $internal ? $this : clone $this;
      }

      $rect = null;

      # <editor-fold desc="Rectangle anlegen">
      switch ( $gravity )
      {

         case Gravity::TOP_LEFT:
            $rect = Rectangle::Init( 0, 0, $width, $height );
            break;

         case Gravity::TOP_CENTER:
            $rect = Rectangle::Init(
               (int) \floor( ( $this->getWidth() / 2.0 ) - ( $width / 2.0 ) ),
               0,
               $width,
               $height
            );
            break;

         case Gravity::TOP_RIGHT:
            $rect = Rectangle::Init( $this->getWidth() - $width, 0, $width, $height );
            break;

         case Gravity::MIDDLE_LEFT:
            $rect = Rectangle::Init(
               0,
               (int) \floor( ( $this->getHeight() / 2.0 ) - ( $height / 2.0 ) ),
               $width,
               $height
            );
            break;

         case Gravity::MIDDLE_RIGHT:
            $rect = Rectangle::Init(
               $this->getWidth() - $width,
               (int) \floor( ( $this->getHeight() / 2.0 ) - ( $height / 2.0 ) ),
               $width,
               $height
            );
            break;

         case Gravity::BOTTOM_LEFT:
            $rect = Rectangle::Init(
               0,
               $this->getHeight() - $height,
               $width,
               $height
            );
            break;

         case Gravity::BOTTOM_CENTER:
            $rect = Rectangle::Init(
               (int) \floor( ( $this->getWidth() / 2.0 ) - ( $width / 2.0 ) ),
               $this->getHeight() - $height,
               $width,
               $height
            );
            break;

         case Gravity::BOTTOM_RIGHT:
            $rect = Rectangle::Init(
               $this->getWidth() - $width,
               $this->getHeight() - $height,
               $width,
               $height
            );
            break;

         default:
            #case Gravity::MIDDLE_CENTER:
            $rect = Rectangle::Init(
               (int) \floor( ( $this->getWidth() / 2.0 ) - ( $width / 2.0 ) ),
               (int) \floor( ( $this->getHeight() / 2.0 ) - ( $height / 2.0 ) ),
               $width,
               $height
            );
            break;

      }
      // </editor-fold>

      return $this->cropRect( $rect, $internal );

   }

   /**
    * Crops the defined image part.
    *
    * @param  integer $width
    * @param  integer $height
    * @param  ContentAlign $align
    * @param  boolean $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError If width and/or height is lower than 1
    */
   public final function crop2( int $width, int $height, ContentAlign $align, bool $internal = true )
      : IImage
   {

      $this->cropCheck( $width, $height );

      if ( $this->hasSameSize( $width, $height ) )
      {
         return $internal ? $this : clone $this;
      }

      $rect = null;

      # <editor-fold desc="Rectangle anlegen">
      switch ( $align->value )
      {

         case ContentAlign::TOP_LEFT:
            $rect = Rectangle::Init( 0, 0, $width, $height );
            break;

         case ContentAlign::TOP:
            $rect = Rectangle::Init(
               (int) \floor( ( $this->getWidth() / 2.0 ) - ( $width / 2.0 ) ),
               0,
               $width,
               $height
            );
            break;

         case ContentAlign::TOP_RIGHT:
            $rect = Rectangle::Init(
               $this->getWidth() - $width,
               0,
               $width,
               $height
            );
            break;

         case ContentAlign::MIDDLE_LEFT:
            $rect = Rectangle::Init(
               0,
               (int) \floor( ( $this->getHeight() / 2.0 ) - ( $height / 2.0 ) ),
               $width,
               $height
            );
            break;

         case ContentAlign::MIDDLE_RIGHT:
            $rect = Rectangle::Init(
               $this->getWidth() - $width,
               (int) \floor( ( $this->getHeight() / 2.0 ) - ( $height / 2.0 ) ),
               $width,
               $height
            );
            break;

         case ContentAlign::BOTTOM_LEFT:
            $rect = Rectangle::Init( 0, $this->getHeight() - $height, $width, $height );
            break;

         case ContentAlign::BOTTOM:
            $rect = Rectangle::Init(
               (int) \floor( ( $this->getWidth() / 2.0 ) - ( $width / 2.0 ) ),
               $this->getHeight() - $height,
               $width,
               $height
            );
            break;

         case ContentAlign::BOTTOM_RIGHT:
            $rect = Rectangle::Init(
               $this->getWidth() - $width,
               $this->getHeight() - $height,
               $width,
               $height
            );
            break;

         default:
            #case \GRAVITY_CENTER:
            $rect = Rectangle::Init(
               (int) \floor( ( $this->getWidth() / 2.0 ) - ( $width / 2.0 ) ),
               (int) \floor( ( $this->getHeight() / 2.0 ) - ( $height / 2.0 ) ),
               $width,
               $height
            );
            break;

      }
      // </editor-fold>

      return $this->cropRect( $rect, $internal );

   }

   /**
    * Crop the defined image part and returns the current or new image instance.
    *
    * @param  \Beluga\Drawing\Rectangle $rect
    * @param  boolean               $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public final function cropRect( Rectangle $rect, bool $internal = true )
      : IImage
   {

      $thumb = \imagecreatetruecolor( $rect->size->getWidth(), $rect->size->getHeight() );

      if ( $this->mimeType == 'image/gif' || $this->mimeType == 'image/png')
      {
         \imagealphablending( $thumb, false );
         \imagesavealpha( $thumb, true );
      }

      \imagecopyresampled(
         $thumb,              # $dst_image
         $this->r,            # $src_image
         0,                   # $dst_x
         0,                   # $dst_y
         $rect->point->x,     # $src_x
         $rect->point->y,     # $src_y
         $rect->size->getWidth(),  # $dst_w
         $rect->size->getHeight(), # $dst_h
         $rect->size->getWidth(),  # $src_w
         $rect->size->getHeight()  # $src_h
      );

      if ( $internal )
      {
         $this->size->setHeight( $rect->size->getHeight() );
         $this->size->setWidth ( $rect->size->getWidth() );
         if ( \is_resource( $this->r ) )
         {
            \imagedestroy( $this->r );
            $this->r = null;
         }
         $this->r = $thumb;
         return $this;
      }

      return new GdImage( $thumb, $rect->size, $this->mimeType, $this->_file );

   }

   /**
    * Crop the max usable quadratic image part from declared position and returns the current or new image instance.
    *
    * @param  integer|string $gravity  The gravity. Use one of the constants from {@see \Beluga\Drawing\Gravity}.
    * @param  boolean        $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public final function cropQuadratic( int $gravity = Gravity::MIDDLE_CENTER, bool $internal = true )
      : IImage
   {

      if ( $this->size->isLandscape() )
      {
         return $this->crop( $this->getHeight(), $this->getHeight(), $gravity, $internal );
      }

      return $this->crop( $this->getWidth(), $this->getWidth(), $gravity, $internal );

   }

   /**
    * Crop the max usable quadratic image part from declared position and returns the current or new image instance.
    *
    * @param  ContentAlign $align
    * @param  boolean                  $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public final function cropQuadratic2( ContentAlign $align, bool $internal = true )
      : IImage
   {

      if ( $this->size->isLandscape() )
      {
         return $this->crop2( $this->getHeight(), $this->getHeight(), $align, $internal );
      }

      return $this->crop2( $this->getWidth(), $this->getWidth(), $align, $internal );

   }

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
   public final function contract( int $percent, bool $internal = true )
      : IImage
   {

      if ( $percent < 1 || $percent >= 100 )
      {
         throw new ArgumentError(
            'percent',
            $percent,
            'Drawing.Image',
            'Image dimension contraction must produce a smaller, non zero sized image!'
         );
      }

      $newWidth  = \intval( \floor( $this->getWidth()  * $percent / 100 ) );
      $newHeight = \intval( \floor( $this->getHeight() * $percent / 100 ) );

      if ( $this->isTrueColor() )
      {
         $dst = \imagecreatetruecolor( $newWidth, $newHeight );
      }
      else
      {
         $dst = \imagecreate( $newWidth, $newHeight );
      }

      if ( $this->canUseTransparency() )
      {
         \imagealphablending( $dst, false );
         \imagesavealpha( $dst, true );
      }

      \imagecopyresized( $dst, $this->r, 0, 0, 0, 0, $newWidth, $newHeight, $this->getWidth(), $this->getHeight() );

      if ( $internal )
      {
         if ( ! \is_null( $this->r ) )
         {
            \imagedestroy( $this->r );
            $this->r = null;
         }
         $this->r = $dst;
         $this->size->setWidth( $newWidth );
         $this->size->setHeight( $newHeight );
         return $this;
      }

      return new GdImage(
         $dst,
         new Size( $newWidth, $newHeight ),
         $this->mimeType,
         $this->_file
      );

   }

   /**
    * Reduce the original image size by holding the image size proportion, to fit best the declared
    * maximum size.
    *
    * @param  \Beluga\Drawing\Size $maxsize
    * @param  bool $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError If $maxsize is empty. or bigger than current image
    */
   public final function contractToMaxSize( Size $maxsize, bool $internal = true )
      : IImage
   {

      if ( $maxsize->getWidth()  >= $this->size->getWidth() &&
           $maxsize->getHeight() >= $this->size->getHeight() )
      {
         return $this->returnSelf( $internal );
      }

      $newSize = new Size( $this->size->getWidth(), $this->size->getHeight() );
      $newSize->reduceToMaxSize( $maxsize );

      return $this->createImageAfterResize( $newSize, $internal );

   }

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
   public final function contractToMaxSide( int $maxSideLandscape, int $maxSidePortrait, bool $internal = true )
      : IImage
   {

      $isPortrait = $this->size->isPortrait();

      if ( ( $isPortrait   && ( $this->size->getHeight() <= $maxSidePortrait  ) ) ||
           ( ! $isPortrait && ( $this->size->getWidth()  <= $maxSideLandscape ) ) )
      {
         return $this->returnSelf( $internal );
      }

      $newSize = clone $this->size;
      $newSize->reduceMaxSideTo2( $maxSideLandscape, $maxSidePortrait );

      return $this->createImageAfterResize( $newSize, $internal );

   }

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
   public final function contractToMinSide( int $maxSideLandscape, int $maxSidePortrait, bool $internal = true )
      : IImage
   {

      $isPortrait = $this->size->isPortrait();

      if ( ( $isPortrait   && ( $this->size->getWidth() <= $maxSidePortrait  ) ) ||
           ( ! $isPortrait && ( $this->size->getHeight()  <= $maxSideLandscape ) ) )
      {
         return $this->returnSelf( $internal );
      }

      $newSize = clone $this->size;
      $newSize->reduceMinSideTo2( $maxSideLandscape, $maxSidePortrait );

      return $this->createImageAfterResize( $newSize, $internal );

   }

   // </editor-fold>


   // <editor-fold desc="// = >   P L A C I N G">

   /**
    * Places the defined $placement image inside the current image.
    *
    * @param  \Beluga\Drawing\Image\IImage $placement The image that should be placed
    * @param  Point        $point     The top left point of the placement
    * @param  int                          $opacity   The opacity of the placement in % (1-100)
    * @param  bool                         $internal  Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError
    */
   public final function place( IImage $placement, Point $point, int $opacity = 100, bool $internal = true )
      : IImage
   {

      if ( ! ( $placement instanceof GdImage ) )
      {
         throw new ArgumentError(
            'placement', $placement, 'Drawing.Image',
            'Can not place an image that is not of type GdImage'
         );
      }

      if ( ! $internal )
      {
         $res = clone $this;
         if ( $placement->canUseTransparency() && $opacity < 100 )
         {
            \imagecopymerge(
               $res->r, $placement->r, $point->x, $point->y, 0, 0,
               $placement->getWidth(), $placement->getHeight(), $opacity
            );
         }
         else
         {
            \imagecopy(
               $res->r,
               $placement->r,
               $point->x,
               $point->y,
               0,
               0,
               $placement->getWidth(),
               $placement->getHeight()
            );
         }
         return $res;
      }

      if ( $placement->canUseTransparency() && $opacity < 100 )
      {
         \imagecopymerge(
            $this->r,
            $placement->r,
            $point->x,
            $point->y,
            0,
            0,
            $placement->getWidth(),
            $placement->getHeight(),
            $opacity
         );
      }
      else
      {
         \imagecopy(
            $this->r,
            $placement->r,
            $point->x,
            $point->y,
            0,
            0,
            $placement->getWidth(),
            $placement->getHeight()
         );
      }

      return $this;

   }

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
   public final function placeWithGravity(
      IImage $placement, int $padding, $gravity = Gravity::MIDDLE_CENTER, int $opacity = 100, bool $internal = true )
      : IImage
   {

      # <editor-fold desc="X + Y zuweisen">
      switch ( $gravity )
      {

         case Gravity::TOP_LEFT:
            $x = $padding;
            $y = $padding;
            break;

         case Gravity::TOP_CENTER:
            $x = \intval( \floor( ( $this->getWidth() / 2 ) - ( $placement->getWidth() / 2 ) ) );
            $y = $padding;
            break;

         case Gravity::TOP_RIGHT:
            $x = $this->getWidth() - $placement->getWidth() - $padding;
            $y = $padding;
            break;

         case Gravity::MIDDLE_LEFT:
            $x = $padding;
            $y = \intval( \floor( ( $this->getHeight() / 2 ) - ( $placement->getHeight() / 2 ) ) );
            break;

         case Gravity::MIDDLE_RIGHT:
            $x = $this->getWidth() - $placement->getWidth() - $padding;
            $y = \intval( \floor( ( $this->getHeight() / 2 ) - ( $placement->getHeight() / 2 ) ) );
            break;

         case Gravity::BOTTOM_LEFT:
            $x = $padding;
            $y = $this->getHeight() - $placement->getHeight() - $padding;
            break;

         case Gravity::BOTTOM_CENTER:
            $x = \intval( \floor( ( $this->getWidth() / 2 ) - ( $placement->getWidth() / 2 ) ) );
            $y = $this->getHeight() - $placement->getHeight() - $padding;
            break;

         case Gravity::BOTTOM_RIGHT:
            $x = $this->getWidth() - $placement->getWidth() - $padding;
            $y = $this->getHeight() - $placement->getHeight() - $padding;
            break;

         default:
            # case Gravity::MIDDLE_CENTER:
            $x = \intval( \floor( ( $this->getWidth() / 2 ) - ( $placement->getWidth() / 2 ) ) );
            $y = \intval( \floor( ( $this->getHeight() / 2 ) - ( $placement->getHeight() / 2 ) ) );
            break;

      }
      // </editor-fold>

      return $this->place( $placement, new Point( $x, $y ), $opacity, $internal );

   }

   /**
    * Places the defined $placement image inside the current image.
    *
    * @param  \Beluga\Drawing\Image\IImage $placement The image that should be placed
    * @param  int                          $padding   The min. padding around the placement
    * @param  ContentAlign $align     The placement align inside the current image
    * @param  int                          $opacity   The opacity of the placement in % (1-100)
    * @param  bool                         $internal  Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    * @throws \Beluga\ArgumentError
    */
   public final function placeWithContentAlign(
      IImage $placement, int $padding, ContentAlign $align, int $opacity = 100, bool $internal = true )
      : IImage
   {

      # <editor-fold desc="X + Y">
      switch ( $align->value )
      {

         case ContentAlign::TOP_LEFT:
            $x = $padding;
            $y = $padding;
            break;

         case ContentAlign::TOP:
            $x = \intval( \floor( ( $this->getWidth() / 2 ) - ( $placement->getWidth() / 2 ) ) );
            $y = $padding;
            break;

         case ContentAlign::TOP_RIGHT:
            $x = $this->getWidth() - $placement->getWidth() - $padding;
            $y = $padding;
            break;

         case ContentAlign::MIDDLE_LEFT:
            $x = $padding;
            $y = \intval( \floor( ( $this->getHeight() / 2 ) - ( $placement->getHeight() / 2 ) ) );
            break;

         case ContentAlign::MIDDLE_RIGHT:
            $x = $this->getWidth() - $placement->getWidth() - $padding;
            $y = \intval( \floor( ( $this->getHeight() / 2 ) - ( $placement->getHeight() / 2 ) ) );
            break;

         case ContentAlign::BOTTOM_LEFT:
            $x = $padding;
            $y = $this->getHeight() - $placement->getHeight() - $padding;
            break;

         case ContentAlign::BOTTOM:
            $x = \intval( \floor( ( $this->getWidth() / 2 ) - ( $placement->getWidth() / 2 ) ) );
            $y = $this->getHeight() - $placement->getHeight() - $padding;
            break;

         case ContentAlign::BOTTOM_RIGHT:
            $x = $this->getWidth() - $placement->getWidth() - $padding;
            $y = $this->getHeight() - $placement->getHeight() - $padding;
            break;

         default:
            # case \GRAVITY_CENTER:
            $x = \intval( \floor( ( $this->getWidth() / 2 ) - ( $placement->getWidth() / 2 ) ) );
            $y = \intval( \floor( ( $this->getHeight() / 2 ) - ( $placement->getHeight() / 2 ) ) );
            break;

      }
      // </editor-fold>

      return $this->place( $placement, new Point( $x, $y ), $opacity, $internal );

   }

   // </editor-fold>


   // <editor-fold desc="// = >   B O R D E R I N G">

   /**
    * Draws a single border with defined color around the image.
    *
    * @param  string|array|\Beluga\Drawing\ColorGD|\Beluga\Drawing\Color $borderColor
    * @param  bool                                                       $internal    Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public final function drawSingleBorder( $borderColor, bool $internal = true )
      : IImage
   {

      $borderColor = $this->getGdColorObject( $borderColor, 'bordercolor' );

      if ( $internal )
      {

         \imagerectangle( $this->r, 1, 1, $this->getWidth() - 2, $this->getHeight() - 2, $borderColor->getGdValue() );

         return $this;

      }

      $res = clone $this;
      \imagerectangle( $res->r, 1, 1, $this->getWidth() - 2, $this->getHeight() - 2, $borderColor->getGdValue() );

      return $res;

   }

   /**
    * Draws a double border with defined colors around the image.
    *
    * @param  string|array|\Beluga\Drawing\ColorGD|\Beluga\Drawing\Color $innerBorderColor
    * @param  string|array|\Beluga\Drawing\ColorGD|\Beluga\Drawing\Color $outerBorderColor
    * @param  bool                                                       $internal Do not create a new instance?
    * @return \Beluga\Drawing\Image\IImage
    */
   public final function drawDoubleBorder( $innerBorderColor, $outerBorderColor, bool $internal = true)
      : IImage
   {
      $innerBorderColor = $this->getGdColorObject( $innerBorderColor, 'bordercolor' );
      $outerBorderColor = $this->getGdColorObject( $outerBorderColor, 'outerbordercolor' );
      if ( $internal )
      {
         \imagerectangle( $this->r, 0, 0, $this->getWidth() - 1, $this->getHeight() - 1, $outerBorderColor->getGdValue() );
         \imagerectangle( $this->r, 1, 1, $this->getWidth() - 2, $this->getHeight() - 2, $innerBorderColor->getGdValue() );
         return $this;
      }
      $res = clone $this;
      \imagerectangle( $res->r, 0, 0, $this->getWidth() - 1, $this->getHeight() - 1, $outerBorderColor->getGdValue() );
      \imagerectangle( $res->r, 1, 1, $this->getWidth() - 2, $this->getHeight() - 2, $innerBorderColor->getGdValue() );
      return $res;
   }

   // </editor-fold>


   # <editor-fold desc="// = >   T E X T   D R A W I N G">

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
   public final function drawText(
      string $text, $font, $fontSize, $color, Point $point, bool $internal = true )
      : IImage
   {

      $color = $this->getGdColorObject( $color, 'textcolor' );

      if ( ! empty( $font ) && \file_exists( $font ) )
      {
         if ( $internal )
         {
            \imagettftext( $this->r, $fontSize, 0, $point->x, $point->y, $color->getGdValue(), $font, $text );
            return $this;
         }
         $res = clone $this;
         \imagettftext( $res->r, $fontSize, 0, $point->x, $point->y, $color->getGdValue(), $font, $text );
         return $res;
      }
      
      if ( $internal )
      {
         \imagestring( $this->r, $fontSize, $point->x, $point->y, $text, $color->getGdValue() );
         return $this;
      }
      
      $res = clone $this;
      \imagestring( $res->r, $fontSize, $point->x, $point->y, $text, $color->getGdValue() );
      
      return $res;
      
   }

   /**
    * Draws a text by using {@see \Beluga\Drawing\Gravity} into current image.
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
   public final function drawTextWithGravity(
      string $text, $font, $fontSize, $color, int $padding, int $gravity, bool $internal = true )
      : IImage
   {
      
      if ( ! empty( $font ) && \file_exists( $font ) )
      {
         return $this->drawText(
            $text,
            $font,
            $fontSize,
            $color,
            $this->imageTtfPoint( $fontSize, 0, $font, $text, $gravity, $this->size, $padding ),
            $internal
         );
      }
      
      if ( ! \is_int( $fontSize ) )
      {
         $fontSize = 2;
      }
      
      if ( $fontSize < 1 )
      {
         $fontSize = 1;
      }
      else if ( $fontSize > 4 )
      {
         $fontSize = 4;
      }
      
      $textSize = $this->imageMeasureString( $fontSize, $text );
      #$textSize->setHeight( $textSize->getHeight() - 2 );
      #$textSize->setWidth ( $textSize->getWidth()  + 1 );
      $point = null;
      
      # Get the insertion point by gravity
      switch ( $gravity )
      {

         case Gravity::BOTTOM_LEFT:
            $point = new Point( $padding, $this->getHeight() - $textSize->getHeight() - $padding );
            break;

         case Gravity::BOTTOM_CENTER:
            $point = new Point(
               \intval( \floor( ( $this->getWidth() / 2 ) - ( $textSize->getWidth() / 2 ) ) ),
               $this->getHeight() - $textSize->getHeight() - $padding
            );
            break;

         case Gravity::BOTTOM_RIGHT:
            $point = new Point(
               $this->getWidth() - $textSize->getWidth() - $padding,
               $this->getHeight() - $textSize->getHeight() - $padding
            );
            break;

         case Gravity::MIDDLE_LEFT:
            $point = new Point(
               $padding,
               \intval( \floor( ( $this->getHeight() / 2 ) - ( $textSize->getHeight() / 2 ) ) )
            );
            break;

         case Gravity::MIDDLE_RIGHT:
            $point = new Point(
               $this->getWidth() - $textSize->getWidth() - $padding,
               \intval( \floor( ( $this->getHeight() / 2 ) - ( $textSize->getHeight() / 2 ) ) )
            );
            break;

         case Gravity::TOP_LEFT:
            $point = new Point( $padding, $padding );
            break;

         case Gravity::TOP_CENTER:
            $point = new Point(
               \intval( \floor( ( $this->getWidth() / 2 ) - ( $textSize->getWidth() / 2 ) ) ),
               $padding );
            break;

         case Gravity::TOP_RIGHT:
            $point = new Point( $this->getWidth() - $textSize->getWidth() - $padding, $padding );
            break;

         default:
            #case \GRAVITY_CENTER:
            $point = new Point(
               \intval( \floor( ( $this->getWidth() / 2 ) - ( $textSize->getWidth() / 2 ) ) ),
               \intval( \floor( ( $this->getHeight() / 2 ) - ( $textSize->getHeight() / 2) ) )
            );
            break;

      }

      return $this->drawText( $text, $font, $fontSize, $color, $point, $internal );

   }

   /**
    * Draws a text by using {@see \Beluga\Drawing\Gravity} into current image.
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
   public final function drawShadowedTextWithGravity(
      string $text, $font, $fontSize, $color, $shadowColor, int $padding, int $gravity, bool $internal = true )
   : IImage
   {

      $pointShadow = $this->imageTtfPoint( $fontSize, 0, $font, $text, $gravity, $this->size, $padding );
      $pointText   = clone $pointShadow;
      $pointShadow->x = $pointShadow->x + 1;
      $pointShadow->y = $pointShadow->y + 1;

      if ( ! empty( $font ) && \file_exists( $font ) )
      {
         return $this->drawText(
            $text,
            $font,
            $fontSize,
            $shadowColor,
            $pointShadow,
            $internal
         )->drawText(
            $text,
            $font,
            $fontSize,
            $color,
            $pointText,
            $internal
         );
      }

      if ( ! \is_int( $fontSize ) )
      {
         $fontSize = 2;
      }

      if ( $fontSize < 1 )
      {
         $fontSize = 1;
      }
      else if ( $fontSize > 4 )
      {
         $fontSize = 4;
      }

      $textSize = $this->imageMeasureString( $fontSize, $text );
      #$textSize->setHeight( $textSize->getHeight() - 2 );
      #$textSize->setWidth ( $textSize->getWidth()  + 1 );
      $point = null;

      # Get the insertion point by gravity
      switch ( $gravity )
      {

         case Gravity::BOTTOM_LEFT:
            $pointText = new Point( $padding, $this->getHeight() - $textSize->getHeight() - $padding );
            break;

         case Gravity::BOTTOM_CENTER:
            $pointText = new Point(
               \intval( \floor( ( $this->getWidth() / 2 ) - ( $textSize->getWidth() / 2 ) ) ),
               $this->getHeight() - $textSize->getHeight() - $padding
            );
            break;

         case Gravity::BOTTOM_RIGHT:
            $pointText = new Point(
               $this->getWidth() - $textSize->getWidth() - $padding,
               $this->getHeight() - $textSize->getHeight() - $padding
            );
            break;

         case Gravity::MIDDLE_LEFT:
            $pointText = new Point(
               $padding,
               \intval( \floor( ( $this->getHeight() / 2 ) - ( $textSize->getHeight() / 2 ) ) )
            );
            break;

         case Gravity::MIDDLE_RIGHT:
            $pointText = new Point(
               $this->getWidth() - $textSize->getWidth() - $padding,
               \intval( \floor( ( $this->getHeight() / 2 ) - ( $textSize->getHeight() / 2 ) ) )
            );
            break;

         case Gravity::TOP_LEFT:
            $pointText = new Point( $padding, $padding );
            break;

         case Gravity::TOP_CENTER:
            $pointText = new Point(
               \intval( \floor( ( $this->getWidth() / 2 ) - ( $textSize->getWidth() / 2 ) ) ),
               $padding );
            break;

         case Gravity::TOP_RIGHT:
            $pointText = new Point( $this->getWidth() - $textSize->getWidth() - $padding, $padding );
            break;

         default:
            #case \GRAVITY_CENTER:
            $pointText = new Point(
               \intval( \floor( ( $this->getWidth() / 2 ) - ( $textSize->getWidth() / 2 ) ) ),
               \intval( \floor( ( $this->getHeight() / 2 ) - ( $textSize->getHeight() / 2) ) )
            );
            break;

      }

      $pointShadow = clone $pointText;
      $pointShadow->x = $pointShadow->x + 1;
      $pointShadow->y = $pointShadow->y + 1;

      return $this->drawText( $text, $font, $fontSize, $shadowColor, $pointShadow, $internal )
                  ->drawText( $text, $font, $fontSize, $color      , $pointText  , $internal );

   }

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
   public final function drawTextWithAlign(
      string $text, $font, $fontSize, $color, int $padding, ContentAlign $align, bool $internal = true )
      : IImage
   {

      if ( ! empty( $font ) && \file_exists( $font ) )
      {
         return $this->drawText(
            $text,
            $font,
            $fontSize,
            $color,
            $this->imageTtfPoint2( $fontSize, 0, $font, $text, $align, $this->size, $padding ),
            $internal
         );
      }

      if ( ! \is_int( $fontSize ) )
      {
         $fontSize = 2;
      }

      if ( $fontSize < 1 )
      {
         $fontSize = 1;
      }
      else if ( $fontSize > 4 )
      {
         $fontSize = 4;
      }

      $textSize = $this->imageMeasureString( $fontSize, $text );
      $textSize->setHeight( $textSize->getHeight() - 2 );
      $textSize->setWidth ( $textSize->getWidth()  + 1 );
      $point = null;

      # <editor-fold desc="Ausrichtung">
      switch ( $align->value )
      {

         case ContentAlign::BOTTOM_LEFT:
            $point = new Point( $padding, $this->getHeight() - $textSize->getHeight() - $padding );
            break;

         case ContentAlign::BOTTOM:
            $point = new Point(
               \intval( \floor( ( $this->getWidth() / 2 ) - ( $textSize->getWidth() / 2 ) ) ),
               $this->getHeight() - $textSize->getHeight() - $padding
            );
            break;

         case ContentAlign::BOTTOM_RIGHT:
            $point = new Point(
               $this->getWidth() - $textSize->getWidth() - $padding,
               $this->getHeight() - $textSize->getHeight() - $padding
            );
            break;

         case ContentAlign::MIDDLE_LEFT:
            $point = new Point(
               $padding,
               \intval( \floor( ( $this->getHeight() / 2 ) - ( $textSize->getHeight() / 2 ) ) )
            );
            break;

         case ContentAlign::MIDDLE_RIGHT:
            $point = new Point(
               $this->getWidth() - $textSize->getWidth() - $padding,
               \intval( \floor( ( $this->getHeight() / 2 ) - ( $textSize->getHeight() / 2 ) ) )
            );
            break;

         case ContentAlign::TOP_LEFT:
            $point = new Point( $padding, $padding );
            break;

         case ContentAlign::TOP:
            $point = new Point(
               \intval( \floor( ( $this->getWidth() / 2 ) - ( $textSize->getWidth() / 2 ) ) ),
               $padding
            );
            break;

         case ContentAlign::TOP_RIGHT:
            $point = new Point(
               $this->getWidth() - $textSize->getWidth() - $padding,
               $padding
            );
            break;

         default:
            $point = new Point(
               \intval( \floor( ( $this->getWidth() / 2 ) - ( $textSize->getWidth() / 2 ) ) ),
               \intval( \floor( ( $this->getHeight() / 2 ) - ( $textSize->getHeight() / 2) ) )
            );
            break;

      }
      // </editor-fold>

      return $this->drawText( $text, $font, $fontSize, $color, $point, $internal );

   }

   // </editor-fold>


   // </editor-fold>


   // </editor-fold>


   // <editor-fold desc="// = = = =   P R I V A T E   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * @param  integer|float $size Schriftgroesse
    * @param  string $text Zu vermessende Zeichenkette
    * @return \Beluga\Drawing\Size
    */
   private function imageMeasureString( $size, string $text )
      : Size
   {

      return new Size(
         \imagefontwidth( $size ) * \Beluga\strLen( $text ),
         \imagefontheight( $size )
      );

   }

   /**
    * @param int    $size
    * @param int    $angle
    * @param string $fontFile
    * @param string $text
    * @param int    $gravity
    * @param \Beluga\Drawing\Size $targetSize
    * @param int    $padding
    * @return Point
    */
   private function imageTtfPoint(
      int $size, int $angle, string $fontFile, string $text, $gravity, Size $targetSize, int $padding )
      : Point
   {

      $coords = \imagettfbbox( $size, 0, $fontFile, $text );

      $a  = \deg2rad( $angle );
      $ca = \cos( $a );
      $sa = \sin( $a );
      $b  = array();

      for ( $i = 0; $i < 7; $i += 2 )
      {
         $b[ $i ]     = \round( $coords[ $i ] * $ca + $coords[ $i + 1 ] * $sa );
         $b[ $i + 1 ] = \round( $coords[ $i + 1 ] * $ca - $coords[ $i ] * $sa );
      }

      $x = $b[ 4 ] - $b[ 6 ];
      $y = $b[ 5 ] - $b[ 7 ];
      $width  = \sqrt( \pow( $x, 2 ) + \pow( $y, 2 ) );
      $x = $b[ 0 ] - $b[ 6 ];
      $y = $b[ 1 ] - $b[ 7 ];
      $height = \sqrt( \pow( $x, 2 ) + \pow( $y, 2 ) );

      switch ( $gravity )
      {

         case Gravity::BOTTOM_RIGHT:
            #echo "Gravity::BOTTOM_RIGHT $gravity\n";
            return new Point(
               (int) ( $targetSize->getWidth() - $width - $b[ 0 ] - $padding ),
               (int) ( $targetSize->getHeight() - $height - $b[ 5 ] - 1 - $padding )
            );

         case Gravity::BOTTOM_CENTER:
            #echo "Gravity::BOTTOM_CENTER $gravity\n";
            return new Point(
               (int) ( ( $targetSize->getWidth() / 2 ) - ( $width / 2 ) ),
               (int) ( $targetSize->getHeight() - $height - $b[ 5 ] ) - 1 - $padding
            );

         case Gravity::BOTTOM_LEFT:
            #echo "Gravity::BOTTOM_LEFT $gravity\n";
            return new Point( $padding, (int) $targetSize->getHeight() - $height - $b[ 5 ] - 1 - $padding );

         case Gravity::MIDDLE_LEFT:
            #echo "Gravity::MIDDLE_LEFT: $gravity\n";
            return new Point($padding, (int) ( ( $targetSize->getHeight() / 2 ) - (int) ( $height / 2 )-$b[ 5 ] - 1 ) );

         case Gravity::MIDDLE_RIGHT:
            #echo "Gravity::MIDDLE_RIGHT: $gravity\n";
            return new Point(
               (int) ( $targetSize->getWidth() - $width + $b[ 0 ] - $padding ),
               (int) ( ( $targetSize->getHeight() / 2 ) - (int) ( $height / 2 ) - $b[ 5 ] - 1 )
            );

         case Gravity::TOP_LEFT:
            #echo "Gravity::TOP_LEFT: $gravity\n";
            return new Point( $padding, (int) ( 0 - $b[ 5 ] + $padding ) );

         case Gravity::TOP_CENTER:
            #echo "Gravity::TOP_CENTER: $gravity\n";
            return new Point(
               (int) ( ( $targetSize->getWidth() / 2 ) - (int) ( $width / 2 ) ),
               (int) ( 0 - $b[ 5 ] + $padding )
            );

         case Gravity::TOP_RIGHT:
            #echo "Gravity::TOP_RIGHT: $gravity\n";
            return new Point(
               (int) ( $targetSize->getWidth() - $width + $b[ 0 ] - $padding ),
               (int) ( 0 - $b[ 5 ] + $padding )
            );

         default:
            #case \GRAVITY_CENTER:
            return new Point(
               (int) ( ( $targetSize->getWidth() / 2 ) - (int) ( $width / 2 ) ),
               (int) ( ( $targetSize->getHeight() / 2 ) - (int) ( $height / 2 ) - $b[ 5 ] - 1 )
            );

      }

   }

   private function imageTtfPoint2(
      int $size, int $angle, string $fontFile, string $text, ContentAlign $align,
      Size $targetSize, int $padding )
      : Point
   {

      $coords = \imagettfbbox( $size, 0, $fontFile, $text );
      $a = \deg2rad( $angle );
      $ca = \cos( $a );
      $sa = \sin( $a );
      $b = [ ];

      for ( $i = 0; $i < 7; $i += 2 )
      {
         $b[ $i ]     = \round( $coords[ $i ] * $ca + $coords[ $i + 1 ] * $sa );
         $b[ $i + 1 ] = \round( $coords[ $i + 1 ] * $ca - $coords[ $i ] * $sa );
      }

      $x = $b[ 4 ] - $b[ 6 ];
      $y = $b[ 5 ] - $b[ 7 ];
      $width  = \sqrt( \pow( $x, 2 ) + \pow( $y, 2 ) );
      $x = $b[ 0 ] - $b[ 6 ];
      $y = $b[ 1 ] - $b[ 7 ];
      $height = \sqrt( \pow( $x, 2 ) + \pow( $y, 2 ) );

      switch ( $align->value )
      {

         case ContentAlign::BOTTOM_RIGHT:
            return new Point(
               $targetSize->getWidth() - $width - $b[ 0 ] - $padding,
               $targetSize->getHeight() - $height - $b[ 5 ] - 1 - $padding
            );

         case ContentAlign::BOTTOM:
            return new Point(
               (int) ( ( $targetSize->getWidth() / 2 ) - ( $width / 2 ) ),
               ( $targetSize->getHeight() - $height - $b[ 5 ] ) - 1 - $padding
            );

         case ContentAlign::BOTTOM_LEFT:
            return new Point( $padding, $targetSize->getHeight() - $height - $b[ 5 ] - 1 - $padding );

         case ContentAlign::MIDDLE_LEFT:
            return new Point(
               $padding,
               (int) ( $targetSize->getHeight() / 2 ) - (int) ( $height / 2 )-$b[ 5 ] - 1
            );

         case ContentAlign::MIDDLE_RIGHT:
            return new Point(
               $targetSize->getWidth() - $width + $b[ 0 ] - $padding,
               (int) ( $targetSize->getHeight() / 2 ) - (int) ( $height / 2 ) - $b[ 5 ] - 1
            );

         case ContentAlign::TOP_LEFT:
            return new Point( $padding, 0 - $b[ 5 ] + $padding );

         case ContentAlign::TOP:
            return new Point(
               (int) ( $targetSize->getWidth() / 2 ) - (int) ( $width / 2 ),
               0 - $b[ 5 ] + $padding
            );

         case ContentAlign::TOP_RIGHT:
            return new Point(
               $targetSize->getWidth() - $width + $b[ 0 ] - $padding,
               0 - $b[ 5 ] + $padding
            );

         default:
            return new Point(
               (int) ( $targetSize->getWidth() / 2 ) - (int) ( $width / 2 ),
               (int) ( $targetSize->getHeight() / 2 ) - (int) ( $height / 2 ) - $b[ 5 ] - 1
            );

      }

   }

   private function cropCheck( int &$width, int &$height )
   {

      if ( $width <= 0 )
      {
         throw new ArgumentError(
            'width',
            $width,
            'Drawing.Image',
            'Croping of a image fails.'
         );
      }

      if ( $height <= 0 )
      {
         throw new ArgumentError(
            'height',
            $height,
            'Drawing.Image',
            'Croping of a image fails.'
         );
      }

      if ( $width > $this->size->getWidth() )
      {
         $width = $this->size->getWidth();
      }

      if ( $height > $this->size->getHeight() )
      {
         $height = $this->size->getHeight();
      }

   }

   private function hasSameSize( int $width, int $height )
   {

      return ( $width  == $this->size->getWidth() && $height == $this->size->getHeight() );

   }

   private function createImageAfterResize( Size $newSize, bool $internal )
   {

      if ( $this->isTrueColor() )
      {
         $dst = \imagecreatetruecolor( $newSize->getWidth(), $newSize->getHeight() );
      }
      else
      {
         $dst = \imagecreate( $newSize->getWidth(), $newSize->getHeight() );
      }

      if ( $this->canUseTransparency() )
      {
         imagealphablending( $dst, false );
         imagesavealpha( $dst, true );
      }

      \imagecopyresampled($dst, $this->r, 0, 0,0, 0, $newSize->getWidth(), $newSize->getHeight(), $this->getWidth(), $this->getHeight());

      if ( $internal )
      {
         \imagedestroy( $this->r );
         $this->r            = $dst;
         $this->size->setWidth( $newSize->getWidth() );
         $this->size->setHeight( $newSize->getHeight() );
         return $this;
      }

      return new GdImage(
         $dst,
         $newSize,
         $this->mimeType,
         $this->_file
      );

   }

   private function returnSelf( bool $internal )
   {

      if ( $internal )
      {
         return $this;
      }

      return clone $this;

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Creates a new image with defined dimension and the background color. If the image is a GIF or PNG image
    * tha background color is set to transparent, if $transparent is (bool) TRUE.
    *
    * @param  int          $width       The image width.
    * @param  int          $height      The image height.
    * @param  string|array $backColor   The image background color
    * @param  string       $type        The image mime type (default='image/gif')
    * @param  bool         $transparent Should the GIF or PNG uses a transparent background?
    * @return \Beluga\Drawing\Image\GdImage
    * @throws \Beluga\ArgumentError
    */
   public static function Create(
      int $width, int $height, $backColor, string $type = 'image/gif', bool $transparent = false )
   : GdImage
   {

      $img = \imagecreatetruecolor( $width, $height );

      if ( false === ( $rgb = ColorTool::Color2Rgb( $backColor ) ) )
      {
         $rgb = [ 0, 0, 0 ];
      }

      $bgColor = null;
      $mime    = null;

      switch ( $type )
      {

         case 'image/gif':
            \imagealphablending( $img, false );
            if ( $transparent )
            {
               $bgColor = \imagecolorallocatealpha( $img, $rgb[ 0 ], $rgb[ 1 ], $rgb[ 2 ], 127 );
            }
            else
            {
               $bgColor = \imagecolorallocate( $img, $rgb[ 0 ], $rgb[ 1 ], $rgb[ 2 ] );
            }
            \imagefill( $img, 0, 0, $bgColor );
            $mime = 'image/gif';
            break;

         case 'image/png':
            \imagealphablending( $img, false );
            if ( $transparent )
            {
               $bgColor = \imagecolorallocatealpha( $img, $rgb[ 0 ], $rgb[ 1 ], $rgb[ 2 ], 127 );
            }
            else
            {
               $bgColor = \imagecolorallocate( $img, $rgb[ 0 ], $rgb[ 1 ], $rgb[ 2 ] );
            }
            \imagesavealpha( $img, true );
            \imagefill( $img, 0, 0, $bgColor );
            $mime = 'image/png';
            break;

         default:
            $mime = 'image/jpeg';
            $bgColor = \imagecolorallocate( $img, $rgb[ 0 ], $rgb[ 1 ], $rgb[ 2 ] );
            \imagefill( $img, 0, 0, $bgColor );
            break;

      }

      $result = new GdImage( $img, new Size( $width, $height ), $mime );
      $result->addUserColor( 'background', $bgColor );

      return $result;

   }

   /**
    * Loads the defined image file to a new {@see \Beluga\Drawing\Image\GdImage} instance.
    *
    * @param  string $imageFile
    * @return \Beluga\Drawing\Image\GdImage
    * @throws \Beluga\IO\FileNotFoundError
    * @throws \Beluga\IO\FileAccessError
    */
   public static function LoadFile( string $imageFile ) : GdImage
   {

      if ( ! \file_exists( $imageFile ) )
      {
         throw new FileNotFoundError(
            'Drawing.Image', $imageFile, 'Loading a \Beluga\Drawing\Image\GdImage Resource from this file fails.'
         );
      }

      $imageInfo = null;

      try
      {
         if ( false === ( $imageInfo = \getimagesize( $imageFile ) ) )
         {
            throw new \Exception( 'Defined imagefile uses a unknown file format!' );
         }
      }
      catch ( \Throwable $ex )
      {
         throw new FileAccessError(
            'Drawing.Image', $imageFile, FileAccessError::ACCESS_READ, $ex->getMessage()
         );
      }

      $img  = null;
      $mime = null;

      switch ( $imageInfo[ 'mime' ] )
      {

         case 'image/png':
            $img = \imagecreatefrompng( $imageFile );
            $mime = 'image/png';
            break;

         case 'image/gif':
            $img = \imagecreatefromgif( $imageFile );
            $mime = 'image/gif';
            break;

         default:
            $img = \imagecreatefromjpeg( $imageFile );
            $mime = 'image/jpeg';
            break;

      }

      return new GdImage(
         $img,
         new Size( $imageInfo[ 0 ], $imageInfo[ 1 ] ),
         $mime,
         $imageFile
      );

   }

   // </editor-fold>
   

}

