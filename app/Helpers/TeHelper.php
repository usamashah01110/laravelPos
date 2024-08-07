<?php


use App\Helpers\ApiResponseHelper;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function get_admin_slugs()
{
    $_pages = [
        'users' => 'UserController',
    ];
    return $_pages;
}

function get_user_slugs()
{
    $_pages = [
        'clinics' => 'ClinicController',
        'services' => 'ServiceController',
        'bookings' => 'BookingController',
        'teams' => 'TeamController',
        'users' => 'UserController',
        'userprofiles' => 'userProfileController',
    ];
    return $_pages;
}

function get_singular($resource)
{
    return Str::singular($resource);
}

function get_plural($resource)
{
    return Str::plural($resource);
}

function get_s3_Client()
{
    return new S3Client([
        'version' => 'latest',
        'region'  => env('AWS_DEFAULT_REGION'),
        'credentials' => [
            'key'    => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ],
    ]);
}


function store_images_in_S3($images)
{
    $s3Client = get_s3_Client();
    $bucket = env('AWS_BUCKET');
    $imageUrls = [];
    foreach ($images as $file) {

        if ($file->isValid()) {
            $fileName = $file->getClientOriginalName();
            $directory = 'clinics/images';
            $key = $directory . '/' . $fileName;

            try {
                // Upload the file to S3
                $result = $s3Client->putObject([
                    'Bucket' => $bucket,
                    'Key' => $key,
                    'SourceFile' => $file->getRealPath(),
                ]);

                // Store the S3 path
                $imageUrls[] = $result->get('ObjectURL');
            } catch (Aws\Exception\S3Exception $e) {
                // Handle the exception
                echo "There was an error uploading the file: " . $e->getMessage();
            }
        }
    }
    return json_encode($imageUrls);

}






