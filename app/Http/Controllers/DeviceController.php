<?php

namespace App\Http\Controllers;

use App\Device;
use Illuminate\Http\Request;
use JWTAuth;

class DeviceController extends Controller
{
    public function update(Request $request, Device $device)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'device_id' => 'required',
            'device_name' => 'required',
        ]);

        // handle fields
        $device->update($request->toArray());

        return response()->json(array('data' => $device), 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'device_id' => 'required',
            'device_name' => 'required',
        ]);

        $device = Device::create([
            'user_id' => $request->user_id,
            'device_id' => $request->device_id,
            'device_name' => $request->device_name
        ]);

        return response()->json(array('data' => $device->id), 200);
    }

    public function destroy(Request $request, Device $device)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $device->delete();

        return response()->json(null, 204);
    }

}