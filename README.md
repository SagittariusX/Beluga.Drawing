# Beluga.Drawing

Some helpful image and drawing tools.

```bash
composer require sagittariusx/beluga.drawing
```

or include it inside you're composer.json

```json
{
   "require": {
      "php": ">=7.0",
      "sagittariusx/beluga.drawing": "^0.1.0"
   }
}
```


The library declares the main classes inside the `Beluga\Drawing` namespace:

* `Beluga\Drawing\Size`:
   Declares the 2 dimensional size of an object (width + height) and some helpful methods
* `Beluga\Drawing\Point`:
   Declares the 2 dimensional location of an object (x + y) and some helpful methods.
* `Beluga\Drawing\Rectangle`:
   Declares the 2 dimensional size and location of an object. It is an combination of the 2 classes `Size` and `Point`.
* `Beluga\Drawing\Gravity`:
   Interface that declares constants for define the 2 dimensional gravity of an object. The gravity means values that
   define the align of an object inside an other. (e.G. `Gravity::TOP_LEFT`)
* `Beluga\Drawing\ContentAlign`:
   An other way to define the content align of an object inside an other
* `Beluga\Drawing\ColorTool`:
   A static helper class for color handling.
* `Beluga\Drawing\Color`
   This class define and handle all information of a color. (R, G, B, Opacity/Alpha, HEX-Format, Color name).
* `Beluga\Drawing\ColorGd`
   extends from Color class and is more GD specific.
   
The sub namespace `Beluga\Drawing\Image` declares the following:

* `Beluga\Drawing\Image\IImage`:
   This interfaces defines all features, required by a valid image implementation.
* `Beluga\Drawing\Image\AbstractImage`:
   This abstract class implements the main image functionality (not much) usable by all `IImage` implementations.
* `Beluga\Drawing\Image\GdImage`:
   This is currently the only usable `IImage` implementation. It works completely with PHPs basic GD graphics library.
* `Beluga\Drawing\Image\ImageSizeReducer`:
   A tool for resizing an `IImage` implementation, that can store and restore the current size reducing settings
   from `XML` (writing=`XMLWriter` and reading=`SimpleXMLElement`), `Array` and `JSON` string
* `Beluga\Drawing\Image\ImageSizeReducerType`:
   Declares all usable image size reducer types. (e.g.: `ImageSizeReducerType::LONG_SIDE`)
* `Beluga\Drawing\Image\ImageSizeReducerCollection`:
   Defines a collection of `Beluga\Drawing\Image\ImageSizeReducer` items.