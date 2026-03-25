<?php

namespace App\Helpers;

class Helper
{
    public static function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
            return null;
        }
        $earthRadius = 6371; // Radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return $distance; // in kilometers
    }

    public static function compressVideo($inputPath, $outputPath)
    {
        try {
            // Check if ffmpeg is likely to be available
            $ffmpegPath = env('FFMPEG_PATH', 'ffmpeg');
            
            // Basic check for Windows/Linux
            $command = (PHP_OS_FAMILY === 'Windows') ? "where $ffmpegPath" : "which $ffmpegPath";
            exec($command, $output, $returnVar);
            
            if ($returnVar !== 0) {
                // FFMPEG not in path, return false to let controller handle fallback
                \Log::warning("FFMPEG binary not found at: " . $ffmpegPath);
                return false;
            }

            $ffmpeg = \FFMpeg\FFMpeg::create([
                'ffmpeg.binaries'  => $ffmpegPath,
                'ffprobe.binaries' => env('FFPROBE_PATH', 'ffprobe'),
                'timeout'          => 600,
                'ffmpeg.threads'   => 4,
            ]);

            $video = $ffmpeg->open($inputPath);
            $format = new \FFMpeg\Format\Video\X264();
            $format->setVideoCodec('libx264')
                   ->setKiloBitrate(1200) // Compressed bitrate for ~5MB for 30s
                   ->setAudioCodec('aac');

            // Resize to max 480p width to save size while keeping quality decent for proof
            $video->filters()->resize(new \FFMpeg\Coordinate\Dimension(854, 480))->synchronize();

            $video->save($format, $outputPath);
            return true;
        } catch (\Exception $e) {
            \Log::error("FFMPEG Compression Error: " . $e->getMessage());
            return false;
        }
    }
}

// Global helper functions (outside the namespace/class)
use App\Models\Cart;

function totalCartItems() {
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user_id = \Illuminate\Support\Facades\Auth::user()->id;
        $totalCartItems = Cart::where('user_id', $user_id)->sum('quantity');
    } else {
        $session_id = \Illuminate\Support\Facades\Session::get('session_id');
        $totalCartItems = Cart::where('session_id', $session_id)->sum('quantity');
    }
    return $totalCartItems;
}

function getCartItems() {
    if (\Illuminate\Support\Facades\Auth::check()) {
        $getCartItems = Cart::with([
            'product' => function ($query) {
                $query->select('id', 'category_id', 'product_name', 'product_image');
            }
        ])->orderBy('id', 'Desc')->where([
            'user_id'    => \Illuminate\Support\Facades\Auth::user()->id
        ])->get()->toArray();
    } else {
        $getCartItems = Cart::with([
            'product' => function ($query) {
                $query->select('id', 'category_id', 'product_name', 'product_image');
            }
        ])->orderBy('id', 'Desc')->where([
            'session_id' => \Illuminate\Support\Facades\Session::get('session_id')
        ])->get()->toArray();
    }
    return $getCartItems;
}
