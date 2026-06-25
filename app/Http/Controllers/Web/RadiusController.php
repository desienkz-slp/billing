<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Services\RadiusService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RadiusController extends Controller
{
    protected RadiusService $radiusService;

    public function __construct(RadiusService $radiusService)
    {
        $this->radiusService = $radiusService;
    }

    /**
     * RADIUS Dashboard — list radius servers.
     */
    public function index()
    {
        // Only get Upluk Upluk API servers
        $server = Server::where('type', 'upluk_upluk_api')
            ->where('is_active', true)
            ->orderBy('name')
            ->first();

        if ($server) {
            return Inertia::render('Radius/ServerDetail', compact('server'));
        }

        return Inertia::render('Radius/Index'); // Or handle empty state if needed
    }

    /**
     * Page: Server Detail (Status, Users, Profiles)
     */
    public function serverDetail(Server $server)
    {
        return Inertia::render('Radius/ServerDetail', compact('server'));
    }

    // ==========================================================
    // API PROXIES FOR VUE FRONTEND
    // ==========================================================

    public function apiGetSessions(Server $server)
    {
        $this->radiusService->connectTo($server);
        return response()->json($this->radiusService->getActiveSessions());
    }

    public function apiGetConfig(Server $server)
    {
        $this->radiusService->connectTo($server);
        return response()->json($this->radiusService->getConfig());
    }

    public function apiGetUsers(Server $server)
    {
        $this->radiusService->connectTo($server);
        return response()->json($this->radiusService->listUsers());
    }

    public function apiStoreUser(Request $request, Server $server)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'profile' => 'nullable|string'
        ]);

        $this->radiusService->connectTo($server);
        $success = $this->radiusService->createUser($request->username, $request->password, $request->profile);

        if ($success) {
            return response()->json(['message' => 'User created successfully']);
        }
        return response()->json(['message' => 'Failed to create user'], 500);
    }

    public function apiDestroyUsers(Request $request, Server $server)
    {
        $request->validate([
            'usernames' => 'required|array'
        ]);

        $this->radiusService->connectTo($server);
        // Batch delete is supported if we add it, but our RadiusService currently only supports deleting 1 user or passing multiple.
        // Wait, RadiusService deleteUser takes a single username but sends it as array. Let's just loop for simplicity, or we can add batchDelete to service.
        // Since deleteUser deletes 1, let's just loop.
        $success = true;
        foreach ($request->usernames as $username) {
            if (!$this->radiusService->deleteUser($username)) {
                $success = false;
            }
        }

        if ($success) {
            return response()->json(['message' => 'Users deleted successfully']);
        }
        return response()->json(['message' => 'Some users failed to delete'], 500);
    }

    public function apiDisableUser(Server $server, $username)
    {
        $this->radiusService->connectTo($server);
        $success = $this->radiusService->disableUser($username);
        return response()->json(['success' => $success]);
    }

    public function apiEnableUser(Request $request, Server $server, $username)
    {
        $this->radiusService->connectTo($server);
        $success = $this->radiusService->enableUser($username, $request->password, $request->profile);
        return response()->json(['success' => $success]);
    }

    // --- PROFILES ---

    public function apiGetProfiles(Server $server)
    {
        $this->radiusService->connectTo($server);
        return response()->json($this->radiusService->listGroups());
    }

    public function apiStoreProfile(Request $request, Server $server)
    {
        $data = $request->validate([
            'groupname' => 'required|string',
            'attribute' => 'required|string',
            'op' => 'required|string',
            'value' => 'required|string'
        ]);

        $this->radiusService->connectTo($server);
        $success = $this->radiusService->createProfile($data);

        if ($success) {
            return response()->json(['message' => 'Profile created successfully']);
        }
        return response()->json(['message' => 'Failed to create profile'], 500);
    }

    public function apiUpdateProfile(Request $request, Server $server, $id)
    {
        $data = $request->validate([
            'groupname' => 'required|string',
            'attribute' => 'required|string',
            'op' => 'required|string',
            'value' => 'required|string'
        ]);

        $this->radiusService->connectTo($server);
        $success = $this->radiusService->updateProfile($id, $data);

        if ($success) {
            return response()->json(['message' => 'Profile updated successfully']);
        }
        return response()->json(['message' => 'Failed to update profile'], 500);
    }

    public function apiDestroyProfile(Server $server, $id)
    {
        $this->radiusService->connectTo($server);
        $success = $this->radiusService->deleteProfile($id);

        if ($success) {
            return response()->json(['message' => 'Profile deleted successfully']);
        }
        return response()->json(['message' => 'Failed to delete profile'], 500);
    }
}
