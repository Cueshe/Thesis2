<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class TTSController extends Controller
{
    /**
     * Generate text-to-speech audio for Tagalog/Filipino text
     */
    public function speak(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:500',
            'lang' => 'nullable|string|in:tl,en'
        ]);

        $text = $request->input('text');
        $lang = $request->input('lang', 'tl'); // Default to Tagalog
        
        // Encode the text for URL
        $encodedText = urlencode($text);
        
        // Use Google Translate TTS API
        // For Tagalog, use 'tl' language code to get native Tagalog voice
        $ttsUrl = "https://translate.google.com/translate_tts?ie=UTF-8&tl={$lang}&client=gtx&q={$encodedText}";
        
        try {
            $headers = [
                // Google TTS is more reliable when requests look like a real browser
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Referer' => 'https://translate.google.com/',
                'Accept' => 'audio/mpeg,audio/*;q=0.9,*/*;q=0.8',
                'Accept-Language' => $lang === 'tl' ? 'tl-PH,tl;q=0.9,en-US;q=0.8,en;q=0.7' : 'en-US,en;q=0.9',
            ];

            // Fetch the audio from Google TTS
            $response = Http::withHeaders($headers)->timeout(10)->get($ttsUrl);
            
            if ($response->successful()) {
                // Return the audio file with proper headers
                return response($response->body(), 200)
                    ->header('Content-Type', 'audio/mpeg')
                    ->header('Content-Disposition', 'inline; filename="pronunciation.mp3"')
                    ->header('Cache-Control', 'public, max-age=3600');
            }
            
            // If first method fails, try alternative endpoint
            $ttsUrl2 = "https://translate.google.com/translate_tts?ie=UTF-8&tl={$lang}&client=tw-ob&q={$encodedText}";
            $response2 = Http::withHeaders($headers)->timeout(10)->get($ttsUrl2);
            
            if ($response2->successful()) {
                return response($response2->body(), 200)
                    ->header('Content-Type', 'audio/mpeg')
                    ->header('Content-Disposition', 'inline; filename="pronunciation.mp3"')
                    ->header('Cache-Control', 'public, max-age=3600');
            }
            
            return response()->json([
                'error' => 'Unable to generate speech'
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'TTS service unavailable: ' . $e->getMessage()
            ], 500);
        }
    }
}

