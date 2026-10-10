<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Signature extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(SignatureAuditLog::class);
    }

    /**
     * The image's MIME type, inferred from its stored file extension (drawn signatures
     * are always PNG; uploaded ones may be PNG or JPEG).
     */
    public function mimeType(): string
    {
        return match (strtolower(pathinfo($this->image_path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'image/png',
        };
    }

    /**
     * Whether the given user has a real, usable (non-blank) signature registered.
     */
    public static function isUsableFor(int $userId): bool
    {
        $signature = self::where('user_id', $userId)->first();

        return $signature !== null && ! $signature->isBlank();
    }

    /**
     * Whether this signature's stored file is blank. A blank signature was never really
     * "drawn", so it doesn't count as a final, usable signature (it may only exist because
     * it was saved before this validation was introduced).
     */
    public function isBlank(): bool
    {
        $contents = Storage::disk('public')->get($this->image_path);

        if ($contents === null) {
            return true;
        }

        return self::pngIsBlank($contents);
    }

    /**
     * Determine whether a canvas-exported PNG has no visible (non-transparent) pixels.
     * Decodes the PNG by hand (IHDR + IDAT, RFC 2083 filtering) since the GD extension
     * isn't available; the browser's Canvas#toDataURL('image/png') always produces a
     * non-interlaced, 8-bit RGBA PNG, so only that format needs to be handled.
     */
    public static function pngIsBlank(string $pngData): bool
    {
        if (substr($pngData, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            return false;
        }

        $offset = 8;
        $width = null;
        $height = null;
        $colorType = null;
        $idat = '';

        while ($offset + 8 <= strlen($pngData)) {
            $length = unpack('N', substr($pngData, $offset, 4))[1];
            $type = substr($pngData, $offset + 4, 4);
            $data = substr($pngData, $offset + 8, $length);

            if ($type === 'IHDR') {
                $width = unpack('N', substr($data, 0, 4))[1];
                $height = unpack('N', substr($data, 4, 4))[1];
                $colorType = ord($data[9]);
            } elseif ($type === 'IDAT') {
                $idat .= $data;
            } elseif ($type === 'IEND') {
                break;
            }

            $offset += 8 + $length + 4;
        }

        if (! $width || ! $height || $idat === '' || $colorType !== 6) {
            return false;
        }

        $raw = @gzuncompress($idat);

        if ($raw === false) {
            return false;
        }

        $bytesPerPixel = 4;
        $stride = $width * $bytesPerPixel;
        $prevRow = str_repeat("\0", $stride);
        $pos = 0;
        $visibilityThreshold = 10;

        for ($y = 0; $y < $height; $y++) {
            if ($pos >= strlen($raw)) {
                break;
            }

            $filterType = ord($raw[$pos]);
            $pos++;
            $row = substr($raw, $pos, $stride);
            $pos += $stride;

            if (strlen($row) < $stride) {
                break;
            }

            $unfiltered = self::unfilterPngRow($filterType, $row, $prevRow, $bytesPerPixel);

            for ($i = 3; $i < $stride; $i += 4) {
                if (ord($unfiltered[$i]) > $visibilityThreshold) {
                    return false;
                }
            }

            $prevRow = $unfiltered;
        }

        return true;
    }

    /**
     * Reverse PNG scanline filtering (RFC 2083 §6) for a single row.
     */
    private static function unfilterPngRow(int $filterType, string $row, string $prevRow, int $bpp): string
    {
        $len = strlen($row);
        $out = str_repeat("\0", $len);

        for ($i = 0; $i < $len; $i++) {
            $x = ord($row[$i]);
            $a = $i >= $bpp ? ord($out[$i - $bpp]) : 0;
            $b = ord($prevRow[$i]);
            $c = $i >= $bpp ? ord($prevRow[$i - $bpp]) : 0;

            $value = match ($filterType) {
                1 => $x + $a,
                2 => $x + $b,
                3 => $x + intdiv($a + $b, 2),
                4 => $x + self::paethPredictor($a, $b, $c),
                default => $x,
            };

            $out[$i] = chr($value & 0xFF);
        }

        return $out;
    }

    private static function paethPredictor(int $a, int $b, int $c): int
    {
        $p = $a + $b - $c;
        $pa = abs($p - $a);
        $pb = abs($p - $b);
        $pc = abs($p - $c);

        if ($pa <= $pb && $pa <= $pc) {
            return $a;
        }

        return $pb <= $pc ? $b : $c;
    }
}
