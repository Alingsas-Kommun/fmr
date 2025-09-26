<?php

namespace App\Utilities;

class Color
{
    /**
     * Convert hex color to hue value for OKLCH color space.
     *
     * @param string $hex Hex color (e.g., '#fab526')
     * @return float Hue value in degrees
     */
    public static function hexToHue($hex)
    {
        $oklch = self::hexToOklch($hex);
        return $oklch['h'];
    }

    /**
     * Convert hex color to RGB values.
     *
     * @param string $hex Hex color (e.g., '#fab526')
     * @return array RGB values as [r, g, b]
     */
    public static function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }

    /**
     * Convert RGB values to hex color.
     *
     * @param int $r Red value (0-255)
     * @param int $g Green value (0-255)
     * @param int $b Blue value (0-255)
     * @return string Hex color (e.g., '#fab526')
     */
    public static function rgbToHex($r, $g, $b)
    {
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Convert hex color to HSL values.
     *
     * @param string $hex Hex color (e.g., '#fab526')
     * @return array HSL values as [h, s, l]
     */
    public static function hexToHsl($hex)
    {
        $rgb = self::hexToRgb($hex);
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;
        
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $delta = $max - $min;
        
        $l = ($max + $min) / 2;
        
        if ($delta == 0) {
            return [0, 0, $l]; // Grayscale
        }
        
        $s = $l < 0.5 ? $delta / ($max + $min) : $delta / (2 - $max - $min);
        
        $h = 0;
        if ($max == $r) {
            $h = 60 * fmod((($g - $b) / $delta), 6);
        } elseif ($max == $g) {
            $h = 60 * (($b - $r) / $delta + 2);
        } else {
            $h = 60 * (($r - $g) / $delta + 4);
        }
        
        if ($h < 0) {
            $h += 360;
        }
        
        return [round($h, 3), round($s, 3), round($l, 3)];
    }

    /**
     * Lighten a hex color by a percentage.
     *
     * @param string $hex Hex color (e.g., '#fab526')
     * @param float $percent Percentage to lighten (0-100)
     * @return string Lightened hex color
     */
    public static function lighten($hex, $percent)
    {
        $hsl = self::hexToHsl($hex);
        $hsl[2] = min(1, $hsl[2] + ($percent / 100));
        return self::hslToHex($hsl[0], $hsl[1], $hsl[2]);
    }

    /**
     * Darken a hex color by a percentage.
     *
     * @param string $hex Hex color (e.g., '#fab526')
     * @param float $percent Percentage to darken (0-100)
     * @return string Darkened hex color
     */
    public static function darken($hex, $percent)
    {
        $hsl = self::hexToHsl($hex);
        $hsl[2] = max(0, $hsl[2] - ($percent / 100));
        return self::hslToHex($hsl[0], $hsl[1], $hsl[2]);
    }

    /**
     * Convert HSL values to hex color.
     *
     * @param float $h Hue (0-360)
     * @param float $s Saturation (0-1)
     * @param float $l Lightness (0-1)
     * @return string Hex color
     */
    private static function hslToHex($h, $s, $l)
    {
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;
        
        if ($h < 60) {
            $r = $c; $g = $x; $b = 0;
        } elseif ($h < 120) {
            $r = $x; $g = $c; $b = 0;
        } elseif ($h < 180) {
            $r = 0; $g = $c; $b = $x;
        } elseif ($h < 240) {
            $r = 0; $g = $x; $b = $c;
        } elseif ($h < 300) {
            $r = $x; $g = 0; $b = $c;
        } else {
            $r = $c; $g = 0; $b = $x;
        }
        
        $r = round(($r + $m) * 255);
        $g = round(($g + $m) * 255);
        $b = round(($b + $m) * 255);
        
        return self::rgbToHex($r, $g, $b);
    }

    /**
     * Convert hex color to chroma value for OKLCH color space.
     *
     * @param string $hex Hex color (e.g., '#fab526')
     * @return float Chroma value
     */
    public static function hexToChroma($hex)
    {
        $oklch = self::hexToOklch($hex);
        return $oklch['c'];
    }

    /**
     * Convert hex color to lightness value for OKLCH color space.
     *
     * @param string $hex Hex color (e.g., '#fab526')
     * @return float Lightness value
     */
    public static function hexToLightness($hex)
    {
        $oklch = self::hexToOklch($hex);
        return $oklch['l'];
    }

    /**
     * Convert hex color to OKLCH values.
     *
     * @param string $hex Hex color (e.g., '#fab526')
     * @return array OKLCH values as [l, c, h]
     */
    public static function hexToOklch($hex)
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        
        // Convert RGB to linear RGB (sRGB to linear RGB)
        $r = $r <= 0.04045 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.04045 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.04045 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);
        
        // Convert to XYZ (sRGB to XYZ matrix)
        $x = $r * 0.4124564 + $g * 0.3575761 + $b * 0.1804375;
        $y = $r * 0.2126729 + $g * 0.7151522 + $b * 0.0721750;
        $z = $r * 0.0193339 + $g * 0.1191920 + $b * 0.9503041;
        
        // Convert XYZ to OKLab
        $l = 0.8189330101 * $x + 0.3618667424 * $y - 0.1288597137 * $z;
        $a = 0.0329845436 * $x + 0.9293118715 * $y + 0.0361456387 * $z;
        $b = 0.0482003018 * $x + 0.2643662691 * $y + 0.6338517070 * $z;
        
        // Apply cube root
        $l = pow($l, 1/3);
        $a = pow($a, 1/3);
        $b = pow($b, 1/3);
        
        // Convert OKLab to OKLCH
        $oklch_l = 0.2104542553 * $l + 0.7936177850 * $a - 0.0040720468 * $b;
        $oklch_a = 1.9779984951 * $l - 2.4285922050 * $a + 0.4505937099 * $b;
        $oklch_b = 0.0259040371 * $l + 0.7827717662 * $a - 0.8086757660 * $b;
        
        // Calculate chroma and hue
        $chroma = sqrt($oklch_a * $oklch_a + $oklch_b * $oklch_b);
        $hue = atan2($oklch_b, $oklch_a) * 180 / M_PI;
        
        // Normalize hue to 0-360 range
        if ($hue < 0) {
            $hue += 360;
        }
        
        return [
            'l' => round($oklch_l, 4),
            'c' => round($chroma, 4),
            'h' => round($hue, 3)
        ];
    }
}
