<?php
/**
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga
 * @since          2016-08-17
 * @subpackage     Drawing
 * @version        0.1.0
 */

namespace Beluga\Drawing;


/**
 * The Beluga\Drawing\Gravity fake enum interface.
 *
 * It defines the gravity/align of an element inside an other container element
 *
 * @since v0.1.0
 */
interface Gravity
{


   /**
    * Aligned at the top left side of a container
    */
   const TOP_LEFT      = 0;

   /**
    * Aligned at the top center side of a container
    */
   const TOP_CENTER    = 1;

   /**
    * Aligned at the top right side of a container
    */
   const TOP_RIGHT     = 2;

   /**
    * Aligned at the middle left side of a container
    */
   const MIDDLE_LEFT   = 3;

   /**
    * Aligned at the middle center side of a container
    */
   const MIDDLE_CENTER = 4;

   /**
    * Aligned at the middle right side of a container
    */
   const MIDDLE_RIGHT  = 5;

   /**
    * Aligned at the bottom left side of a container
    */
   const BOTTOM_LEFT   = 6;

   /**
    * Aligned at the bottom center side of a container
    */
   const BOTTOM_CENTER = 7;

   /**
    * Aligned at the bottom right side of a container
    */
   const BOTTOM_RIGHT  = 8;

   /**
    * Define all known gravity values
    */
   const KNOWN_VALUES = [ self::TOP_LEFT   , self::TOP_CENTER   , self::TOP_RIGHT   ,
                          self::MIDDLE_LEFT, self::MIDDLE_CENTER, self::MIDDLE_RIGHT,
                          self::BOTTOM_LEFT, self::BOTTOM_CENTER, self::BOTTOM_RIGHT ];


}

