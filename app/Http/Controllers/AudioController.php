<?php

namespace App\Http\Controllers;

use App\Models\AudioFile;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AudioController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'audio' => 'required|file|mimetypes:audio/mpeg,audio/mp3'
            ]);

            $file = $request->file('audio');
            $client = new Client();
            
            $response = $client->request(
                'POST',
                config('services.bytescale.base_url') . '/v2/accounts/' . config('services.bytescale.account_id') . '/uploads/form_data',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . config('services.bytescale.api_key'),
                    ],
                    'multipart' => [
                        [
                            'name' => 'file',
                            'contents' => fopen($file->getRealPath(), 'r'),
                            'filename' => $file->getClientOriginalName(),
                        ],
                        [
                            'name' => 'path',
                            'contents' => '/audio',
                        ],
                    ],
                ]
            );

            $data = json_decode($response->getBody(), true);
            $fileData = $data['files'][0];

            $audio = AudioFile::create([
                'title'          => $request->title,
                'file_path'      => $fileData['fileUrl'],
                'bytescale_id'   => $fileData['etag'],
                'bytescale_path' => $fileData['filePath'],
                'user_id'        => auth()->id(),
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Audio uploaded successfully',
                'data'    => $audio
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Audio files retrieved successfully',
            'data' => AudioFile::all()
        ], 200);
    }

    public function listFromBytescale()
    {
        try {
            $client = new Client();

            $response = $client->request(
                'POST',
                config('services.bytescale.base_url') . '/v2/accounts/' . config('services.bytescale.account_id') . '/search/files',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . config('services.bytescale.api_key'),
                        'Content-Type'  => 'application/json',
                    ],
                    'body' => json_encode([
                        'path'  => '/audio',
                        'limit' => 50,
                    ]),
                ]
            );

            $data = json_decode($response->getBody(), true);

            return response()->json([
                'status' => true,
                'message' => 'Bytescale audio files retrieved successfully',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve files from Bytescale: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $audio = AudioFile::find($id);

        if (!$audio) {
            return response()->json([
                'status' => false,
                'message' => 'Audio not found'
            ], 404);
        }

        $audio->delete();

        return response()->json([
            'status' => true,
            'message' => 'Audio deleted successfully'
        ], 200);
    }
}