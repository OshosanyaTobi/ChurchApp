<?php

namespace App\Http\Controllers;

use App\Models\AudioFile;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AudioController extends Controller
{
    // Admin Upload Audio
    public function store(Request $request)
    {
        return "Hello";
        $request->validate([
            'title' => 'required|string',
            'audio' => 'required|file|mimetypes:audio/mpeg,audio/mp3'
        ]);

        $file = $request->file('audio');

        $client = new Client();

        $response = $client->request('POST',
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

        $audio = AudioFile::create([
            'title'          => $request->title,
            'file_path'      => $data['fileUrl'] ?? null,
            'bytescale_id'   => $data['fileId'] ?? null,
            'bytescale_path' => $data['filePath'] ?? null,
            'user_id'        => auth()->id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Audio uploaded successfully',
            'data'    => $audio
        ], 201);
    }

    // Public — Everyone can view
    public function index()
    {
        return response()->json(AudioFile::all(['id', 'title', 'file_path', 'bytescale_path']), 200);
    }

    public function listFromBytescale()
    {
        $client = new Client();

        $response = $client->request('POST',
            config('services.bytescale.base_url') . '/v2/accounts/' . config('services.bytescale.account_id') . '/search/files',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.bytescale.api_key'),
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode([
                    'path'  => '/audio',
                    'limit' => 50, // adjust as needed
                ]),
            ]
        );

        $data = json_decode($response->getBody(), true);

        return response()->json($data, 200);
    }

    // Admin Delete
    public function destroy($id)
    {
        $audio = AudioFile::find($id);

        if (!$audio) {
            return response()->json(['message' => 'Audio not found'], 404);
        }

        // Optional: delete from Bytescale too
        // $client = new Client();
        // $client->delete(config('services.bytescale.base_url') . '/v2/accounts/' . config('services.bytescale.account_id') . '/files/' . $audio->bytescale_id, [
        //     'headers' => [
        //         'Authorization' => 'Bearer ' . config('services.bytescale.api_key'),
        //     ],
        // ]);

        $audio->delete();

        return response()->json(['message' => 'Audio deleted'], 200);
    }
}
