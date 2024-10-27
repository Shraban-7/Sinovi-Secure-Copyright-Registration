<?php

namespace App\Http\Controllers;

use App\Models\NidVerification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\ImageManager;
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

        $image = $request->file('nid_image');
        $inputNidNumber = $request->input('nid_number');

        try {
            // Perform OCR on the uploaded image
            $ocr = new TesseractOCR($image->path());
            $ocr->executable('C:\\Program Files\\Tesseract-OCR\\tesseract.exe'); // Adjust path if necessary
            $extractedText = $ocr->run();

            // Define patterns for NID and Passport extraction
            $pattern_id = '/IDNO\s*:\s*(\d+)/';
            $pattern_passport = '/Passport No\.\s*([A-Z]{2}\d{7})/';

            // Extract NID and Passport numbers from OCR result
            preg_match($pattern_id, $extractedText, $nidMatches);
            $extractedNidNumber = $nidMatches[1] ?? null;

            preg_match($pattern_passport, $extractedText, $passportMatches);
            $extractedPassportNumber = $passportMatches[1] ?? null;

            // Handle NID and Passport verification cases
            if ($extractedNidNumber && $extractedNidNumber === $inputNidNumber) {
                $censoredImagePath = $this->createCensoredImage($image, 'nid', $extractedNidNumber);

                // Save verified NID data to the database
                NidVerification::create([
                    'nid_number' => $this->aesEncrypt($inputNidNumber),
                    'email' => Auth::user()->email,
                    'encrypted_image' => $this->aesEncrypt(file_get_contents($image->path())),
                    'censored_image_path' => $censoredImagePath, // Encrypting the image path
                ]);

                return redirect()->route('dashboard')->with([
                    'success' => 'NID verified and saved successfully.',
                    'extractedNidNumber' => $extractedNidNumber,
                ]);
            } elseif ($extractedPassportNumber && $extractedPassportNumber === $inputNidNumber) {
                $censoredImagePath = $this->createCensoredImage($image, 'passport', $extractedPassportNumber);

                // Save verified Passport data to the database
                NidVerification::create([
                    'nid_number' => $this->aesEncrypt($inputNidNumber),
                    'email' => Auth::user()->email,
                    'encrypted_image' => $this->aesEncrypt(file_get_contents($image->path())),
                    'censored_image_path' => $censoredImagePath, // Encrypting the image path
                ]);

                return redirect()->route('dashboard')->with([
                    'success' => 'Passport verified and saved successfully.',
                    'extractedPassportNumber' => $extractedPassportNumber,
                ]);
            } else {
                return redirect()->route('dashboard')->with([
                    'error' => 'Verification failed. Numbers do not match.',
                    'extractedNidNumber' => $extractedNidNumber ?? 'No NID detected',
                    'extractedPassportNumber' => $extractedPassportNumber ?? 'No Passport detected',
                ]);
            }
        } catch (Exception $e) {
            return redirect()->route('dashboard')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Create a censored image with the extracted NID/Passport number.
     */
    private function createCensoredImage($image, $type, $number)
    {
        $manager = new ImageManager(new Driver());
        $img = $manager->read($image->path());

        $width = $img->width();
        $height = $img->height();

        if ($type === 'nid') {
            // NID: Censor only after ID NO
            $leftOffset = intval($width * 0.09); // Adjust this value for left margin
            $bottomOffset = intval($height * 0.05); // Adjust this value for bottom margin

            $x = intval($width * 0.5) - $leftOffset; // Move left by leftOffset
            $y = intval($height * 0.8) + $bottomOffset; // Move down by bottomOffset
            $rectWidth = intval($width * 0.45);
            $rectHeight = intval($height * 0.1);

            $img->drawRectangle($x, $y, function (RectangleFactory $rectangle) use ($rectWidth, $rectHeight) {
                $rectangle->size($rectWidth, $rectHeight);
                $rectangle->background('#000000');
            });

        } else {
            // Passport: Censor top right and bottom full
            // Top right rectangle
            $topMargin = intval($height * 0.1);

            $x1 = intval($width * 0.6);
            $y1 = 0.3 + $topMargin;
            $rectWidth1 = intval($width * 0.3);
            $rectHeight1 = intval($height * 0.3);

            $img->drawRectangle($x1, $y1, function (RectangleFactory $rectangle) use ($rectWidth1, $rectHeight1) {
                $rectangle->size($rectWidth1, $rectHeight1);
                $rectangle->background('#000000');
            });

            // Bottom full rectangle
            $x2 = 0;
            $y2 = intval($height * 0.8);
            $rectWidth2 = $width;
            $rectHeight2 = intval($height * 0.2);

            $img->drawRectangle($x2, $y2, function (RectangleFactory $rectangle) use ($rectWidth2, $rectHeight2) {
                $rectangle->size($rectWidth2, $rectHeight2);
                $rectangle->background('#000000');
            });
        }

        $censoredImagePath = 'uploads/' . time() . '_censored.jpg';

        $img->encodeByPath(public_path($censoredImagePath), progressive: true, quality: 100);
        $img->save(public_path($censoredImagePath));

        return $censoredImagePath;
    }

    /**
     * Encrypt the given data using AES-256 encryption.
     */
    private function aesEncrypt($data)
    {
        $encryptionKey = env('AES_ENCRYPTION_KEY'); // Set your encryption key in the .env file
        $cipher = "AES-256-CBC"; // AES encryption with CBC mode
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)); // Generate an initialization vector
        $encryptedData = openssl_encrypt($data, $cipher, $encryptionKey, 0, $iv); // Encrypt the data

        // Combine encrypted data with IV for storage or transmission
        return base64_encode($encryptedData . '::' . base64_encode($iv)); // Store as base64
    }
}
