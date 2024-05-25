<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use lastguest\Murmur;

class FPController extends Controller
{
    public function checkFingerprint(Request $request)
    {
        $recordID = $request->query('recordID');
        if (!$recordID) {
            return response()->json(['error' => 'recordID is required'], 400);
        }

        Log::error('Check fingerprint started at: ' . now());

        $fingerprint = $this->checkIfFingerprintExists($recordID);

        Log::error('Check fingerprint ended at: ' . now());

        return response()->json(['fingerprint' => $fingerprint]);
    }

    public function finishPage(Request $request)
    {
        $recordID = $request->input('recordID');
        $fingerprint = $this->genFingerprint($recordID);
        $response = $this->checkIfFingerprintExists($fingerprint);
        return response()->json(['fingerprint' => $response]);
    }

    public function getCookie(Request $request)
    {
        $IP = $request->ip();
        $uniqueLabel = sha1($IP . now());

        $cookie = $request->input('cookie', '');
        $exists = DB::table('cookies')->where('cookie', $cookie)->exists();

        if (!$exists) {
            $cookie = $uniqueLabel;
            DB::table('cookies')->insert(['cookie' => $cookie]);
        }

        $this->doInit($uniqueLabel, $cookie);
        return "$uniqueLabel,$cookie";
    }

    public function checkExistPicture(Request $request)
    {
        $hashValue = $request->input('hash_value', '');
        $exists = DB::table('pictures')->where('dataurl', $hashValue)->exists();

        return $exists ? '1' : '0';
    }

    public function storePictures(Request $request)
    {
        $imageB64 = $request->input('imageBase64', '');
        $hashValue = sha1($imageB64);

        DB::table('pictures')->insert(['dataurl' => $hashValue]);

        $imageB64 = preg_replace('/^data:image\/\w+;base64,/', '', $imageB64);
        $imageData = base64_decode($imageB64);
        $imagePath = "pictures/{$hashValue}.png";
        Storage::disk('public')->put($imagePath, $imageData);

        return $hashValue;
    }

    public function updateFeatures(Request $request)
    {
        $result = $request->all();
        $uniqueLabel = $result['uniquelabel'];
        unset($result['uniquelabel']);

        foreach ($result as $key => $value) {
            if ($key === 'cpu_cores') {
                $result[$key] = (int)$value;
            }
        }

        $fingerprint = $this->doUpdateFeatures($uniqueLabel, $result);
        return response()->json(['finished' => array_keys($result), 'fingerprint' => $fingerprint]);
    }

     // Method to check if the fingerprint exists
     public function checkIfFingerprintExists($fingerprint)
     {
        if (Cache::has($fingerprint)) {
            return response()->json(Cache::get($fingerprint));
        }
        $result = DB::table('features')
            ->where('browserfingerprint', $fingerprint)
            ->first();
        if ($result) {
            Cache::put($fingerprint, $result, now()->addMinutes(10));
            return response()->json($result);
        }
        return response()->json(null);
     }

     public function genFingerprint($recordID)
     {
         Log::error('Gen fingerprint started at: ' . now());

         // List of features
        $featureList = [
            "agent",
            "accept",
            "encoding",
            "language",
            "langsDetected",
            "resolution",
            "jsFonts",
            "WebGL",
            "inc",
            "gpu",
            "gpuimgs",
            "timezone",
            "plugins",
            "cookie",
            "localstorage",
            "adBlock",
            "cpucores",
            "canvastest",
            "audio",
            "ccaudio",
            "hybridaudio",
            "touchSupport",
            "doNotTrack",
            "fp2_colordepth",
            "fp2_sessionstorage",
            "fp2_indexdb",
            "fp2_addbehavior",
            "fp2_opendatabase",
            "fp2_cpuclass",
            "fp2_pixelratio",
            "fp2_platform",
            "fp2_liedlanguages",
            "fp2_liedresolution",
            "fp2_liedos",
            "fp2_liedbrowser",
            "fp2_webgl",
            "fp2_webglvendoe"
        ];

        $featureStr = implode(",", $featureList);
        $sqlStr = "SELECT $featureStr FROM features WHERE uniquelabel = ?";
        $res = DB::select($sqlStr, [$recordID]);

        if (empty($res)) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $resStr = json_encode($res[0]);

        $fingerprintHex= Murmur::hash3($resStr);
        Log::error('Gen fingerprint ended at: ' . now());

        $updateSql = "UPDATE features SET browserfingerprint = ? WHERE uniquelabel = ?";
        DB::update($updateSql, [$fingerprintHex, $recordID]);

        return $fingerprintHex;
     }

     public function doInit($uniqueLabel, $cookie)
    {
        $result = [];
        $agent = $accept = $encoding = $language = $IP = $keys = "";

        try {
            $agent = request()->header('User-Agent', '');
            $accept = request()->header('Accept', '');
            $encoding = request()->header('Accept-Encoding', '');
            $language = request()->header('Accept-Language', '');
            $keys = implode('_', request()->headers->keys());
            $IP = request()->ip();
        } catch (\Exception $e) {
            Log::error("Error processing headers: " . $e->getMessage());
        }

        // Create a new record in the features table
        $sqlStr = "INSERT INTO features (uniquelabel, IP) VALUES (?, ?)";
        DB::insert($sqlStr, [$uniqueLabel, $IP]);

        // Update the statistics
        $result['agent'] = $agent;
        $result['accept'] = $accept;
        $result['encoding'] = $encoding;
        $result['language'] = $language;
        $result['label'] = $cookie;
        $result['httpheaders'] = $keys;

        return $this->doUpdateFeatures($uniqueLabel, $result);
    }

    private function doUpdateFeatures($uniqueLabel, $data)
    {
        Log::error('Inside doUpdateFeatures');

        // Build the SQL update string dynamically based on the data provided
        $updateParts = array_map(function($key) {
            return "$key = ?";
        }, array_keys($data));

        $updateStr = implode(", ", $updateParts);
        $sqlValues = array_values($data);
        $sqlValues[] = $uniqueLabel;

        // Prepare and execute the SQL update statement
        $sqlStr = "UPDATE features SET $updateStr WHERE uniquelabel = ?";
        DB::update($sqlStr, $sqlValues);
        Log::error('Updating features completed');

        // Regenerate the fingerprint after updating the features
        return $this->genFingerprint($uniqueLabel);
    }


}
