<?php
/**
 * In this file the class {@see \Beluga\Drawing\Color} is defined.
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


use \Beluga\ArgumentError;


/**
 * Defines all information of a color. (R, G, B, Opacity/Alpha, GD).
 *
 * @since    v0.1
 * @property integer $r     The red color part.
 * @property integer $red   Alias of 'r'
 * @property integer $g     The green color part.
 * @property integer $green Alias of 'g'
 * @property integer $b     The blue color part.
 * @property integer $blue  Alias alias 'b'
 * @property string  $hex   Hexadecimal color definition #rrggbb
 * @property array   $rgb   Array numeric [ r, g, b ]
 */
class Color
{


   // <editor-fold desc="// = = = =   P R O T E C T E D   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Array with keys 'red', 'green', 'blue', 'hex', 'opacity', 'alpha'
    *
    * @var array
    */
   protected $data;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new instance.
    *
    * @param string|int|array $colorDefinition Color
    * @param integer          $opacity         The color opacity in percent 0-100 (default=null)
    */
   public function __construct( $colorDefinition = '#ffffff', int $opacity = null )
   {

      if ( false === ( $rgb = ColorTool::Color2Rgb( $colorDefinition ) ) )
      {
         $rgb = array( 0, 0, 0 );
      }

      $this->data = [ 'red' => $rgb[ 0 ], 'green' => $rgb[ 1 ], 'blue' => $rgb[ 2 ] ];

      if ( \is_null( $opacity ) )
      {
         $this->data[ 'opacity' ] = null;
      }
      else if ( $opacity <= 0 )
      {
         $this->data[ 'opacity' ] = 0;
      }
      else if ( $opacity >= 100 )
      {
         $this->data[ 'opacity' ] = 100;
      }
      else
      {
         $this->data[ 'opacity' ] = $opacity;
      }

      $this->data[ 'hex' ] = ColorTool::Rgb2Hex(
         $this->data[ 'red' ],
         $this->data[ 'green' ],
         $this->data[ 'blue' ]
      );

      if ( \is_null( $opacity ) )
      {
         $this->data[ 'alpha' ] = null;
      }
      else if ( $this->data[ 'opacity' ] == 100 )
      {
         $this->data[ 'alpha' ] = 0;
      }
      else if ( $this->data[ 'opacity' ] == 0 )
      {
         $this->data[ 'alpha' ] = 127;
      }
      else
      {
         $this->data[ 'alpha' ] = ( 127 * ( 100 - $this->data[ 'opacity' ] ) ) / 100;
      }

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">


   // <editor-fold desc="// - - -   G E T T E R   - - - - - - - - - - - - - - - - - - - - - -">

   /**
    * Gets the RGB values as numeric indicated array (3 elements)
    *
    * @return array 0=Red 1=Green 2=Blue
    */
   public final function getRGB() : array
   {

      return [
         $this->data[ 'red' ],
         $this->data[ 'green' ],
         $this->data[ 'blue' ]
      ];

   }

   /**
    * Gets the alpha channel value and the RGB values as numeric indicated array (4 elements)
    *
    * @return array 0=Alpha 1=Red 2=Green 3=Blue
    */
   public final function getARGB() : array
   {

      return [
         $this->data[ 'alpha' ],
         $this->data[ 'red' ],
         $this->data[ 'green' ],
         $this->data[ 'blue' ]
      ];

   }

   /**
    * Gets the alpha channel value and the RGB values as numeric indicated array (4 elements)
    *
    * @return array 0=Red 1=Green 2=Blue 3=Alpha
    */
   public final function getRGBA() : array
   {

      return [
         $this->data[ 'red' ],
         $this->data[ 'green' ],
         $this->data[ 'blue' ],
         $this->data[ 'alpha' ]
      ];

   }

   /**
    * Returns the red RGB color part.
    *
    * @return integer
    */
   public final function getR() : int
   {

      return $this->data[ 'red' ];

   }

   /**
    * Returns the green RGB color part.
    *
    * @return integer
    */
   public final function getG() : int
   {

      return $this->data[ 'green' ];

   }

   /**
    * Returns the blue RGB color part.
    *
    * @return mixed
    */
   public final function getB() : int
   {

      return $this->data[ 'blue' ];

   }

   /**
    * @return integer
    */
   public final function getAlpha() : int
   {

      return \is_null( $this->data[ 'alpha' ] ) ? 0 : $this->data[ 'alpha' ];

   }

   /**
    * @return integer
    */
   public final function getOpacity() : int
   {

      return \is_null( $this->data[ 'opacity' ] ) ? 100 : $this->data[ 'opacity' ];

   }

   /**
    * @return string
    */
   public final function getHexadecimal() : string
   {

      return $this->data[ 'hex' ];

   }

   /**
    * @param $name
    * @return array|bool|int|mixed|string
    */
   public function __get( $name )
   {

      return $this->get( $name );

   }

   /**
    * @param $name
    * @return array|bool|int|mixed|string
    */
   protected function get( string $name )
   {

      switch ( \strtolower( $name ) )
      {
         case 'r': case 'red':      return $this->getR();
         case 'g': case 'green':    return $this->getG();
         case 'b': case 'blue':     return $this->getB();
         case 'alpha': case 'a':    return $this->getAlpha();
         case 'opacity': case 'o':  return $this->getOpacity();
         case 'rgb':                return $this->getRGB();
         case 'rgba':               return $this->getRGBA();
         case 'argb':               return $this->getARGB();
         case 'hex': case 'h': case 'hexadecimal':
                                    return $this->getHexadecimal();
         default:                   return false;
      }

   }

   // </editor-fold>


   // <editor-fold desc="// - - -   S E T T E R   - - - - - - - - - - - - - - - - - - - - - -">

   /**
    * Sets a new color, defined by a valid RGB value.
    *
    * If a string is used the following formats are valid: 'rgb(r,g,b)' or 'r,g,b'
    *
    * Or a array (0=r,1=g,2=b) or associative with keys 'r'|'red', 'g'|'green', 'b'|'blue'
    *
    * @param  string|array $rgbValue
    * @return \Beluga\Drawing\Color
    * @throws \Beluga\ArgumentError
    */
   public function setRGB( $rgbValue ) : Color
   {

      if ( \is_string( $rgbValue ) )
      {
         $this->setRGBString( $rgbValue );
      }
      else if ( \is_array( $rgbValue ) && \count( $rgbValue ) > 2 )
      {
         $this->setRGBArray( $rgbValue );
      }
      else
      {
         throw new ArgumentError(
            'rgbValue',
            $rgbValue,
            'Drawing',
            'Illegal RGB-Value Format!'
         );
      }

      return $this;

   }

   /**
    * @param  string|array $argbValue
    * @return \Beluga\Drawing\Color
    * @throws \Beluga\ArgumentError
    */
   public function setARGB( $argbValue ) : Color
   {

      if ( \is_string( $argbValue ) )
      {
         $this->setARGBString( $argbValue );
         return $this;
      }

      if ( \is_array( $argbValue ) && \count( $argbValue ) > 2 )
      {
         $this->setARGBArray( $argbValue );
         return $this;
      }

      throw new ArgumentError(
         'argbValue',
         $argbValue,
         'Drawing',
         'Illegal ARGB-Value Format!'
      );

   }

   /**
    * @param  string|array $rgbaValue
    * @return \Beluga\Drawing\Color
    * @throws \Beluga\ArgumentError
    */
   public function setRGBA( $rgbaValue ) : Color
   {

      if ( \is_string( $rgbaValue ) )
      {
         $this->setRGBAString( $rgbaValue );
         return $this;
      }

      if ( \is_array( $rgbaValue ) && \count( $rgbaValue ) > 2 )
      {
         $this->setRGBAArray( $rgbaValue );
         return $this;
      }

      throw new ArgumentError(
         'rgbaValue',
         $rgbaValue,
         'Drawing',
         'Illegal RGBA-Value Format!'
      );

   }

   /**
    * @param  integer $redValue
    * @return \Beluga\Drawing\Color
    * @throws \Beluga\ArgumentError
    */
   public function setR( int $redValue ) : Color
   {

      if ( $redValue < 0 || $redValue > 255 )
      {
         throw new ArgumentError(
            'redValue',
            $redValue,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      $this->data[ 'red' ] = $redValue;

      $this->data[ 'hex' ] = ColorTool::Rgb2Hex(
         $this->data[ 'red' ],
         $this->data[ 'green' ],
         $this->data[ 'blue' ]
      );

      return $this;

   }

   /**
    * @param  integer $greenValue
    * @return \Beluga\Drawing\Color
    * @throws \Beluga\ArgumentError
    */
   public function setG( int $greenValue ) : Color
   {

      if ( $greenValue < 0 || $greenValue > 255 )
      {
         throw new ArgumentError(
            'greenValue',
            $greenValue,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      $this->data[ 'green' ] = $greenValue;

      $this->data[ 'hex' ] = ColorTool::Rgb2Hex(
         $this->data[ 'red' ],
         $this->data[ 'green' ],
         $this->data[ 'blue' ]
      );

      return $this;

   }

   /**
    * @param  integer $blueValue
    * @return \Beluga\Drawing\Color
    * @throws \Beluga\ArgumentError
    */
   public function setB( int $blueValue ) : Color
   {

      if ( $blueValue < 0 || $blueValue > 255 )
      {
         throw new ArgumentError(
            'blueValue',
            $blueValue,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      $this->data[ 'blue' ] = $blueValue;

      $this->data[ 'hex' ]  = ColorTool::Rgb2Hex(
         $this->data[ 'red' ],
         $this->data[ 'green' ],
         $this->data[ 'blue' ]
      );

      return $this;

   }

   /**
    * @param string $colorDefinition
    * @return \Beluga\Drawing\Color
    */
   public function setWebColor( $colorDefinition ) : Color
   {

      $rgb = ColorTool::Color2Rgb( $colorDefinition );

      $this->data[ 'red' ]     = $rgb[ 0 ];
      $this->data[ 'green' ]   = $rgb[ 1 ];
      $this->data[ 'blue' ]    = $rgb[ 2 ];
      $this->data[ 'alpha' ]   = 0;
      $this->data[ 'opacity' ] = 100;
      $this->data[ 'hex' ]     = ColorTool::Rgb2Hex(
         $this->data[ 'red' ],
         $this->data[ 'green' ],
         $this->data[ 'blue' ]
      );

      return $this;

   }

   /**
    * @param  integer $value
    * @return \Beluga\Drawing\Color
    * @throws ArgumentError
    */
   public function setAlpha( int $value = null ) : Color
   {

      if ( \is_null( $value ) )
      {
         $this->data[ 'alpha' ]   = null;
         $this->data[ 'opacity' ] = null;
         return $this;
      }

      if ( $value < 0 || $value > 127 )
      {
         throw new ArgumentError(
            'value',
            $value,
            'Drawing',
            'Illegal alpha value outside the allowed range 0-127.'
         );
      }

      $this->data[ 'alpha' ] = $value;

      if ( $this->data[ 'alpha' ] == 0 )
      {
         $this->data[ 'opacity' ] = 100;
      }
      else if ( $this->data[ 'alpha' ] == 127 )
      {
         $this->data[ 'opacity' ] = 0;
      }
      else
      {
         $this->data[ 'opacity' ] = \intval( \floor( ( $this->data[ 'alpha' ] * 100 ) / 127 ) );
      }

      return $this;

   }

   /**
    * @param  integer $value
    * @return \Beluga\Drawing\Color
    * @throws ArgumentError
    */
   public function setOpacity( int $value = null ) : Color
   {

      if ( \is_null( $value ) )
      {
         $this->data[ 'alpha' ] = null;
         $this->data[ 'opacity' ] = null;
         return $this;
      }

      if ( $value < 0 || $value > 100 )
      {
         throw new ArgumentError(
            'value',
            $value,
            'Drawing',
            'Illegal opacity value outside the allowed range 0-100.'
         );
      }

      $this->data[ 'opacity' ] = $value;

      if ( $this->data[ 'opacity' ] == 100 )
      {
         $this->data[ 'alpha' ] = 0;
      }
      else if ( $this->data[ 'opacity' ] == 0 )
      {
         $this->data[ 'alpha' ] = 127;
      }
      else
      {
         $this->data[ 'alpha' ] = ( 127 * ( 100 - $this->data[ 'opacity' ] ) ) / 100;
      }

      return $this;

   }

   /**
    * @param $name
    * @param $value
    */
   public function __set( $name, $value )
   {

      $this->set( $name, $value );

   }

   /**
    * @param $name
    * @param $value
    */
   protected function set( string $name, $value )
   {

      switch ( \strtolower( $name ) )
      {

         case 'rgb':
            $this->setRGB( $value );
            break;

         case 'argb':
            $this->setARGB( $value );
            break;

         case 'rgba':
            $this->setRGBA( $value );
            break;

         case 'r':
         case 'red':
            $this->setR( $value );
            break;

         case 'g':
         case 'green':
            $this->setG( $value );
            break;

         case 'b':
         case 'blue':
            $this->setB( $value );
            break;

         case 'hex':
         case 'hexadecimal':
         case 'value':
         case 'webcolor':
            $this->setWebColor( $value );
            break;

         case 'alpha':
         case 'a':
            $this->setAlpha( $value );
            break;

         case 'o':
         case 'opacity':
            $this->setOpacity( $value );
            break;

      }

   }

   // </editor-fold>


   // <editor-fold desc="// - - -   O T H E R   M E T H O D S   - - - - - - - - - - - - - - -">

   /**
    * @return string
    */
   public function __toString()
   {

      return $this->data[ 'hex' ];

   }

   /**
    * @return array
    */
   public function toArray() : array
   {

      return $this->data;

   }

   /**
    * @return number
    */
   public function createGdValue()
   {

      if ( \is_null( $this->getAlpha() ) )
      {
         return \hexdec(
            \str_pad( \dechex( $this->red ), 2, 0, \STR_PAD_LEFT )
            . \str_pad( \dechex( $this->green ), 2, 0, \STR_PAD_LEFT )
            . \str_pad( \dechex( $this->blue ), 2, 0, \STR_PAD_LEFT )
         );
      }

      return \bindec(
         \decbin(  $this->getAlpha()  ) . \decbin(  \hexdec( \substr($this->hex, 1) )  )
      );

   }

   // </editor-fold>


   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new instance from defined string.
    *
    * @param  string $objectString
    * @return \Beluga\Drawing\Color
    */
   public static function FromString( $objectString )
   {

      if ( false !== ( $rgb = ColorTool::Color2Rgb( $objectString ) ) )
      {
         return new Color(
            ColorTool::Rgb2Hex(
               $rgb[ 0 ],
               $rgb[ 1 ],
               $rgb[ 2 ]
            )
         );
      }

      if ( false !== ( $hex = self::RgbStringToHex( $objectString ) ) )
      {
         return new Color( $hex );
      }

      return new Color();

   }

   /**
    * Init a new instance from defined array.
    *
    * @param  array $objectData
    * @return \Beluga\Drawing\Color Or boolean FALSE
    */
   public static function FromArray( array $objectData )
   {

      if ( \count( $objectData ) == 3 )
      {
         if ( false !== ( $hex = ColorTool::Color2Hex( $objectData ) ) )
         {
            return new Color( $hex );
         }
      }

      $rgb = array();

      if ( isset( $objectData[ 'r' ] ) )
      {
         $rgb[ 0 ] = \intval( $objectData[ 'r' ] );
      }
      else if ( isset( $objectData[ 'red' ] ) )
      {
         $rgb[ 0 ] = \intval( $objectData[ 'red' ] );
      }
      else if ( isset( $objectData[ 'Red' ] ) )
      {
         $rgb[ 0 ] = \intval( $objectData[ 'Red' ] );
      }
      else
      {
         return new Color();
      }

      if ( isset( $objectData[ 'g' ] ) )
      {
         $rgb[ 1 ] = \intval( $objectData[ 'g' ] );
      }
      else if ( isset( $objectData[ 'green' ] ) )
      {
         $rgb[ 1 ] = \intval( $objectData[ 'green' ] );
      }
      else if ( isset( $objectData[ 'Green' ] ) )
      {
         $rgb[ 1 ] = \intval( $objectData[ 'Green' ] );
      }
      else
      {
         return new Color();
      }

      if ( isset( $objectData[ 'b' ] ) )
      {
         $rgb[ 2 ] = \intval( $objectData[ 'b' ] );
      }
      else if ( isset( $objectData[ 'blue' ] ) )
      {
         $rgb[ 2 ] = \intval( $objectData[ 'blue' ] );
      }
      else if ( isset( $objectData[ 'Blue' ] ) )
      {
         $rgb[ 2 ] = \intval( $objectData[ 'Blue' ] );
      }
      else
      {
         return new Color();
      }

      if ( false !== ( $hex = ColorTool::Rgb2Hex( $rgb ) ) )
      {
         return new Color( $hex );
      }

      return new Color();

   }

   /**
    * Converts a GD integer value as color with alpha channel to an \Beluga\Drawing\Color instance.
    *
    * @param  int $gdValueWithAlpha
    * @return \Beluga\Drawing\Color
    */
   public static function FromGdValueWithAlpha( $gdValueWithAlpha )
   {

      $a = ( $gdValueWithAlpha >> 24 ) & 0xFF;
      $r = ( $gdValueWithAlpha >> 16 ) & 0xFF;
      $g = ( $gdValueWithAlpha >> 8 )  & 0xFF;
      $b = $gdValueWithAlpha & 0xFF;

      if ( $a == 0 )
      {
         $o = 0;
      }
      else if ( $a == 127 )
      {
         $o = 100;
      }
      else
      {
         $o = \intval( \floor( ( 100 * $a ) / 127 ) );
      }

      return new Color( array( $r, $g, $b ), $o );

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R O T E C T E D   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = =">

   /**
    * @param  string  $rgbString
    * @return boolean
    */
   protected static function RgbStringToHex( string $rgbString )
   {

      if ( \preg_match( '~^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$~', $rgbString, $m ) )
      {
         $r = \intval( $m[ 1 ] );
         $g = \intval( $m[ 2 ] );
         $b = \intval( $m[ 3 ] );
      }
      else if ( \preg_match( '~^argb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$~', $rgbString, $m ) )
      {
         $r = \intval( $m[ 2 ] );
         $g = \intval( $m[ 3 ] );
         $b = \intval( $m[ 4 ] );
      }
      else
      {
         return false;
      }

      if ( $r < 0 )   { $r = 0; }
      if ( $r > 255 ) { $r = 255; }
      if ( $g < 0 )   { $g = 0; }
      if ( $g > 255 ) { $g = 255; }
      if ( $b < 0 )   { $b = 0; }
      if ( $b > 255 ) { $b = 255; }

      return ColorTool::Rgb2Hex( $r, $g, $b );

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R I V A T E   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * @param $rgbString
    * @throws ArgumentError
    */
   private function setRGBString( string $rgbString )
   {

      if ( \preg_match( '~^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$~', $rgbString, $m ) )
      {
         $r = \intval( $m[ 1 ] );
         $g = \intval( $m[ 2 ] );
         $b = \intval( $m[ 3 ] );
      }
      else if ( \preg_match('~^(\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})$~', $rgbString, $m ) )
      {
         $r = \intval( $m[ 1 ] );
         $g = \intval( $m[ 2 ] );
         $b = \intval( $m[ 3 ] );
      }
      else
      {
         throw new ArgumentError(
            'rgbString',
            $rgbString,
            'Drawing',
            "Illegal RGB-Value Format! You can use a formats like 'rgb(r,g,b)' or 'r,g,b'."
         );
      }

      if ( $r < 0 || $r > 255 )
      {
         throw new ArgumentError(
            'r',
            $r,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      if ( $g < 0 || $g > 255 )
      {
         throw new ArgumentError(
            'g',
            $g,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      if ( $b < 0 || $b > 255 )
      {
         throw new ArgumentError(
            'b',
            $b,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      $this->data[ 'red' ]   = $r;
      $this->data[ 'green' ] = $g;
      $this->data[ 'blue' ]  = $b;
      $this->data[ 'hex' ]   = ColorTool::Rgb2Hex( $r, $g, $b );

   }

   /**
    * @param array $rgbValue
    * @throws ArgumentError
    */
   private function setRGBArray( array $rgbValue )
   {

      if ( isset( $rgbValue[ 0 ] )
        && isset( $rgbValue[ 1 ] )
        && isset( $rgbValue[ 2 ] ) )
      {

         $r = \intval( $rgbValue[ 0 ] );
         $g = \intval( $rgbValue[ 1 ] );
         $b = \intval( $rgbValue[ 2 ] );

      }
      else
      {

         $rgbValue = \array_change_key_case( $rgbValue, \CASE_LOWER );

         if ( isset( $rgbValue[ 'r' ] )
           && isset( $rgbValue[ 'g' ] )
           && isset( $rgbValue[ 'b' ] ) )
         {

            $r = \intval( $rgbValue[ 'r' ] );
            $g = \intval( $rgbValue[ 'g' ] );
            $b = \intval( $rgbValue[ 'b' ] );

         }
         else if ( isset( $rgbValue[ 'red' ] )
                && isset( $rgbValue[ 'green' ] )
                && isset( $rgbValue[ 'blue' ] ) )
         {

            $r = \intval( $rgbValue[ 'red' ] );
            $g = \intval( $rgbValue[ 'green' ] );
            $b = \intval( $rgbValue[ 'blue' ] );

         }
         else
         {
            throw new ArgumentError(
               'rgbValue',
               $rgbValue,
               'Drawing',
               "Illegal RGB-Value Format! You can use numeric indices (0=R 1=G 2=B) or a associative array with "
               . "caseless keys 'r'|'red', 'g'|'green', 'b'|'blue'"
            );
         }

      }

      if ( $r < 0 || $r > 255 )
      {
         throw new ArgumentError(
            'r',
            $r,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      if ( $g < 0 || $g > 255 )
      {
         throw new ArgumentError(
            'g',
            $g,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      if ( $b < 0 || $b > 255 )
      {
         throw new ArgumentError(
            'b',
            $b,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      $this->data[ 'red' ]   = $r;
      $this->data[ 'green' ] = $g;
      $this->data[ 'blue' ]  = $b;
      $this->data[ 'hex' ]   = ColorTool::Rgb2Hex( $r, $g, $b );

   }

   /**
    * @param $argbValue
    * @throws ArgumentError
    */
   private function setARGBString( string $argbValue )
   {

      $a = $r = $b = $g = 0;
      if ( \preg_match( '~^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$~', $argbValue, $m ) )
      {
         $r = \intval( $m[ 1 ] );
         $g = \intval( $m[ 2 ] );
         $b = \intval( $m[ 3 ] );
      }
      else if ( \preg_match( '~^argb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$~', $argbValue, $m ) )
      {
         $a = \intval( $m[ 1 ] );
         $r = \intval( $m[ 2 ] );
         $g = \intval( $m[ 3 ] );
         $b = \intval( $m[ 4 ] );
      }
      else
      {
         throw new ArgumentError(
            'argbValue',
            $argbValue,
            'Drawing',
            "Illegal ARGB-Value Format! You can use a formats like 'rgb(r,g,b)' or 'argb(alpha,r,g,b)'."
         );
      }

      $this->checkArgb( $a, $r, $g, $b );
      $this->assignArgb( $a, $r, $g, $b );

   }

   private function checkArgb( int $a, int $r, int $g, int $b )
   {

      if ( $a < 0 || $a > 127 )
      {
         throw new ArgumentError(
            'a',
            $a,
            'Drawing',
            'Illegal alpha value outside the allowed range 0-127.'
         );
      }

      if ( $r < 0 || $r > 255 )
      {
         throw new ArgumentError(
            'r',
            $r,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      if ( $g < 0 || $g > 255 )
      {
         throw new ArgumentError(
            'g',
            $g,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      if ( $b < 0 || $b > 255 )
      {
         throw new ArgumentError(
            'b',
            $b,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

   }

   private function assignArgb( int $a, int $r, int $g, int $b )
   {

      $this->data[ 'red' ]   = $r;
      $this->data[ 'green' ] = $g;
      $this->data[ 'blue' ]  = $b;
      $this->data[ 'hex' ]   = ColorTool::Rgb2Hex( $r, $g, $b );
      $this->data[ 'alpha' ] = $a;

      if ( $this->data[ 'alpha' ] == 0 )
      {
         $this->data[ 'opacity' ] = 100;
      }
      else if ( $this->data[ 'alpha' ] == 127 )
      {
         $this->data[ 'opacity' ] = 0;
      }
      else
      {
         $this->data[ 'opacity' ] = \intval( \floor( ( $this->data['alpha'] * 100 ) / 127 ) );
      }

   }

   /**
    * @param array $argbValue
    * @throws ArgumentError
    */
   private function setARGBArray( array $argbValue )
   {

      $a = $r = $b = $g = 0;

      if ( isset( $argbValue[ 0 ] )
        && isset( $argbValue[ 1 ] )
        && isset( $argbValue[ 2 ] ) )
      {

         if ( isset( $argbValue[ 3 ] ) )
         {
            $a = \intval( $argbValue[ 0 ] );
            $r = \intval( $argbValue[ 1 ] );
            $g = \intval( $argbValue[ 2 ] );
            $b = \intval( $argbValue[ 3 ] );
         }
         else
         {
            $r = \intval( $argbValue[ 0 ] );
            $g = \intval( $argbValue[ 1 ] );
            $b = \intval( $argbValue[ 2 ] );
         }

      }
      else
      {

         $argbValue = \array_change_key_case( $argbValue, \CASE_LOWER );

         if ( isset( $argbValue[ 'r' ] )
           && isset( $argbValue[ 'g' ] )
           && isset( $argbValue[ 'b' ] ) )
         {

            $r = \intval( $argbValue[ 'r' ] );
            $g = \intval( $argbValue[ 'g' ] );
            $b = \intval( $argbValue[ 'b' ] );
            if ( isset( $argbValue[ 'a' ] ) )
            {
               $a = \intval( $argbValue[ 'a' ] );
            }

         }
         else if ( isset( $argbValue[ 'red' ] )
            && isset( $argbValue[ 'green' ] )
            && isset( $argbValue[ 'blue' ] ) )
         {

            $r = \intval( $argbValue[ 'red' ] );
            $g = \intval( $argbValue[ 'green' ] );
            $b = \intval( $argbValue[ 'blue' ] );
            if ( isset( $argbValue[ 'alpha' ] ) )
            {
               $a = \intval( $argbValue[ 'alpha' ] );
            }

         }
         else
         {

            throw new ArgumentError(
               'argbValue',
               $argbValue,
               'Drawing',
               "Illegal ARGB-Value Format! You can use numeric indices (0=R 1=G 2=B) or (0=A 1=R 2=G 3=B) or a "
               . "associative array with caseless keys 'r'|'red', 'g'|'green', 'b'|'blue' and 'a'|'alpha'"
            );

         }

      }

      if ( $a < 0 || $a > 127 )
      {
         throw new ArgumentError(
            'a',
            $a,
            'Drawing',
            'Illegal alpha value outside the allowed range 0-127.'
         );
      }

      if ( $r < 0 || $r > 255 )
      {
         throw new ArgumentError(
            'r',
            $r,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      if ( $g < 0 || $g > 255 )
      {
         throw new ArgumentError(
            'g',
            $g,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      if ( $b < 0 || $b > 255 )
      {
         throw new ArgumentError(
            'b',
            $b,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      $this->data[ 'red' ]   = $r;
      $this->data[ 'green' ] = $g;
      $this->data[ 'blue' ]  = $b;
      $this->data[ 'hex' ]   = ColorTool::Rgb2Hex( $r, $g, $b );
      $this->data[ 'alpha' ] = $a;

      if ( $this->data[ 'alpha' ] == 0 )
      {
         $this->data['opacity'] = 100;
      }
      else if ( $this->data[ 'alpha' ] == 127 )
      {
         $this->data['opacity'] = 0;
      }
      else
      {
         $this->data[ 'opacity' ] = \intval( \floor( ( $this->data['alpha'] * 100 ) / 127 ) );
      }

   }

   /**
    * @param $rgbaValue
    * @throws ArgumentError
    */
   private function setRGBAString( string $rgbaValue )
   {

      $a = $r = $b = $g = 0;

      if ( \preg_match( '~^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$~', $rgbaValue, $m ) )
      {
         $r = \intval( $m[ 1 ] );
         $g = \intval( $m[ 2 ] );
         $b = \intval( $m[ 3 ] );
      }
      else if ( \preg_match( '~^rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$~', $rgbaValue, $m ) )
      {
         $r = \intval( $m[ 2 ] );
         $g = \intval( $m[ 3 ] );
         $b = \intval( $m[ 4 ] );
         $a = \intval( $m[ 1 ] );
      }
      else
      {
         throw new ArgumentError(
            'rgbaValue',
            $rgbaValue,
            'Drawing',
            "Illegal RGBA-Value Format! You can use a formats like 'rgb(r,g,b)' or 'rgba(r,g,b,alpha)'."
         );
      }

      if ( $a < 0 || $a > 127 )
      {
         throw new ArgumentError(
            'a',
            $a,
            'Drawing',
            'Illegal alpha value outside the allowed range 0-127.'
         );
      }

      if ( $r < 0 || $r > 255 )
      {
         throw new ArgumentError(
            'r',
            $r,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      if ( $g < 0 || $g > 255 )
      {
         throw new ArgumentError(
            'g',
            $g,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      if ( $b < 0 || $b > 255 )
      {
         throw new ArgumentError(
            'b',
            $b,
            'Drawing',
            'Illegal r|g|b value outside the allowed range 0-255.'
         );
      }

      $this->data[ 'red' ]   = $r;
      $this->data[ 'green' ] = $g;
      $this->data[ 'blue' ]  = $b;
      $this->data[ 'hex' ]   = ColorTool::Rgb2Hex( $r, $g, $b );
      $this->data[ 'alpha' ] = $a;

      if ( $this->data[ 'alpha' ] == 0 )
      {
         $this->data['opacity'] = 100;
      }
      else if ( $this->data[ 'alpha' ] == 127 )
      {
         $this->data['opacity'] = 0;
      }
      else
      {
         $this->data[ 'opacity' ] = \intval( \floor( ( $this->data['alpha'] * 100 ) / 127 ) );
      }

   }

   /**
    * @param array $rgbaValue
    * @throws ArgumentError
    */
   private function setRGBAArray( array $rgbaValue )
   {

      $a = $r = $b = $g = 0;

      if ( isset( $rgbaValue[ 0 ] )
        && isset( $rgbaValue[ 1 ] )
        && isset( $rgbaValue[ 2 ] ) )
      {

         if ( isset( $rgbaValue[ 3 ] ) )
         {
            $a = \intval( $rgbaValue[ 0 ] );
            $r = \intval( $rgbaValue[ 1 ] );
            $g = \intval( $rgbaValue[ 2 ] );
            $b = \intval( $rgbaValue[ 3 ] );
         }
         else
         {
            $r = \intval( $rgbaValue[ 0 ] );
            $g = \intval( $rgbaValue[ 1 ] );
            $b = \intval( $rgbaValue[ 2 ] );
         }

      }
      else
      {

         $rgbaValue = \array_change_key_case( $rgbaValue,\CASE_LOWER );
         if ( isset( $rgbaValue[ 'r' ] )
           && isset( $rgbaValue[ 'g' ] )
           && isset( $rgbaValue[ 'b' ] ) )
         {

            $r = \intval( $rgbaValue[ 'r' ] );
            $g = \intval( $rgbaValue[ 'g' ] );
            $b = \intval( $rgbaValue[ 'b' ] );
            if ( isset( $rgbaValue[ 'a' ] ) )
            {
               $a = \intval( $rgbaValue[ 'a' ] );
            }

         }
         else if ( isset( $rgbaValue[ 'red' ] )
            && isset( $rgbaValue[ 'green' ] )
            && isset( $rgbaValue[ 'blue' ] ) )
         {

            $r = \intval( $rgbaValue[ 'red' ] );
            $g = \intval( $rgbaValue[ 'green' ] );
            $b = \intval( $rgbaValue[ 'blue' ] );
            if ( isset( $rgbaValue[ 'alpha' ] ) )
            {
               $a = \intval( $rgbaValue[ 'alpha' ] );
            }

         }
         else
         {

            throw new ArgumentError(
               'rgbaValue',
               $rgbaValue,
               'Drawing',
               "Illegal RGBA-Value Format! You can use numeric indices (0=R 1=G 2=B) or (0=R 1=G 2=B 3=A) or a"
               . " associative array with caseless keys 'r'|'red', 'g'|'green', 'b'|'blue' and 'a'|'alpha'"
            );

         }

      }

      $this->checkArgb( $a, $r, $g, $b );
      $this->assignArgb( $a, $r, $g, $b );

   }

   // </editor-fold>


}

