<?php

namespace App\Http\Controllers;

use App\Fileupload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use JWTAuth;

class FileuploadController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     * Filetypes
     * 0 timesheet
     * 1 confimration
     * 2 travel exp
     * 3 other exp
     * 4 company conf
     * 5 company certs
     * 6 user other files from finance
     * 7 sellers logo
     *
     * Sources
     * 0 user
     * 1 invoice
     * 2 sellers
     */
    public function store(Request $request)
    {
        /**
         * Folder for source of files
         */
        $source_folder = '';
        switch ($request->source === '0') {
            case 0:
                $source_folder = 'user_files';
                break;
            case 1:
                $source_folder = 'invoices_files';
                break;
            case 2:
                $source_folder = 'sellers';
                break;
        }

        /**
         * Target user id
         */
        $source_id = $request->source_id;

        /**
         * Type of source
         */
        $source_type = '';

        switch ($request->type) {
            case 0:
                $source_type = 'timesheets';
                break;
            case 1:
                $source_type = 'confimrations';
                break;
            case 2:
                $source_type = 'travels';
                break;
            case 3:
                $source_type = 'others';
                break;
            case 4:
                $source_type = 'company';
                break;
            case 5:
                $source_type = 'certs';
                break;
            case 6:
                $source_type = 'order';
                break;
            case 7:
                $source_type = 'seller_logo';
                break;
        }

        /* path  to file */

        $originalName = $request->file('file')->getClientOriginalName();
        $fileNameForSave = uniqid('seargin.', true) . '.' . $request->file('file')->getClientOriginalName();

        $path = $request->file->storeAs('public/documents/' . $source_id . '/' . $source_folder . '/' . $source_type, $fileNameForSave);

        $url = Storage::url($path);

        /* create db record*/
        $fileupload = new Fileupload();
        $fileupload->original_name = $originalName;
        $fileupload->type = $request->type;
        $fileupload->filename = $url;
        $fileupload->path = $path;
        $fileupload->source = $request->source;
        $fileupload->source_id = $request->source_id;
        $fileupload->save();
        return response()->json(array('id' => $fileupload->id, 'original_name' => $originalName, 'filename' => $url), 200);
    }

    /**
     * @param Fileupload $fileupload
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Fileupload $fileupload)
    {
        Storage::delete($fileupload->path);
        $fileupload->delete();
        return response()->json(array('status' => 'success', 'id' => $fileupload->id), 200);
    }
}
