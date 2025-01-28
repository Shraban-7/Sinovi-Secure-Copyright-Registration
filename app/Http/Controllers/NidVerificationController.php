<?php

namespace App\Http\Controllers;

use App\Models\NidVerification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use thiagoalessio\TesseractOCR\TesseractOCR;

class NidVerificationController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'nid_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nid_number' => 'required|string',
        ]);

        $user = Auth::user();
        $image = $request->file('nid_image');
        $inputNidNumber = trim($request->input('nid_number'));

        // Check if the user already has an NID verification
        if ($this->userHasNidVerification($user->email)) {
            return redirect()->route('dashboard')->with('error', 'You have already uploaded an NID. Only one NID per user is allowed.');
        }

        // Check if the NID number already exists
        if ($this->nidExists($inputNidNumber)) {
            return redirect()->route('dashboard')->with('error', 'This NID has already been verified and uploaded.');
        }

        try {
            // Perform OCR on the uploaded image
            $ocr = new TesseractOCR($image->path());
            $ocr->executable('C:\\Program Files\\Tesseract-OCR\\tesseract.exe');
            $extractedText = $ocr->run();

            // Define pattern for NID extraction
            $pattern_id = '/(\d{3})\s+(\d{3})\s+(\d{4})/';

            // Extract NID number from OCR result
            preg_match($pattern_id, $extractedText, $nidMatches);
            $extractedNidNumber = isset($nidMatches[1], $nidMatches[2], $nidMatches[3])
                ? "$nidMatches[1]$nidMatches[2]$nidMatches[3]"
                : null;

            // dd($extractedNidNumber);

            // Handle NID verification
            if ($extractedNidNumber && $extractedNidNumber === $inputNidNumber) {
                $censoredImagePath = nidCensoredImage($image, 'nid', $inputNidNumber);
                // dd($censoredImagePath);
                // Save verified NID data to the database
                $this->saveVerificationData($inputNidNumber, $image, $censoredImagePath, $user->email);

                return redirect()->route('dashboard')->with([
                    'success' => 'NID verification successful.',
                    'extractedNidNumber' => $extractedNidNumber,
                ]);
            } else {
                return redirect()->route('dashboard')->with([
                    'error' => 'Verification failed. NID number does not match.',
                    'extractedNidNumber' => $extractedNidNumber ?? 'No NID detected',
                ]);
            }
        } catch (Exception $e) {
            dd('NID Verification Error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'An error occurred during verification. Please try again.');
        }
    }

    private function userHasNidVerification($email)
    {
        return NidVerification::where('email', $email)->exists();
    }

    private function nidExists($nidNumber)
    {
        $nidVerifications = NidVerification::all();
        foreach ($nidVerifications as $verification) {
            $decryptedNid = $this->aesDecrypt($verification->nid_number);
            if ($decryptedNid === $nidNumber) {
                return true;
            }
        }
        return false;
    }

    private function saveVerificationData($inputNidNumber,$image, $censoredImagePath, $email)
    {
        try {
            $savedImageInfo = $this->saveImage($image);
            NidVerification::updateOrCreate(
                ['email' => $email],
                [
                    'nid_number' => $this->aesEncrypt($inputNidNumber),
                    'encrypted_image' => $this->aesEncrypt($savedImageInfo['name']),
                    'censored_image_path' => $censoredImagePath,
                ]
            );

            return [
                'image_name' => $savedImageInfo['name'],
                'image_path' => $savedImageInfo['path'],
            ];
        } catch (Exception $e) {
            Log::error('Error in saveVerificationData: ' . $e->getMessage());
            throw $e;
        }
    }

    private function saveImage($image)
    {
        try {
            $uniqueId = Str::uuid()->toString();
            $timestamp = now()->format('YmdHis');
            $extension = $image->getClientOriginalExtension();
            $imageName = "{$uniqueId}_{$timestamp}.{$extension}";
            $imagePath = 'uploads/' . $imageName;
            if ($image->move(public_path('uploads'), $imageName)) {
                return [
                    'name' => $imageName,
                    'path' => $imagePath,
                ];
            } else {
                Log::error('Failed to move uploaded image: ' . $imageName);
                return null;
            }
        } catch (Exception $e) {
            Log::error('Error saving image: ' . $e->getMessage());
            return null;
        }
    }

    private function aesEncrypt($data)
    {
        $encryptionKey = env('AES_ENCRYPTION_KEY');
        $cipher = "AES-256-CBC";
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $encryptedData = openssl_encrypt($data, $cipher, $encryptionKey, 0, $iv);
        return base64_encode($encryptedData . '::' . base64_encode($iv));
    }

    private function aesDecrypt($encryptedData)
    {
        $encryptionKey = env('AES_ENCRYPTION_KEY');
        $cipher = "AES-256-CBC";
        list($encryptedData, $iv) = explode('::', base64_decode($encryptedData), 2);
        return openssl_decrypt($encryptedData, $cipher, $encryptionKey, 0, base64_decode($iv));
    }
}
