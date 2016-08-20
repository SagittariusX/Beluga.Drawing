<?php
/**
 * In this file the class {@see \Beluga\Drawing\ColorTool} is defined.
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
 * A static color helping class
 *
 * @since      v0.1
 */
abstract class ColorTool
{

   
   // <editor-fold desc="// = = = =   P R I V A T E   S T A T I C   F I E L D S   = = = = = = = = = = = = = = = = = =">

   // <editor-fold desc="// = >   Color name => RGB Associations">

   private static $ColornameRGB = [
      'aliceblue'            => [ 240, 248, 255 ], 'antiquewhite'         => [ 250, 235, 215 ],
      'aqua'                 => [ 0, 255, 255 ],   'aquamarine'           => [ 127, 255, 212 ],
      'azure'                => [ 240, 255, 255 ], 'beige'                => [ 245, 245, 220 ],
      'bisque'               => [ 255, 228, 196 ], 'black'                => [ 0, 0, 0 ],
      'blanchedalmond'       => [ 255, 235, 205 ], 'blue'                 => [ 0, 0, 255 ],
      'blueviolet'           => [ 138, 43, 226 ],  'brown'                => [ 165, 42, 42 ],
      'burlywood'            => [ 222, 184, 135 ], 'cadetblue'            => [ 95, 158, 160 ],
      'chartreuse'           => [ 127, 255, 0 ],   'chocolate'            => [ 210, 105, 30 ],
      'coral'                => [ 255, 127, 80 ],  'cornflowerblue'       => [ 100, 149, 237 ],
      'cornsilk'             => [ 255, 248, 220 ], 'crimson'              => [ 220, 20, 60 ],
      'cyan'                 => [ 0, 255, 255 ],   'darkblue'             => [ 0, 0, 139 ],
      'darkcyan'             => [ 0, 139, 139 ],   'darkgoldenrod'        => [ 184, 134, 11 ],
      'darkgray'             => [ 169, 169, 169 ], 'darkgreen'            => [ 0, 100, 0 ],
      'darkkhaki'            => [ 189, 183, 107 ], 'darkmagenta'          => [ 139, 0, 139 ],
      'darkolivegreen'       => [ 85, 107, 47 ],   'darkorange'           => [ 255, 140, 0 ],
      'darkorchid'           => [ 153, 50, 204 ],  'darkred'              => [ 139, 0, 0 ],
      'darksalmon'           => [ 233, 150, 122 ], 'darkseagreen'         => [ 143, 188, 143 ],
      'darkslateblue'        => [ 72, 61, 139 ],   'darkslategray'        => [ 47, 79, 79 ],
      'darkturquoise'        => [ 0, 206, 209 ],   'darkviolet'           => [ 148, 0, 211 ],
      'deeppink'             => [ 255, 20, 147 ],  'deepskyblue'          => [ 0, 191, 255 ],
      'dimgray'              => [ 105, 105, 105 ], 'dodgerblue'           => [ 30, 144, 255 ],
      'firebrick'            => [ 178, 34, 34 ],   'floralwhite'          => [ 255, 250, 240 ],
      'forestgreen'          => [ 34, 139, 34 ],   'fuchsia'              => [ 255, 0, 255 ],
      'gainsboro'            => [ 220, 220, 220 ], 'ghostwhite'           => [ 248, 248, 255 ],
      'gold'                 => [ 255, 215, 0 ],   'goldenrod'            => [ 218, 165, 32 ],
      'gray'                 => [ 128, 128, 128 ], 'green'                => [ 0, 128, 0 ],
      'greenyellow'          => [ 173, 255, 47 ],  'honeydew'             => [ 240, 255, 240 ],
      'hotpink'              => [ 255, 105, 180 ], 'indianred'            => [ 205, 92, 92 ],
      'indigo'               => [ 75, 0, 130 ],    'ivory'                => [ 255, 255, 240 ],
      'khaki'                => [ 240, 230, 140 ], 'lavender'             => [ 230, 230, 250 ],
      'lavenderblush'        => [ 255, 240, 245 ], 'lawngreen'            => [ 124, 252, 0 ],
      'lemonchiffon'         => [ 255, 250, 205 ], 'lightblue'            => [ 173, 216, 230 ],
      'lightcoral'           => [ 240, 128, 128 ], 'lightcyan'            => [ 224, 255, 255 ],
      'lightgoldenrodyellow' => [ 250, 250, 210 ], 'lightgrey'            => [ 211, 211, 211 ],
      'lightgreen'           => [ 144, 238, 144 ], 'lightpink'            => [ 255, 182, 193 ],
      'lightsalmon'          => [ 255, 160, 122 ], 'lightseagreen'        => [ 32, 178, 170 ],
      'lightskyblue'         => [ 135, 206, 250 ], 'lightslategray'       => [ 119, 136, 153 ],
      'lightsteelblue'       => [ 176, 196, 222 ], 'lightyellow'          => [ 255, 255, 224 ],
      'lime'                 => [ 0, 255, 0 ],     'limegreen'            => [ 50, 205, 50 ],
      'linen'                => [ 250, 240, 230 ], 'magenta'              => [ 255, 0, 255 ],
      'maroon'               => [ 128, 0, 0 ],     'mediumaquamarine'     => [ 102, 205, 170 ],
      'mediumblue'           => [ 0, 0, 205 ],     'mediumorchid'         => [ 186, 85, 211 ],
      'mediumpurple'         => [ 147, 112, 216 ], 'mediumseagreen'       => [ 60, 179, 113 ],
      'mediumslateblue'      => [ 123, 104, 238 ], 'mediumspringgreen'    => [ 0, 250, 154 ],
      'mediumturquoise'      => [ 72, 209, 204 ],  'mediumvioletred'      => [ 199, 21, 133 ],
      'midnightblue'         => [ 25, 25, 112 ],   'mintcream'            => [ 245, 255, 250 ],
      'mistyrose'            => [ 255, 228, 225 ], 'moccasin'             => [ 255, 228, 181 ],
      'navajowhite'          => [ 255, 222, 173 ], 'navy'                 => [ 0, 0, 128 ],
      'oldlace'              => [ 253, 245, 230 ], 'olive'                => [ 128, 128, 0 ],
      'olivedrab'            => [ 107, 142, 35 ],  'orange'               => [ 255, 165, 0 ],
      'orangered'            => [ 255, 69, 0 ],    'orchid'               => [ 218, 112, 214 ],
      'palegoldenrod'        => [ 238, 232, 170 ], 'palegreen'            => [ 152, 251, 152 ],
      'paleturquoise'        => [ 175, 238, 238 ], 'palevioletred'        => [ 216, 112, 147 ],
      'papayawhip'           => [ 255, 239, 213 ], 'peachpuff'            => [ 255, 218, 185 ],
      'peru'                 => [ 205, 133, 63 ],  'pink'                 => [ 255, 192, 203 ],
      'plum'                 => [ 221, 160, 221 ], 'powderblue'           => [ 176, 224, 230 ],
      'purple'               => [ 128, 0, 128 ],   'red'                  => [ 255, 0, 0 ],
      'rosybrown'            => [ 188, 143, 143 ], 'royalblue'            => [ 65, 105, 225 ],
      'saddlebrown'          => [ 139, 69, 19 ],   'salmon'               => [ 250, 128, 114 ],
      'sandybrown'           => [ 244, 164, 96 ],  'seagreen'             => [ 46, 139, 87 ],
      'seashell'             => [ 255, 245, 238 ], 'sienna'               => [ 160, 82, 45 ],
      'silver'               => [ 192, 192, 192 ], 'skyblue'              => [ 135, 206, 235 ],
      'slateblue'            => [ 106, 90, 205 ],  'slategray'            => [ 112, 128, 144 ],
      'snow'                 => [ 255, 250, 250 ], 'springgreen'          => [ 0, 255, 127 ],
      'steelblue'            => [ 70, 130, 180 ],  'tan'                  => [ 210, 180, 140 ],
      'teal'                 => [ 0, 128, 128 ],   'thistle'              => [ 216, 191, 216 ],
      'tomato'               => [ 255, 99, 71 ],   'turquoise'            => [ 64, 224, 208 ],
      'violet'               => [ 238, 130, 238 ], 'wheat'                => [ 245, 222, 179 ],
      'white'                => [ 255, 255, 255 ], 'whitesmoke'           => [ 245, 245, 245 ],
      'yellow'               => [ 255, 255, 0 ],   'yellowgreen'          => [ 154, 205, 50 ]
   ];

   // </editor-fold>

   // <editor-fold desc="// = >   Color name => HEX Associations">

   private static $ColornameHex = [
      'aliceblue'            => '#F0F8FF', 'antiquewhite'         => '#FAEBD7', 'aqua'                 => '#00FFFF',
      'aquamarine'           => '#7FFFD4', 'azure'                => '#F0FFFF', 'beige'                => '#F5F5DC',
      'bisque'               => '#FFE4C4', 'black'                => '#000000', 'blanchedalmond'       => '#FFEBCD',
      'blue'                 => '#0000FF', 'blueviolet'           => '#8A2BE2', 'brown'                => '#A52A2A',
      'burlywood'            => '#DEB887', 'cadetblue'            => '#5F9EA0', 'chartreuse'           => '#7FFF00',
      'chocolate'            => '#D2691E', 'coral'                => '#FF7F50', 'cornflowerblue'       => '#6495ED',
      'cornsilk'             => '#FFF8DC', 'crimson'              => '#DC143C', 'cyan'                 => '#00FFFF',
      'darkblue'             => '#00008B', 'darkcyan'             => '#008B8B', 'darkgoldenrod'        => '#B8860B',
      'darkgray'             => '#A9A9A9', 'darkgreen'            => '#006400', 'darkkhaki'            => '#BDB76B',
      'darkmagenta'          => '#8B008B', 'darkolivegreen'       => '#556B2F', 'darkorange'           => '#FF8C00',
      'darkorchid'           => '#9932CC', 'darkred'              => '#8B0000', 'darksalmon'           => '#E9967A',
      'darkseagreen'         => '#8FBC8F', 'darkslateblue'        => '#483D8B', 'darkslategray'        => '#2F4F4F',
      'darkturquoise'        => '#00CED1', 'darkviolet'           => '#9400D3', 'deeppink'             => '#FF1493',
      'deepskyblue'          => '#00BFFF', 'dimgray'              => '#696969', 'dodgerblue'           => '#1E90FF',
      'firebrick'            => '#B22222', 'floralwhite'          => '#FFFAF0', 'forestgreen'          => '#228B22',
      'fuchsia'              => '#FF00FF', 'gainsboro'            => '#DCDCDC', 'ghostwhite'           => '#F8F8FF',
      'gold'                 => '#FFD700', 'goldenrod'            => '#DAA520', 'gray'                 => '#808080',
      'green'                => '#008000', 'greenyellow'          => '#ADFF2F', 'honeydew'             => '#F0FFF0',
      'hotpink'              => '#FF69B4', 'indianred'            => '#CD5C5C', 'indigo'               => '#4B0082',
      'ivory'                => '#FFFFF0', 'khaki'                => '#F0E68C', 'lavender'             => '#E6E6FA',
      'lavenderblush'        => '#FFF0F5', 'lawngreen'            => '#7CFC00', 'lemonchiffon'         => '#FFFACD',
      'lightblue'            => '#ADD8E6', 'lightcoral'           => '#F08080', 'lightcyan'            => '#E0FFFF',
      'lightgoldenrodyellow' => '#FAFAD2', 'lightgrey'            => '#D3D3D3', 'lightgreen'           => '#90EE90',
      'lightpink'            => '#FFB6C1', 'lightsalmon'          => '#FFA07A', 'lightseagreen'        => '#20B2AA',
      'lightskyblue'         => '#87CEFA', 'lightslategray'       => '#778899', 'lightsteelblue'       => '#B0C4DE',
      'lightyellow'          => '#FFFFE0', 'lime'                 => '#00FF00', 'limegreen'            => '#32CD32',
      'linen'                => '#FAF0E6', 'magenta'              => '#FF00FF', 'maroon'               => '#800000',
      'mediumaquamarine'     => '#66CDAA', 'mediumblue'           => '#0000CD', 'mediumorchid'         => '#BA55D3',
      'mediumpurple'         => '#9370D8', 'mediumseagreen'       => '#3CB371', 'mediumslateblue'      => '#7B68EE',
      'mediumspringgreen'    => '#00FA9A', 'mediumturquoise'      => '#48D1CC', 'mediumvioletred'      => '#C71585',
      'midnightblue'         => '#191970', 'mintcream'            => '#F5FFFA', 'mistyrose'            => '#FFE4E1',
      'moccasin'             => '#FFE4B5', 'navajowhite'          => '#FFDEAD', 'navy'                 => '#000080',
      'oldlace'              => '#FDF5E6', 'olive'                => '#808000', 'olivedrab'            => '#6B8E23',
      'orange'               => '#FFA500', 'orangered'            => '#FF4500', 'orchid'               => '#DA70D6',
      'palegoldenrod'        => '#EEE8AA', 'palegreen'            => '#98FB98', 'paleturquoise'        => '#AFEEEE',
      'palevioletred'        => '#D87093', 'papayawhip'           => '#FFEFD5', 'peachpuff'            => '#FFDAB9',
      'peru'                 => '#CD853F', 'pink'                 => '#FFC0CB', 'plum'                 => '#DDA0DD',
      'powderblue'           => '#B0E0E6', 'purple'               => '#800080', 'red'                  => '#FF0000',
      'rosybrown'            => '#BC8F8F', 'royalblue'            => '#4169E1', 'saddlebrown'          => '#8B4513',
      'salmon'               => '#FA8072', 'sandybrown'           => '#F4A460', 'seagreen'             => '#2E8B57',
      'seashell'             => '#FFF5EE', 'sienna'               => '#A0522D', 'silver'               => '#C0C0C0',
      'skyblue'              => '#87CEEB', 'slateblue'            => '#6A5ACD', 'slategray'            => '#708090',
      'snow'                 => '#FFFAFA', 'springgreen'          => '#00FF7F', 'steelblue'            => '#4682B4',
      'tan'                  => '#D2B48C', 'teal'                 => '#008080', 'thistle'              => '#D8BFD8',
      'tomato'               => '#FF6347', 'turquoise'            => '#40E0D0', 'violet'               => '#EE82EE',
      'wheat'                => '#F5DEB3', 'white'                => '#FFFFFF', 'whitesmoke'           => '#F5F5F5',
      'yellow'               => '#FFFF00', 'yellowgreen'          => '#9ACD32'
   ];

   // </editor-fold>

   // </editor-fold>

   
   # <editor-fold desc="= = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = = =">

   /**
    * Returns if the defined color string is a usable hexadecimal color definition.
    *
    * @param  mixed $colorDefinition
    * @return bool
    */
   public static function IsHexFormat( $colorDefinition ) : bool
   {

      if ( \is_array( $colorDefinition ) )
      {
         return false;
      }

      return (bool) \preg_match(
         '~^#?([a-f0-9]{2}|[a-f0-9]{3}|[a-f0-9]{6}|[a-f0-9]{8})$~i',
         $colorDefinition
      );

   }

   /**
    * Converts the defined color into a RGB color definition (a numeric indicated array 0=R 1=G 2=B
    *
    * @param  string|array $colorDefinition Color name or hexadecimal color definition
    * @return array Or boolean FALSE
    * @throws \Beluga\ArgumentError
    */
   public static function Color2Rgb( $colorDefinition )
   {

      if ( \is_array( $colorDefinition ) )
      {
         if ( \count( $colorDefinition ) > 2 && \count( $colorDefinition ) < 5 )
         {
            return $colorDefinition;
         }
         throw new ArgumentError(
            'colorDefinition',
            $colorDefinition,
            'Drawing',
            'A (a)rgb(a) array with 3-4 Elements is required!'
         );
      }

      if ( static::IsHexFormat( $colorDefinition ) )
      {
         return static::Hex2Rgb( $colorDefinition );
      }

      $uc = \strtolower( $colorDefinition );

      if ( isset( static::$ColornameRGB[ $uc ] ) )
      {
         return static::$ColornameRGB[ $uc ];
      }

      if ( false !== ( $hex = static::Rgb2Hex( $colorDefinition ) ) )
      {
         return static::Hex2Rgb( $hex );
      }

      return false;

   }

   /**
    * Converts the defined Color definition into hexadecimal representation.
    *
    * @param  string|array $colorDefinition Color name, RGB color array or Hex color definition
    * @return string|FALSE
    * @throws \Beluga\ArgumentError
    */
   public static function Color2Hex( $colorDefinition )
   {

      if ( \is_array( $colorDefinition ) )
      {
         if ( \count( $colorDefinition ) != 3 )
         {
            throw new ArgumentError(
               'colorDefinition',
               $colorDefinition,
               'Drawing',
               'A rgb array with 3 Elements is required!'
            );
         }
         return static::Rgb2Hex( $colorDefinition );
      }

      if ( static::IsHexFormat( $colorDefinition ) )
      {
         return $colorDefinition;
      }

      $uc = \strtolower( $colorDefinition );

      if ( isset( static::$ColornameHex[ $uc ] ) )
      {
         return \strtolower( static::$ColornameHex[ $uc ] );
      }

      if ( false !== ( $hex = static::Rgb2Hex( $colorDefinition ) ) )
      {
         return $hex;
      }

      return false;

   }

   /**
    * Convert a color from hexadecimal notation to RGB array.
    *
    * @param  string $color
    * @return array|bool
    */
   public static function Hex2Rgb( string $color )
   {

      if ( $color[ 0 ] == '#' )
      {
         $color = \substr( $color, 1 );
      }

      if ( \strlen( $color ) == 8 )
      {
         $color = \substr( $color, 2 );
      }

      if ( \strlen( $color ) == 6 )
      {
         $r = $color[ 0 ] . $color[ 1 ];
         $g = $color[ 2 ] . $color[ 3 ];
         $b = $color[ 4 ] . $color[ 5 ];
      }
      else if ( \strlen( $color ) == 3 )
      {
         $r = $color[ 0 ] . $color[ 0 ];
         $g = $color[ 1 ] . $color[ 1 ];
         $b = $color[ 2 ] . $color[ 2 ];
      }
      else if ( \strlen( $color ) == 2 )
      {
         $r = $color;
         $g = $color;
         $b = $color;
      }
      else
      {
         return false;
      }

      return [ \hexdec( $r ), \hexdec( $g ), \hexdec( $b ) ];

   }

   /**
    * Converts a RGB array or the 3 r, g, b values into a hexadecimal color representation.
    *
    * @param  array|integer $r RGB Array or the red bit value.
    * @param  int           $g The green bit value.
    * @param  int           $b The blue bit value
    * @return string|FALSE
    */
   public static function Rgb2Hex( $r, int $g = -1, int $b = -1 )
   {

      if ( \is_array( $r ) )
      {

         if ( \count( $r ) != 3 )
         {
            return false;
         }

         \array_change_key_case( $r, \CASE_LOWER );

         if ( isset( $r[ 'r' ] ) && isset( $r[ 'g' ] ) && isset( $r[ 'b' ] ) )
         {
            $g = $r[ 'g' ];
            $b = $r[ 'b' ];
            $r = $r[ 'r' ];
         }
         else if ( isset( $r[ 'red' ] ) && isset( $r[ 'green' ] ) && isset( $r[ 'blue' ] ) )
         {
            $g = $r[ 'green' ];
            $b = $r[ 'blue' ];
            $r = $r[ 'red' ];
         }
         else
         {
            $rgb = $r;
            list( $r, $g, $b ) = $rgb;
         }

      }
      else if ( \preg_match( '~^(rgb\s*\()?(\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)?$~', (string) $r, $m ) )
      {

         list( $ignore, $r, $g, $b ) = $m;
         if ( ! empty( $ignore ) )
         {
            unset( $ignore );
         }

      }
      else if ( ! \is_int( $r ) )
      {

         return false;

      }
      $r1 = \intval( $r );
      $g1 = \intval( $g );
      $b1 = \intval( $b );
      $r  = \dechex( $r1 < 0 ? 0 : ( $r1 > 255 ? 255 : $r1 ) );
      $g  = \dechex( $g1 < 0 ? 0 : ( $g1 > 255 ? 255 : $g1 ) );
      $b  = \dechex( $b1 < 0 ? 0 : ( $b1 > 255 ? 255 : $b1 ) );

      return \sprintf( '#%02s%02s%02s', $r, $g, $b );

   }

   /**
    * Returns, if the defined color is associated to a known netscape color name. If so the method returns true, and
    * the parameter $colorNameResult returns the associated color name.
    *
    * @param string|array $colorDefinition
    * @param string       $colorNameResult
    * @return boolean
    */
   public static function IsNamedColor( $colorDefinition, &$colorNameResult ) : bool
   {

      if ( false === ( $hex = self::Color2Hex( $colorDefinition ) ) )
      {
         return false;
      }

      if ( false !== ( $colorName = \array_search( \strtoupper( $hex ), self::$ColornameHex ) ) )
      {
         $colorNameResult = $colorName;
         return true;
      }

      return false;

   }

   /**
    * Return all known color names.
    *
    * @return array
    */
   public static function GetColorNames() : array
   {

      return \array_keys( static::$ColornameHex );

   }

   // </editor-fold>


}

