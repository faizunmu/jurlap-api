<?php

namespace App\Http\Controllers;

use App\Models\Files;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class FilesController extends Controller
{
    public function index()
    {
        try {
            $response = Files::all()->sortBy("date");

            return response()->json($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Activity failed to fetch", "statusCode" => Response::HTTP_BAD_REQUEST, "error" => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                "files" => "required|mimes:png,jpg|max:20000"
            ]);

            if ($validate->fails()) {
                return response()->json(["message" => $validate->errors()->first(), "statusCode" => Response::HTTP_FORBIDDEN], Response::HTTP_FORBIDDEN);
            }

            if ($request->hasFile('files')) {
                foreach ($request->files as $file) {
                    $name = rand(0, 999999999) . "." . $file->getClientOriginalExtension();
                    $file->move(public_path() . "/images/", $name);
                    $host = request()->getSchemeAndHttpHost();
                    $path = $host . "/images/" . $name;
                    Files::create(["parent_id" => $request->parent_id, "filename" => $name, "path" => $path]);
                }
            }

            return response()->json(["message" => "Upload files successfully", "statusCode" => Response::HTTP_ACCEPTED], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Upload files failed", "statusCode" => Response::HTTP_BAD_REQUEST, "error" => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(Request $request)
    {
        try {
            $find = Files::where($request->id)->where($request->parent_id)->first()->get();

            Files::where($request->id)->where($request->parent_id)->first()->delete();

            if ($res = File::exists(public_path() . "/images/" . $find[0]->filename)) {
                File::delete(public_path() . "/images/" . $find[0]->filename);
            } else {
                return response()->json(["message" => "Files not found", "statusCode" => Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
            }


            return response()->json(["message" => "Files deleted successfully", "statusCode" => Response::HTTP_OK], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Delete files failed", "statusCode" => Response::HTTP_BAD_REQUEST, "error" => $th->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
