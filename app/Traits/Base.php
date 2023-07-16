<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Image;

trait Base
{
    /**
     * @param string $message
     * @param array<string, mixed> $data
     * @param string $code
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public static function error($message, $data = [],  $code = "error", $error = [], string $type = null)
    {
        return response()->json([
            'success' => false,
            'code' => 400,
            'message' => $message,
            'data' => $error,
            'type' => $type
        ]);
    }

    /**
     * @param string $message
     * @param array<string, mixed> $data
     * @param string $code
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public static function success(string $message, $data = [], string $code = "success", string $type = null)
    {
        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => $message,
            'data' => $data,
            'type' => $type
        ]);
    }

    public static function validation($validator)
    {
        // if ($validator->fails()) return Base::error($validator->errors()->first(), $validator->errors());
        return response()->json([
            'success' => false,
            'code' => 400,
            'message' => $validator->errors()->first(),
            'data' => $validator->errors(),
            'type' => 'validation'
        ]);
    }
    public static function exception($e)
    {
        // return Base::error($e->getMessage()." ".$e->getFile()." ".$e->getLine());

        $exception_msg = $e->getMessage() . " " . $e->getFile() . " " . $e->getLine();

        error_log("⚠️⚠️⚠️⚠️⚠️⚠️: " . $exception_msg);

        // $msg = 'An error occurred!!'; // ! For Production
        $msg = $exception_msg; // ! For Development
        // $msg = GlobalTrait::translate($msg); //! translated message

        return response()->json([
            'success' => false,
            'code' => 400,
            'message' => $msg,
            'data' => $e,
            'type' => 'exception'
        ]);
    }

    public static function pass($msg = 'Task successful!!', $data = [])
    {
        // $msg = GlobalTrait::translate($msg);
        return (object) collect([
            'success' => true,
            'message' => $msg,
            'data' => $data
        ])->all();
    }

    public static function fail($msg = 'An error occurred!!', $error = [], $err_type = null)
    {
        // $msg = GlobalTrait::translate($msg);
        return (object) collect([
            'success' => false,
            'message' => $msg,
            'data' => $error
        ])->all();
    }

    public static function exception_fail($e)
    {
        $exception_msg = $e->getMessage() . " " . $e->getFile() . " " . $e->getLine();

        error_log("⚠️⚠️⚠️⚠️⚠️⚠️: " . $exception_msg);

        $msg = $exception_msg; // ! For Development
        // $msg = GlobalTrait::translate($msg); //! for translation

        return (object) collect([
            'success' => false,
            'code' => 400,
            'message' => $msg,
            'data' => $e,
            'type' => 'exception'
        ])->all();
    }

    public static function now()
    {
        return Carbon::now();
    }

    public static function carbonDateParse($date)
    {
        return Carbon::parse($date)->isoFormat('D MMM, YYYY');
    }

    public static function timeParse($time)
    {
        return Carbon::parse($time)->format('H:i');
    }

    public static function carbonParse($date)
    {
        return Carbon::parse($date);
    }

    public static function dateTimeConcat($date, $time)
    {

        return Carbon::parse($date . ' ' . $time);
    }

    // public static function generateUDID()
    // {
    //     return sprintf(
    //         '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    //         mt_rand(0, 0xffff),
    //         mt_rand(0, 0xffff),
    //         mt_rand(0, 0xffff),
    //         mt_rand(0, 0x0fff) | 0x4000,
    //         mt_rand(0, 0x3fff) | 0x8000,
    //         mt_rand(0, 0xffff),
    //         mt_rand(0, 0xffff),
    //         mt_rand(0, 0xffff)
    //     );
    // }

    // public static function imageUpload($image, $model)
    // {
    //     $uuid = (string) Str::uuid();
    //     $image = $image; // image base64 encoded
    //     preg_match(
    //         "/data:image\/(.*?);/",
    //         $image,
    //         $image_extension
    //     ); // extract the image extension
    //     $image = preg_replace(
    //         '/data:image\/(.*?);base64,/',
    //         '',
    //         $image
    //     ); // remove the type part
    //     $image = str_replace(' ', '+', $image);
    //     $imageName = $model . '_' . time() . '_' . $uuid . '.' . $image_extension[1]; //generating unique file name;
    //     $file = base64_decode($image);

    //     $imageFile = Image::make($file);

    //     $imageFile->resize(512, 512, function ($constraint) {
    //         $constraint->aspectRatio();
    //     });

    //     // $imageFile->save('/images/' . $model . '/' . $imageName);

    //     $newPath = public_path() . '/images/' . $model;
    //     if (!file_exists($newPath)) {
    //         mkdir($newPath, 0755, true);
    //     }

    //     $imageFile->save(public_path() . '/images/' . $model . '/' . $imageName);

    //     return 'images/' . $model . '/' . $imageName;
    // }
    // public static function imageUploadWeb($image, $model, $user_image = null)
    // {
    //     // $imageFile->save('/images/' . $model . '/' . $imageName);

    //     $original_name = $image->getClientOriginalName();
    //     $rnd_ext = rand(100, 9999);
    //     $name = time() . '_' . $rnd_ext . '_' . $original_name;

    //     $newPath = public_path() . '/uploads/images/' . $model;
    //     if (!file_exists($newPath)) {
    //         mkdir($newPath, 0755, true);
    //     }
    //     $image->move('uploads/images/', $name);
    //      $image_name = 'uploads/images/' . $name;

    //      if ($user_image) {
    //         $user_image = public_path() . '/' . $user_image;
    //         if (file_exists($user_image)) {
    //             unlink($user_image);
    //         }
    //     }

    //     return $image_name;
    // }

    // public static function convertDaysToMonthsYears($days)
    // {
    //     $years = floor($days / 365);
    //     $months = floor(($days % 365) / 30);

    //     return array('years' => $years, 'months' => $months);
    // }
}
